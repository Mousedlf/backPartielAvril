<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\OrderRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class OrderController extends AbstractController
{
    #[Route('/admin/orders', name: 'app_order_index')]
    public function index(OrderRepository $repo): Response
    {
        $orders = $repo->findAll();

        return $this->render('order/indexALL.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/order/{id}', name: 'app_order_show')]
    public function show(Order $order): Response
    {
        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }


    #[Route('/order/payment/methods', name: 'order_payment')]
    public function paymentMethods(): Response
    {
        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
        ]);
    }


    #[Route('/order/save', name: 'order_save')]
    public function saveOrder(
        CartService $cartService,
        EntityManagerInterface $manager,
    ): Response
    {
        $order = new Order();
        $order->setTotal($cartService->getTotal());
        $order->setByProfile($this->getUser()->getProfile());
        $order->setCreatedAt(new \DateTimeImmutable());
        $order->setPaid(true);

        foreach($cartService->getCart() as $item){
            $orderItem = new OrderItem();
            $orderItem->setQuantity($item['quantity']);
            $orderItem->setOfOrder($order);
            $orderItem->setProduct($item['product']);

            $manager->persist($orderItem);
        }


        $manager->flush();
        $cartService->empty();


        return $this->redirectToRoute('app_product_index');
    }
}
