<?php

namespace App\Controller;

use App\Entity\FavoriteCart;
use App\Entity\Product;
use App\Form\FavoriteCartType;
use App\Repository\FavoriteCartRepository;
use App\Service\Panier\PanierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/account/panier", name="panier_show")
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
            'total' => $panierService->getTotalPrice(),
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
        $user = $this->getUser();
        $refId =  $product->getReferenceId();
        $panierService->add($refId);
        if ($user) {
            return $this->json([
                'code' => 200, 
                'message' => 'product_added',
                'allQuantityItem' => $panierService->allQuantityItem(),
                'total_price' => $panierService->getTotalPrice()
            ]);
        }else{

            $this->addFlash(
               'danger',
               'Veuillez vous connecter'
            );

            return $this->json([
                'code' => 200, 
                'message' => 'auth_before_add_product',
                'route' => $this->generateUrl('account_order_step_one'),
                'allQuantityItem' => $panierService->allQuantityItem(),
                'total_price' => $panierService->getTotalPrice()
            ]);
        }
    }

    /**
     * @Route("/account/panier/add/one/{id}", name="panier_add_one")
     */
    public function panier_add_one(Product $product, PanierService $panierService)
    {
        $refId =  $product->getReferenceId();
        $panierService->add($refId);

        $product_item_quantity = $this->session->get('panier', [])[$refId];
        $total_price_item = $product_item_quantity * $product->getPrice();
        $total_price = $panierService->getTotalPrice();

        return $this->json([
            'code' => 200, 
            'message' => 'produit incrémenté',
            'quantity' => $product_item_quantity,
            'total_price_item' => $total_price_item,
            'total_price' => $total_price
        ]);
    }

    
    /**
     * @Route("/account/panier/remove/one/{id}", name="panier_remove_one")
     */
    public function panier_remove_one(Product $product, PanierService $panierService)
    {
        $refId =  $product->getReferenceId();
        $panierService->removeOne($refId);

        $product_item_quantity = $this->session->get('panier', [])[$refId];
        $total_price_item = $product_item_quantity * $product->getPrice();
        $total_price = $panierService->getTotalPrice();

        return $this->json([
            'code' => 200, 
            'message' => 'produit incrémenté',
            'quantity' => $product_item_quantity,
            'total_price_item' => $total_price_item,
            'total_price' => $total_price
        ]);
    }

    
    /**
     * @Route("/account/panier/{id}/delete", name="panier_delete_item")
     */
    public function panier_delete_item( Product $product, PanierService $panierService, Request $request)
    {
        $refId =  $product->getReferenceId();
        $panierService->remove($refId);
        // dd($panierService->getTotalPrice());

        return $this->json([
            'code' => 200, 
            'route' => $routeName = $request->get('_route'),
            'message' => 'produit supprimé',
            'allQuantityItem' => $panierService->allQuantityItem(),
            'total_price' => $panierService->getTotalPrice()
        ]);
    }

    /**
     * @Route("/account/panier/add_with_quantity/product/{id}", name="panier_quantity_edit")
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
     * @Route("/account/panier/delete/all", name="panier_delete_all")
     */
    public function panier_delete_all(PanierService $panierService)
    {
        $panierService->deleteAll();
        return $this->redirectToRoute("panier_show");
    }

}
