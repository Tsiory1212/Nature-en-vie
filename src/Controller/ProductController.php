<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\Panier\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
    public function product_show(Product $produit, SessionInterface $session): Response
    {
        
        $relatedProducts = $this->repoProduit->findBy(['category' => $produit->getCategory()], null, 11);
        
        $panier = $session->get('panier', []);
        if (!empty($panier)) {
           $quantity_item = $panier[$produit->getId()];
        } else {
            $quantity_item = 0;
        }
        

        return $this->render('product/single_product.html.twig', [
            'produit' => $produit,
            'relatedProducts' => $relatedProducts ,
            'quantity_item' => $quantity_item
        ]);
    }
}
