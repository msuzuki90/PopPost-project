<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Entity\Payment;
use App\Repository\OrderDetailsRepository;
use App\Repository\OrderRepository;
use App\Repository\PaymentRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class PaymentController extends AbstractController
{
    #[Route('/checkout', name: 'app_stripe_checkout')]
    public function checkout(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        // je récupère ma session panier
        $productsInSession = $request->getSession()->get('cart');

        if(!empty($productsInSession)) {

            // Définir la clé secrète de Stripe
            // récupérer ma session stripe via ma clé stripe
            \Stripe\Stripe::setApiKey($this->getParameter('app.stripe_key'));
            
            // je mets tous mes produits de mon panier dans un tableau php
            $products = [];
    
            for($i = 0; $i < count($productsInSession["id"]); $i++) {
                $products[] = [
                    "price" => $productsInSession["priceIdStripe"][$i],
                    "quantity" => $productsInSession["quantity"][$i]
                ];
            }
    
            // afficher un formulaire de paiement avec une session de paiement stripe
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'currency' => 'eur',
                'line_items' => [
                    $products
                ],
                'allow_promotion_codes' => true,
                'customer_email' => $user->getEmail(),
                'mode' => 'payment',
                'success_url' => $this->generateUrl('app_stripe_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url' => $this->generateUrl('app_stripe_error', [], UrlGeneratorInterface::ABSOLUTE_URL),
                // 'client_reference_id' => 1
            ]);
    
    
            // créer un paiement en bdd
            // pour stocker les informations liées à la session de paiement stripe
            $payment = new Payment();
            $payment->setUser($this->getUser())
                ->setSessionID($session['id'])
                ->setPaymentStatus($session['payment_status'])
                ->setCreationDate(new \DateTime())
                ->setSuccessPageExpired(false)
                ->setPrice($session['amount_total'] / 100);
            $entityManager->persist($payment);
            $entityManager->flush();
    
            return $this->redirect($session->url, 303);

        } else {
            return $this->redirectToRoute('app_micro_post');
        }
    }

    #[Route('/payment/success', name: 'app_stripe_success')]
    public function success(
        Request $request,
        OrderRepository $orderRepository,
        EntityManagerInterface $entityManager,
        ProductRepository $productRepository,
        PaymentRepository $paymentRepository,
        OrderDetailsRepository $orderDetailsRepository,
        MailerInterface $mailer
    ): Response {
        // Récupération de la clé secrète de Stripe
        \Stripe\Stripe::setApiKey($this->getParameter('app.stripe_key'));

        $user = $this->getUser();
        
        // Check if user is authenticated
        if (!$user) {
            // Redirect to login page or homepage
            return $this->redirectToRoute('app_login'); // Replace with appropriate route
        }
        
        // Récupération du dernier paiement effectué par l'utilisateur
        $lastPayment = $paymentRepository->findLastPaymentByUser($user->getId());
        
        if ($lastPayment) {
            // Récupération de la session Stripe liée au dernier paiement
            $session = \Stripe\Checkout\Session::retrieve($lastPayment->getSessionID());
            
            // Vérification si la page SUCCESS a déjà été visitée et si le paiement est confirmé
            if (!$lastPayment->getSuccessPageExpired() && $session['payment_status'] == "paid") {
                // Mise à jour du statut du paiement
                $lastPayment->setPaymentStatus($session['payment_status'])
                    ->setSubscriptionId($session['subscription'])
                    ->setSuccessPageExpired(true);
                
                $entityManager->persist($lastPayment);
                
                // Calcul du total du panier
                $cart = $request->getSession()->get('cart');
                $cartTotal = 0;
                foreach ($cart["id"] as $i => $productId) {
                    $product = $productRepository->find($productId);
                    $cartTotal += (float) $product->getPrice() * $cart["quantity"][$i];
                }
                
                // Création de la commande
                $order = new Order();
                $order->setTotal($cartTotal)
                    ->setStatus('En cours')
                    ->setUser($user)
                    ->setDate(new \DateTime())
                    ->setPdf(false);
                
                $entityManager->persist($order);
                $entityManager->flush();
                
                // Création des détails de commande
                foreach ($cart["id"] as $i => $productId) {
                    $product = $productRepository->find($productId);
                    
                    $orderDetails = new OrderDetails();
                    $orderDetails->setIdOrder($order)
                        ->setProduct($product)
                        ->setQuantity($cart["quantity"][$i]);
                    
                    $entityManager->persist($orderDetails);
                    $entityManager->flush();
                }
                
                // Génération du PDF de la facture
                if (!$order->isPdf()) {
                    $pdfOptions = new Options();
                    $pdfOptions->set('defaultFont', 'Arial');
                    
                    $dompdf = new Dompdf($pdfOptions);
                    $html = $this->renderView('invoices/index.html.twig', [
                        'user' => $user,
                        'amount' => $order->getTotal(),
                        'invoiceNumber' => $order->getId(),
                        'date' => new \DateTime(),
                        'orderDetails' => $orderDetailsRepository->findBy(['id_order' => $order->getId()])
                    ]);
                    
                    $dompdf->loadHtml($html);
                    $dompdf->setPaper('A4', 'portrait');
                    $dompdf->render();
                    $finalInvoice = $dompdf->output();
                    
                    $pathInvoice = "./uploads/factures/" . $order->getId() . "_" . $user->getId() . ".pdf";
                    file_put_contents($pathInvoice, $finalInvoice);
                    
                    // Envoi de la facture par email
                    $email = (new TemplatedEmail())
                        ->from($this->getParameter('app.mailAddress'))
                        ->to($user->getEmail())
                        ->subject("Facture PopPost Market")
                        ->htmlTemplate("invoices/email.html.twig")
                        ->context([
                            'user' => $user,
                            'amount' => $order->getTotal(),
                            'invoiceNumber' => $order->getId(),
                            'date' => new \DateTime(),
                            'orderDetails' => $orderDetailsRepository->findBy(['id_order' => $order->getId()])
                        ])
                        ->attach($finalInvoice, sprintf('facture-%s-blog-afpa.pdf', $order->getId()));
                    
                    $mailer->send($email);
                    
                    $order->setPdf(true);
                    $entityManager->persist($order);
                    $entityManager->flush();
                    
                    // Vider le panier après le traitement
                    $request->getSession()->set('cart', []);
                    
                    return $this->render('payment/success.html.twig', [
                        'user' => $user,
                        'amount' => $order->getTotal(),
                        'invoiceNumber' => $order->getId(),
                        'date' => new \DateTime(),
                        'orderDetails' => $orderDetailsRepository->findBy(['id_order' => $order->getId()])
                    ]);
                }
            }
        }
        
        // Redirection vers la page d'accueil si le traitement n'est pas nécessaire
        return $this->redirectToRoute('app_micro_post');
    }

    #[Route('/payment/error', name: 'app_stripe_error')]
    public function error(): Response
    {
        return $this->render('stripe/index.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }
}
