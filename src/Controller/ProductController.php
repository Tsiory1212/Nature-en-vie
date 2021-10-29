<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    protected $repoProduit;

    public function __construct(ProductRepository $repoProduit)
    {
        $this->repoProduit = $repoProduit;
    }

    /**
     * @Route("/product/{id}/show", name="product_show")
     */
    public function product_show(Product $produit): Response
    {
        $relatedProducts = $this->repoProduit->findBy(['category' => $produit->getCategory()]);
        return $this->render('product/single_product.html.twig', [
            'produit' => $produit,
            'relatedProducts' => $relatedProducts 
        ]);
    }
}
