<?php
namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        $cartTotal = 0;

        // Ensure the cart is properly initialized
        if (!$session->has('cart')) {
            $session->set('cart', [
                'id' => [],
                'name' => [],
                'description' => [],
                'picture' => [],
                'price' => [],
                'priceIdStripe' => [],
                'quantity' => []
            ]);
        }

        $cart = $session->get('cart');

        // Calculate cart total
        if (count($cart['id']) > 0) {
            foreach ($cart['id'] as $key => $productId) {
                $cartTotal += floatval($cart['price'][$key]) * $cart['quantity'][$key] / 100;
            }
        }

        return $this->render('cart/index.html.twig', [
            'cartItems' => $cart,
            'cartTotal' => number_format($cartTotal, 2, ',', ' '),
        ]);
    }

    #[Route('/cart/add/{idProduct}', name: 'app_cart_add', methods: ['POST', 'GET'], requirements: ['idProduct' => '\d+'])]
    public function addProduct(Request $request, ProductRepository $productRepository, int $idProduct): Response
    {
        $session = $request->getSession();
        if (!$session->has('cart')) {
            $session->set('cart', [
                'id' => [],
                'name' => [],
                'description' => [],
                'picture' => [],
                'price' => [],
                'priceIdStripe' => [],
                'quantity' => []
            ]);
        }

        $cart = $session->get('cart');
        $product = $productRepository->find($idProduct);

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        // Check if the product is already in the cart
        $productKey = array_search($idProduct, $cart['id']);
        if ($productKey !== false) {
            // Update quantity if product is already in the cart
            $cart['quantity'][$productKey]++;
        } else {
            // Add new product to the cart
            $cart['id'][] = $product->getId();
            $cart['name'][] = $product->getName();
            $cart['description'][] = $product->getDescription();
            $cart['picture'][] = $product->getPicture();
            $cart['price'][] = $product->getPrice();
            $cart['priceIdStripe'][] = $product->getPriceIdStripe();
            $cart['quantity'][] = 1;
        }

        $session->set('cart', $cart);

        $cartTotal = 0;
        foreach ($cart['id'] as $key => $productId) {
            $cartTotal += floatval($cart['price'][$key]) * $cart['quantity'][$key] / 100;
        }

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/delete', name: 'app_cart_delete', methods: ['GET'])]
    public function deleteCart(Request $request): Response
    {
        $session = $request->getSession();
        $session->remove('cart');

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/{key}', name: 'app_cart_remove', methods: ['GET'], requirements: ['key' => '\d+'])]
    public function removeProduct(Request $request, int $key): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart');

        if (isset($cart['id'][$key])) {
            foreach ($cart as $field => $values) {
                unset($cart[$field][$key]);
                $cart[$field] = array_values($cart[$field]);
            }
            $session->set('cart', $cart);
        }

        return $this->redirectToRoute('app_cart');
    }
}
