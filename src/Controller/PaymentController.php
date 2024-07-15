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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{
    #[Route('/checkout', name: 'app_stripe_checkout')]
    public function checkout(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        // Retrieve cart session
        $productsInSession = $request->getSession()->get('cart');

        if (!empty($productsInSession)) {
            // Set Stripe secret key
            \Stripe\Stripe::setApiKey($this->getParameter('app.stripe_key'));

            // Prepare products for Stripe checkout
            $products = [];
            for ($i = 0; $i < count($productsInSession["id"]); $i++) {
                $products[] = [
                    "price" => $productsInSession["priceIdStripe"][$i],
                    "quantity" => $productsInSession["quantity"][$i]
                ];
            }

            // Create Stripe checkout session
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'currency' => 'eur',
                'line_items' => [$products],
                'allow_promotion_codes' => true,
                'customer_email' => $user->getEmail(),
                'mode' => 'payment',
                'success_url' => $this->generateUrl('app_stripe_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url' => $this->generateUrl('app_stripe_error', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);

            // Create and persist payment entity
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
        \Stripe\Stripe::setApiKey($this->getParameter('app.stripe_key'));
    
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
    
        $lastPayment = $paymentRepository->findLastPaymentByUser($user->getId());
        if ($lastPayment) {
            $session = \Stripe\Checkout\Session::retrieve($lastPayment->getSessionID());
            if (!$lastPayment->getSuccessPageExpired() && $session['payment_status'] == "paid") {
                $lastPayment->setPaymentStatus($session['payment_status'])
                    ->setSubscriptionId($session['subscription'])
                    ->setSuccessPageExpired(true);
    
                $entityManager->persist($lastPayment);
    
                $cart = $request->getSession()->get('cart');
                $cartTotal = 0;
                foreach ($cart["id"] as $i => $productId) {
                    $product = $productRepository->find($productId);
                    $cartTotal += (float) $product->getPrice() * $cart["quantity"][$i];
                }
    
                $order = new Order();
                $order->setTotal($cartTotal)
                    ->setStatus('En cours')
                    ->setUser($user)
                    ->setDate(new \DateTime())
                    ->setPdf(false);
    
                $entityManager->persist($order);
                $entityManager->flush();
    
                foreach ($cart["id"] as $i => $productId) {
                    $product = $productRepository->find($productId);
    
                    $orderDetails = new OrderDetails();
                    $orderDetails->setIdOrder($order)
                        ->setProduct($product)
                        ->setQuantity($cart["quantity"][$i]);
    
                    $entityManager->persist($orderDetails);
                    $entityManager->flush();
                }
    
                // Generate PDF only if not already generated
                if (!$order->isPdf()) {
                    $pdfOptions = new Options();
                    $pdfOptions->set('defaultFont', 'Arial');
    
                    $dompdf = new Dompdf($pdfOptions);
                    $html = $this->renderView('invoices/index.html.twig', [
                        'user' => $user,
                        'amount' => $order->getTotal(),
                        'invoiceNumber' => $order->getId(),
                        'date' => new \DateTime(),
                        'orderDetails' => $orderDetailsRepository->findBy(['id_order' => $order->getId()]) // Use id_order here
                    ]);
    
                    $dompdf->loadHtml($html);
                    $dompdf->setPaper('A4', 'portrait');
                    $dompdf->render();
                    $finalInvoice = $dompdf->output();
    
                    $pathInvoice = "./uploads/factures/" . $order->getId() . "_" . $user->getId() . ".pdf";
                    file_put_contents($pathInvoice, $finalInvoice);
    
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
                            'orderDetails' => $orderDetailsRepository->findBy(['id_order' => $order->getId()]) // Use id_order here
                        ])
                        ->attach($finalInvoice, sprintf('facture-%s-blog-afpa.pdf', $order->getId()));
    
                    $mailer->send($email);
    
                    $order->setPdf(true);
                    $entityManager->persist($order);
                    $entityManager->flush();
    
                    // Clear the cart session
                    $request->getSession()->remove('cart');
    
                    // Render the invoice template
                    return $this->render('invoices/index.html.twig', [
                        'user' => $user,
                        'amount' => $order->getTotal(),
                        'invoiceNumber' => $order->getId(),
                        'date' => new \DateTime(),
                        'orderDetails' => $orderDetailsRepository->findBy(['id_order' => $order->getId()]) // Use id_order here
                    ]);
                }
            }
        }
    
        // If the payment process did not complete successfully, handle accordingly
        return $this->render('payment/success.html.twig', [
            'user' => $user,
            'amount' => null,
            'invoiceNumber' => null,
            'date' => null,
            'orderDetails' => []
        ]);
    }
    
    

    #[Route('/payment/error', name: 'app_stripe_error')]
    public function error(): Response
    {
        return $this->render('stripe/index.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }
}
