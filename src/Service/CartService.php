<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{

    private $productRepository;
    private $session;

    public function __construct(ProductRepository $productRepository, RequestStack $requestStack){
        $this->productRepository = $productRepository;
        $this->session = $requestStack->getSession();
    }


    public function getCart(){
        $cart= $this->session->get('sessionCart', []);
        $entityCart = [];

        foreach($cart as $productId){
            $item = [
                'product' => $this->productRepository->find($productId),
            ];

            $entityCart[]=$item;
        }

       // dd($entityCart);

        return $entityCart;
    }


    public function addProduct(Product $product){
        $cart = $this->session->get('sessionCart', []);
        //lala

        if(isset($cart[$product->getId()])){
            $cart[$product->getId()] = $cart[$product->getId()];
        }

        dd($cart);

        $this->session->set('sessionCart', $cart);
    }



}