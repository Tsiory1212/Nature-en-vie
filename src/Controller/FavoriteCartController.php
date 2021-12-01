<?php

namespace App\Controller;

use App\Entity\FavoriteCart;
use App\Entity\Product;
use App\Repository\FavoriteCartRepository;
use App\Service\Panier\PanierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/account")
 */
class FavoriteCartController extends AbstractController
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/panier-favori/{id}", name="accout_favorite_cart_show")
     */
    public function accout_favorite_cart_show(FavoriteCart $favoriteCart, PanierService $panierService): Response
    {
        $cart = $favoriteCart->getCart();

        return $this->render('/account/panier/favorite_cart.html.twig', [
            'favoriteCart' => $favoriteCart,
            'items' => $panierService->getFullFavoriteCart($cart),
            'total' => $panierService->getTotalFavoriteCart($cart) ,
            'allQuantityItem' => $panierService->allQuantityItemInFavoriteCart($cart)
        ]);    
    }



    /**
     * @Route("/panier-favori/{fav}/add/product/{id}", name="favorite_cart_add")
     */
    public function favorite_cart_add_one(Product $product, $fav, $id, PanierService $panierService, FavoriteCartRepository $repoFavoriteCart)
    {
        $favoriteCartEntity = $repoFavoriteCart->find($fav);
        $cart = $favoriteCartEntity->getCart();
        $favoriteCartEntity->setCart( $panierService->addFC($id, $cart) );
        $this->em->persist($favoriteCartEntity);
        $this->em->flush();

        // dd($cart[$id] );

        // ici, on doit prendre $favoriteCartEntity à nouveau après flush() 
        $product_item_quantity = $favoriteCartEntity->getCart()[$id];
        $price = $product->getPrice($id);
        $product_item_total = $product_item_quantity * $price;
        $product_total = $panierService->getTotalFavoriteCart($cart);
        
        return $this->json([
            'code' => 200, 
            'message' => 'produit incrémenté',
            'quantity' => $product_item_quantity,
            'total_item' => $product_item_total,
            'total' => $product_total
        ]);
    }

    /**
     * @Route("/panier/{id}/remove", name="panier_remove")
     */
    public function panier_remove($id, PanierService $panierService )
    {
        $panierService->remove($id);

        return $this->json([
            'code' => 200, 
            'message' => 'produit supprimé',
            'allQuantityItem' => $panierService->allQuantityItem(),
            'totalPrice' => $panierService->getTotal()
        ]);
    }

        /**
     * @Route("/panier/{id}/remove/charging", name="panier_remove_charging_page")
     */
    public function panier_remove_charging_page($id, PanierService $panierService )
    {
        $panierService->remove($id);

        return $this->redirectToRoute('panier_show');
    }


    /**
     * @Route("/panier/add/one/{id}", name="panier_add_one")
     */
    public function panier_add_one($id, Product $product, PanierService $panierService)
    {
        $panierService->add($id);

        $product_item_quantity = $this->session->get('panier', [])[$id];
        $price = $product->getPrice($id);
        $product_item_total = $product_item_quantity * $price;
        $product_total = $panierService->getTotal();

        return $this->json([
            'code' => 200, 
            'message' => 'produit incrémenté',
            'quantity' => $product_item_quantity,
            'total_item' => $product_item_total,
            'total' => $product_total
        ]);

        // return $this->redirectToRoute("panier");
    }

    /**
     * @Route("/panier/add_with_quantity/product/{id}", name="panier_quantity_edit")
     */
    public function panier_quantity_edit(Request $request, $id, PanierService $panierService): Response
    {
        $quantity =  $request->query->get('quantity');
        $panierService->panier_quantity_edit($quantity, $id);

        return $this->json([
            'code' => 200, 
            // 'message' => 'produit incrémenté',
        ]);
    }

    /**
     * @Route("/panier/remove/one/{id}", name="panier_remove_one")
     */
    public function panier_remove_one($id, Product $product, PanierService $panierService)
    {
        $panierService->removeOne($id);

        $product_item_quantity = $this->session->get('panier', [])[$id];
        $price = $product->getPrice($id);
        $product_item_total = $product_item_quantity * $price;
        $product_total = $panierService->getTotal();

        return $this->json([
            'code' => 200, 
            'message' => 'produit incrémenté',
            'quantity' => $product_item_quantity,
            'total_item' => $product_item_total,
            'total' => $product_total
        ]);
    }

    
    /**
     * @Route("/panier/delete/all", name="panier_delete_all")
     */
    public function panier_delete_all(PanierService $panierService)
    {
        $panierService->deleteAll();
        return $this->redirectToRoute("panier_show");
    }

}
