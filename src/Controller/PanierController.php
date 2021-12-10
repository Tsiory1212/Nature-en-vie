<?php

namespace App\Controller;

use App\Entity\FavoriteCart;
use App\Entity\Product;
use App\Form\FavoriteCartType;
use App\Repository\FavoriteCartRepository;
use App\Service\Panier\PanierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/account")
 */

class PanierController extends AbstractController
{
    protected $session;
    protected $em;

    public function __construct(SessionInterface $session, EntityManagerInterface $em)
    {
        $this->session = $session;
        $this->em = $em;
    }


    /**
     * @Route("/panier", name="panier_show")
     */
    public function panier_show(PanierService $panierService, Request $request)
    {
        $paypal_client_id = $_ENV['PAYPAL_CLIENT_ID'];

        $favoriteCart = new FavoriteCart();
        $currentCart = $this->session->get('panier', []);

        $formFavCart = $this->createForm(FavoriteCartType::class, $favoriteCart);
        $formFavCart->handleRequest($request);

        if ($formFavCart->isSubmitted() && $formFavCart->isValid()) {
            $favoriteCart->setUser($this->getUser());
            $favoriteCart->setCart($currentCart);
            $this->em->persist($favoriteCart);
            $this->em->flush();
            $this->addFlash(
               'success',
               'Panier ajouté dans la lite des paniers favoris'
            );
            return $this->redirectToRoute('panier_show', ['cart' => 'saved']);;
        }

        return $this->render('/account/panier/panier.html.twig', [
            'items' => $panierService->getFullcart(),
            'total' => $panierService->getTotal(),
            'allQuantityItem' => $panierService->allQuantityItem(),
            'formFavCart' => $formFavCart->createView(),
            'paypal_client_id' => $paypal_client_id
        ]);
    }

    /**
     * @Route("/panier/add/{id}", name="panier_add")
     */
    public function panier_add(Product $product, PanierService $panierService)
    {
        $refId =  $product->getReferenceId();
        $panierService->add($refId);
        
        return $this->json([
            'code' => 200, 
            'message' => 'produit ajouté',
            'allQuantityItem' => $panierService->allQuantityItem(),
            'totalPrice' => $panierService->getTotal()
        ]);
    }

    /**
     * @Route("/panier/{id}/remove", name="panier_remove")
     */
    public function panier_remove( Product $product, PanierService $panierService )
    {
        $refId =  $product->getReferenceId();
        $panierService->remove($refId);

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
    public function panier_remove_charging_page(Product $product, PanierService $panierService )
    {
        $refId =  $product->getReferenceId();
        $panierService->remove($refId);

        return $this->redirectToRoute('panier_show');
    }


    /**
     * @Route("/panier/add/one/{id}", name="panier_add_one")
     */
    public function panier_add_one(Product $product, PanierService $panierService)
    {
        $refId =  $product->getReferenceId();
        $panierService->add($refId);

        $product_item_quantity = $this->session->get('panier', [])[$refId];
        // $price = $product->getPrice($id);
        $price = $product->getPrice();
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
    public function panier_quantity_edit(Request $request, Product $product, PanierService $panierService): Response
    {
        $refId =  $product->getReferenceId();
        $quantity =  $request->query->get('quantity');
        $panierService->panier_quantity_edit($quantity, $refId);

        return $this->json([
            'code' => 200, 
            // 'message' => 'produit incrémenté',
        ]);
    }

    /**
     * @Route("/panier/remove/one/{id}", name="panier_remove_one")
     */
    public function panier_remove_one(Product $product, PanierService $panierService)
    {
        $refId =  $product->getReferenceId();
        $panierService->removeOne($refId);

        $product_item_quantity = $this->session->get('panier', [])[$refId];
        // $price = $product->getPrice($id);
        $price = $product->getPrice();
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

    /**
     * @Route("/clear", name="clear")
     */
    public function clear(): Response
    {
         $this->session->remove("panier");
         return new Response('Clear');
    }

    /**
     * @Route("/see_cart", name="see_cart")
     */
    public function see_cart(): Response
    {
         dd($this->session->get("panier")) ;
    }

}
