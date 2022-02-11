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

    protected $repoFavoriteCart;

    public function __construct(EntityManagerInterface $em, FavoriteCartRepository $repoFavoriteCart)
    {
        $this->em = $em;
        $this->repoFavoriteCart = $repoFavoriteCart;
    }

    /**
     * @Route("/panier-favori/{id}", name="account_favorite_cart_show")
     */
    public function account_favorite_cart_show(FavoriteCart $favoriteCart, PanierService $panierService): Response
    {
        $cart = $favoriteCart->getCart();

        return $this->render('/account/panier/favorite_cart.html.twig', [
            'favoriteCart' => $favoriteCart,
            'items' => $panierService->getFullFavoriteCart($cart),
            'total' => $panierService->getTotalPriceFavoriteCart($cart) ,
            'allQuantityItem' => $panierService->allQuantityItemInFavoriteCart($cart)
        ]);    
    }



    /**
     * @Route("/panier-favori/{favCart}/add/product/{product}", name="favorite_cart_add_one")
     */
    public function favorite_cart_add_one(Product $product, $favCart, PanierService $panierService)
    {
        $refId =  $product->getReferenceId();

        $favoriteCartEntity = $this->repoFavoriteCart->find($favCart);
        $cart = $favoriteCartEntity->getCart();
        $favoriteCartEntity->setCart( $panierService->increaseItemInFavCart($refId, $cart) );
        $this->em->persist($favoriteCartEntity);
        $this->em->flush();


        // ici, on doit prendre $favoriteCartEntity à nouveau après flush() 
        $product_item_quantity = $favoriteCartEntity->getCart()[$refId];
        $total_price_item = $product_item_quantity * $product->getPrice();
        $total_price = $panierService->getTotalPriceFavoriteCart($favoriteCartEntity->getCart());
        
        return $this->json([
            'code' => 200, 
            'message' => 'produit incrémenté',
            'quantity' => $product_item_quantity,
            'total_price_item' => $total_price_item,
            'total_price' => $total_price
        ]);
    }

    /**
     * @Route("/panier-favori/{favCart}/remove/product/{product}", name="favorite_cart_remove_one")
     */
    public function favorite_cart_remove_one(Product $product, $favCart, PanierService $panierService)
    {
        $refId =  $product->getReferenceId();

        $favoriteCartEntity = $this->repoFavoriteCart->find($favCart);
        $cart = $favoriteCartEntity->getCart();
        $favoriteCartEntity->setCart( $panierService->removeOneItemInFavCart($refId, $cart) );
        $this->em->persist($favoriteCartEntity);
        $this->em->flush();


        // ici, on doit prendre $favoriteCartEntity à nouveau après flush() 
        $product_item_quantity = $favoriteCartEntity->getCart()[$refId];
        $total_price_item = $product_item_quantity * $product->getPrice();
        $total_price = $panierService->getTotalPriceFavoriteCart($favoriteCartEntity->getCart());

        return $this->json([
            'code' => 200, 
            'message' => 'produit décrementé',
            'quantity' => $product_item_quantity,
            'total_price_item' => $total_price_item,
            'total_price' => $total_price
        ]);
    }


    /**
     * @Route("/panier-favori/{favCart}/product/{product}/delete", name="favorite_cart_delete_item")
     */
    public function favorite_cart_delete_item( Product $product, $favCart, PanierService $panierService )
    {
        $refId =  $product->getReferenceId();

        $favoriteCartEntity = $this->repoFavoriteCart->find($favCart);
        $cart = $favoriteCartEntity->getCart();
       
        $favoriteCartEntity->setCart( $panierService->deleteItemInFavCart($refId, $cart) );
        $this->em->persist($favoriteCartEntity);
        $this->em->flush();

        $total_price = $panierService->getTotalPriceFavoriteCart($favoriteCartEntity->getCart());

        return $this->json([
            'code' => 200, 
            'message' => 'produit supprimé',
            'total_price' => $total_price
        ]);
    }


    /**
     * Permet de supprimer le panier favori dans le path order_step_one
     * 
     * @Route("/accout/panier_favori/{cart}/supprimer", name="favorite_cart_delete")
     */
    public function favorite_cart_delete(FavoriteCart $cart, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'. $this->getUser()->getId(), $request->get('_token'))) {
            $this->em->remove($cart);
            $this->em->flush();
        }
        return $this->redirectToRoute('account_order_step_one', ["favorite_cart" => "deleted"]);
    }

    /**
     * Permet de supprimer le panier favori dans le dashboard
     *
     * @Route("/accout/dashboard/panier_favori/{cart}/supprimer", name="dashboard_favorite_cart_delete")
     */
    public function dashboard_favorite_cart_delete(FavoriteCart $cart, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'. $this->getUser()->getId(), $request->get('_token'))) {
            $this->em->remove($cart);
            $this->em->flush();
            $this->addFlash(
               'danger',
               'Panier favori supprimé'
            );
        }
        return $this->redirectToRoute('dashboard', ["favorite_cart" => "deleted"]);
    }

}
