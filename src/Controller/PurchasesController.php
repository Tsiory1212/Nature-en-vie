<?php

namespace App\Controller;

use App\Entity\Delivry;
use App\Entity\FavoriteCart;
use App\Entity\Product;
use App\Entity\Purchases;
use App\Form\FavoriteCartType;
use App\Form\OrderDelivryType;
use App\Form\PurchasesType;
use App\Repository\DelivryRepository;
use App\Repository\FavoriteCartRepository;
use App\Repository\ProductRepository;
use App\Repository\PurchasesRepository;
use App\Service\Panier\PanierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Uuid;

class PurchasesController extends AbstractController
{

    protected $session;
    protected $em;
    protected $repoPurchases;
    protected $repoDelivry;

    public function __construct(SessionInterface $session, EntityManagerInterface $em, PurchasesRepository $repoPurchases, DelivryRepository $repoDelivry)
    {
        $this->session = $session;
        $this->em = $em;
        $this->repoPurchases = $repoPurchases;
        $this->repoDelivry = $repoDelivry;
    }

    
    /**
     * @Route("/produits/commande", name="account_pass_to_order")
     */
    public function account_pass_to_order(PanierService $panierService, Request $request, FavoriteCartRepository $repoFavCart): Response
    {
        $paypal_client_id = $_ENV['PAYPAL_CLIENT_ID'];

        $user = $this->getUser();

        $myDelivryInfo = $this->repoDelivry->findOneBy(['user' => $user]);
        $myLastFavCart = $repoFavCart->findBy(['user' =>  $user], ['id'=>'DESC'],1,0)['0'];
        $itemsInTab = $panierService->putItemsIntoArray();

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
            return $this->redirectToRoute('account_pass_to_order', ['cart' => 'saved']);;
        }

        return $this->render('purchases/order.html.twig', [
            'items' => $panierService->getFullcart(),
            'total' => $panierService->getTotal(),
            'allQuantityItem' => $panierService->allQuantityItem(),
            'myDelivryInfo' => $myDelivryInfo,
            'myLastFavCart' => $myLastFavCart,
            'itemsInTab' => $itemsInTab,
            'formFavCart' => $formFavCart->createView(),
            'paypal_client_id' => $paypal_client_id,
        ]);
    }

    /**
     * @Route("/account/order/step/one", name="account_order_step_one")
     */
    public function account_order_step_one(PanierService $panierService): Response
    {
        return $this->render('purchases/order_step/order_step_one.html.twig', [
            'items' => $panierService->getFullcart(),
            'total' => $panierService->getTotal(),
        ]);
    }

    /**
     * @Route("/account/order/step/two", name="account_order_step_two")
     */
    public function account_order_step_two(): Response
    {
        $user = $this->getUser();
        $myDelivryInfo = $this->repoDelivry->findOneBy(['user' => $user]);

        return $this->render('purchases/order_step/order_step_two.html.twig', [
            'myDelivryInfo' => $myDelivryInfo
        ]);
    }

    /**
     * @Route("/account/order/step/three", name="account_order_step_three")
     */
    public function account_order_step_three(PanierService $panierService): Response
    {
        $paypal_client_id = $_ENV['PAYPAL_CLIENT_ID'];


        return $this->render('purchases/order_step/order_step_three.html.twig', [
            'items' => $panierService->getFullcart(),
            'paypal_client_id' => $paypal_client_id,
            'total' => $panierService->getTotal(),
        ]);
    }
    
    /**
     * @Route("/account/order/show", name="account_order_show")
     */
    public function account_order_show(): Response
    {
        $user = $this->getUser();
        $myOrders = $this->repoPurchases->findBy(['user' => $user]);

        return $this->render('purchases/show_my_order.html.twig', [
            'myOrders' => $myOrders
        ]);
    }

    /**
     * @Route("/account/order/cart/{id}/show", name="account_order_cart_show")
     */
    public function account_order_cart_show(): Response
    {
        $myCart = $this->repoPurchases->findBy(['user' => $this->getUser()]);
        return $this->render('$0.html.twig', []);
    }


    /**
     * @Route("/account/delivry/create", name="account_delivry_create")
     */
    public function account_delivry_create(Request $request): Response
    {

        $delivry = new Delivry();
        $formDelivry = $this->createForm(OrderDelivryType::class, $delivry);
        $formDelivry->handleRequest($request);

        if ($formDelivry->isSubmitted() && $formDelivry->isValid()) {
            $delivry->setUser($this->getUser());

            $this->em->persist($delivry);
            $this->em->flush();

            if ( $this->session->get('panier')) {
                return $this->redirectToRoute('account_pass_to_order');
            } else {
                return $this->redirectToRoute('dashboard');
            }
            
        }
        return $this->render('account/delivry/create_delivry.html.twig', [
            'formDelivry' => $formDelivry->createView()
        ]);
    }

    /**
     * @Route("/account/delivry/edit", name="account_delivry_edit")
     */
    public function account_delivry_edit(Request $request): Response
    {
      
        $user = $this->getUser();
        $delivry = $user->getDelivry();

        $formDelivry = $this->createForm(OrderDelivryType::class, $delivry);
        $formDelivry->handleRequest($request);

        if ($formDelivry->isSubmitted() && $formDelivry->isValid()) {

            $this->em->persist($delivry);
            $this->em->flush();

            return $this->redirectToRoute('dashboard');
           
            
        }
        return $this->render('account/delivry/create_delivry.html.twig', [
            'formDelivry' => $formDelivry->createView()
        ]);
    }



}
