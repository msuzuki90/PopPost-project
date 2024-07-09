<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        $cartTotal = 0;

        // Calculate cart total
        if (!is_null($session->get('cart')) && count($session->get('cart')) > 0) {
            foreach ($session->get('cart')['id'] as $key => $productId) {
                $cartTotal += floatval($session->get('cart')['price'][$key]) * $session->get('cart')['stock'][$key];
            }
        }

        return $this->render('cart/index.html.twig', [
            'cartItems' => $session->get('cart'),
            'cartTotal' => $cartTotal,
        ]);
    }

    #[Route('/cart/{idProduct}', name: 'app_cart_add', methods: ['POST'])]
    public function addProduct(Request $request, ProductRepository $productRepository, int $idProduct): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart');

        // Validate quantity
        $quantity = $request->request->getInt('quantity');
        $product = $productRepository->find($idProduct);

        if ($quantity <= 0 || $quantity > $product->getStock()) {
            $this->addFlash('error', 'Quantité invalide. Ce produit est disponible en ' . $product->getStock() . ' exemplaires maximum.');
            return $this->redirectToRoute('app_product_show', ['id' => $idProduct]);
        }

        // Add product to cart
        $cart['id'][] = $product->getId();
        $cart['name'][] = $product->getName();
        $cart['text'][] = $product->getText();
        $cart['picture'][] = $product->getPicture();
        $cart['price'][] = $product->getPrice();
        $cart['priceIdStripe'][] = $product->getPriceIdStripe();
        $cart['stock'][] = $quantity;

        $session->set('cart', $cart);

        // Calculate cart total
        $cartTotal = 0;
        foreach ($cart['id'] as $key => $productId) {
            $cartTotal += floatval($cart['price'][$key]) * $cart['stock'][$key];
        }

        return $this->render('cart/index.html.twig', [
            'cartItems' => $session->get('cart'),
            'cartTotal' => $cartTotal,
        ]);
    }

    #[Route('/cart/delete', name: 'app_cart_delete', methods: ['GET'])]
    public function deleteCart(Request $request): Response
    {
        $session = $request->getSession();
        $session->remove('cart');

        return $this->redirectToRoute('app_cart');
    }
}
