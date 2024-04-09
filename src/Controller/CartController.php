<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/cart')]
class CartController extends AbstractController
{

    #[Route('/', name: 'app_cart')]
    public function index(CartService $cartService): Response
    {

        return $this->render('cart/index.html.twig', [
            'cart'=>$cartService->getCart(),
            'total'=>$cartService->getTotal()
        ]);

        /*$response = [
            'cart' => $cartService->getCart(),
            'total' => $cartService->getTotal(),
        ];

        return $this->json($response, 200);*/
    }

    #[Route('/add/{id}/{quantity}', name: 'addtocart', methods: ['GET'])]
    #[Route('/addfromcart/{id}/{quantity}', name: 'addfromcart', methods: ['GET'])]
    public function add(Product $product, CartService $cartService, $quantity, Request $request): Response
    {
        $cartService->addProduct($product, $quantity);

        $routeName=$request->attributes->get('_route');

        $redirection= 'app_product_index';
        if($routeName === "addfromcart"){
            $redirection= 'app_cart';
        }


        return $this->redirectToRoute($redirection);
    }


    #[Route('/removerow/{id}', name: 'removerow_cart', methods: ['GET'])]
    public function removeRow(CartService $cartService, Product $product): Response
    {
        $cartService->removeRow($product);
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/removeone/{id}', name: 'removeone_cart', methods: ['GET'])]
    public function removeOne(CartService $cartService, Product $product): Response
    {
        $cartService->removeOne($product);
        return $this->redirectToRoute('app_cart');
    }
    #[Route('/empty', name: 'empty_cart', methods: ['GET'])]
    public function empty(CartService $cartService): Response
    {
        $cartService->empty();
        return $this->redirectToRoute('app_cart');
    }


}
