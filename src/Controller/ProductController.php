<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\QrCodeGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/admin/products', name: 'app_product_index')]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/admin/product/new', name: 'app_product_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, QrCodeGenerator $qrCodeGenerator): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {

            $qrcode = $qrCodeGenerator->createQrCode($product->getName());
            $product->setQrcode($qrcode);

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/admin/product/{id}', name: 'app_product_show')]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/product/{id}', name: 'one_product', methods: ['GET'])]
    public function getProductById(Product $product): Response
    {
        return $this->json($product, 200, [], ['groups' => 'oneProduct']);
    }

    #[Route('/admin/product/{id}/edit', name: 'app_product_edit')]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager, QrCodeGenerator $qrCodeGenerator): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $qrcode = $qrCodeGenerator->createQrCode($product->getId());
            $product->setQrcode($qrcode);

            $entityManager->flush();

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/admin/product/{id}', name: 'app_product_delete')]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/api/findby/qrcode/{id}', name: 'match_qrcode_toproduct', methods: ['GET'])]
    public function matchQrCodeToProduct(
        ProductRepository $productRepository,
        Product $product): Response
    {
        $matchingProduct = $productRepository->findOneBy(['id' => $product->getId()]);
        return $this->json($matchingProduct, 200, [], ['groups' => ['getProductWithQrCode']]);
    }


}
