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

    #[Route('/add/{id}', name: 'addtocart')]
    public function add(Product $product, CartService $cartService): Response
    {
        $cartService->addProduct($product);
       // dd($cartService);
        return $this->redirectToRoute('app_product_index');
    }


    #[Route('/', name: 'app_cart')]
    public function index(CartService $cartService): Response
    {
        //dd($cartService->getCart());

        return $this->render('cart/index.html.twig', [
            'cart'=>$cartService->getCart(),
            //
        ]);
    }



}
