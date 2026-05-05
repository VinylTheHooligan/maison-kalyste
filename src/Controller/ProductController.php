<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/produits', name: 'app_product_')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(
        Request $request,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
    ): Response {

        $categoryId  = $request->query->get('categorie');
        $searchQuery = $request->query->get('q');

        $products = match(true) {
            !empty($searchQuery) => $productRepository->search($searchQuery),
            !empty($categoryId)  => $productRepository->findByCategory((int) $categoryId),
            default              => $productRepository->findAllInStock(),
        };

        return $this->render('product/index.html.twig', [
            'products'        => $products,
            'categories'      => $categoryRepository->findAll(),
            'currentCategory' => $categoryId,
            'searchQuery'     => $searchQuery,
        ]);
    }

    #[Route('/{slug}', name: 'show')]
    public function show(string $slug, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findOneBy(['slug' => $slug]);

        if (!$product) {
            throw $this->createNotFoundException('Produit introuvable.');
        }

        // les produits similaires
        $related = $productRepository->findByCategory(
            $product->getCategory()->getId()
        );

        $related = array_filter(
            $related,
            fn($p) => $p->getId() !== $product->getId()
        );

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'related' => array_slice(array_values($related), 0, 3),
        ]);
    }
}