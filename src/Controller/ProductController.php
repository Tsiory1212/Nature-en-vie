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
     * @Route("/product/{id}/show/{slug}", name="product_show", requirements={"slug": "[a-z0-9\-]*"})
     */
    public function product_show(Product $currentProduct, string $slug, SessionInterface $session): Response
    {
        if ($currentProduct->getSlug() !== $slug) {
            return $this->redirectToRoute('product_show', [
                'id' => $currentProduct->getId(),
                'slug' => $currentProduct->getSlug()
            ], 301);
        }


        $relatedProducts = $this->repoProduit->findBy(['category' => $currentProduct->getCategory()], null, 11);
        
        $panier = $session->get('panier', []);
        
        $quantity_item = 0;

        // On prend la quantité du produit dans le panier si le produit existe dans le panier
        $productInCart = array_key_exists($currentProduct->getReferenceId(), $panier);
       
        if (!empty($panier)) {
            if ($productInCart) {
                $quantity_item = $panier[$currentProduct->getReferenceId()];
            }
        } 
        
        

        return $this->render('product/single_product.html.twig', [
            'currentProduct' => $currentProduct,
            'relatedProducts' => $relatedProducts ,
            'quantity_item' => $quantity_item,
            'productInCart' => $productInCart
        ]);
    }
}
