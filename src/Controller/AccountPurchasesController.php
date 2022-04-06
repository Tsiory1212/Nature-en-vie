<?php

namespace App\Controller;


use App\Entity\FavoriteCart;
use App\Entity\Order;
use App\Form\FavoriteCartType;
use App\Repository\DelivryRepository;
use App\Repository\FavoriteCartRepository;
use App\Repository\OrderRepository;
use App\Service\Panier\PanierService;
use App\Service\Paypal\PaypalService;
use App\Service\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


class AccountPurchasesController extends AbstractController
{

    protected $session;
    protected $em;
    protected $repoOrder;
    protected $repoDelivry;
    protected $repoFavCart;
    protected $paypalService;

    public function __construct(SessionInterface $session, EntityManagerInterface $em, OrderRepository $repoOrder, DelivryRepository $repoDelivry, FavoriteCartRepository $repoFavCart,PaypalService $paypalService)
    {
        $this->session = $session;
        $this->em = $em;
        $this->repoOrder = $repoOrder;
        $this->repoDelivry = $repoDelivry;
        $this->repoFavCart = $repoFavCart;
        $this->paypalService = $paypalService;
    }

    
    /**
     * @Route("/produits/commande", name="account_pass_to_order")
     */
    public function account_pass_to_order(PanierService $panierService, Request $request): Response
    {
        $paypal_client_id = $this->paypalService->clientId;

        $user = $this->getUser();

        $myDelivryInfo = $this->repoDelivry->findOneBy(['user' => $user]);
        $myLastFavCart = $this->repoFavCart->findBy(['user' =>  $user], ['id'=>'DESC'],1,0);
        if (empty($myLastFavCart)) {
            $myLastFavCart = $myLastFavCart;
        } else {
            $myLastFavCart = $this->repoFavCart->findBy(['user' =>  $user], ['id'=>'DESC'],1,0)['0'];
        }
        
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
            return $this->redirectToRoute('account_pass_to_order', ['cart' => 'saved']);;
        }

        return $this->render('purchases/order.html.twig', [
            'items' => $panierService->getFullcart(),
            'total' => $panierService->getTotalPrice(),
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
    public function account_order_step_one(PanierService $panierService, Request $request, StripeService $stripeService): Response
    {
        $user = $this->getUser();
        $myDelivryInfo = $this->repoDelivry->findOneBy(['user' => $user]);
        $myLastFavCart = $this->repoFavCart->findBy(['user' =>  $user], ['id'=>'DESC'],1,0);
        if (empty($myLastFavCart)) {
            $myLastFavCart = $myLastFavCart;
        } else {
            $myLastFavCart = $this->repoFavCart->findBy(['user' =>  $user], ['id'=>'DESC'],1,0)['0'];
        }
        $itemsInTab = $panierService->putItemsIntoArray();

        // Gestion modal : Favorite cart 
        $favoriteCart = new FavoriteCart();
        $currentCart = $this->session->get('panier', []);
        $formFavCart = $this->createForm(FavoriteCartType::class, $favoriteCart);
        $formFavCart->handleRequest($request);
        if ($formFavCart->isSubmitted() && $formFavCart->isValid()) {
            $favoriteCart->setUser($this->getUser());
            $favoriteCart->setCart($currentCart);
            $this->em->persist($favoriteCart);
            $this->em->flush();
            return $this->redirectToRoute('account_order_step_one', ['cart' => 'saved']);;
        }

        return $this->render('purchases/order_step/order_step_one.html.twig', [
            'items' => $panierService->getFullcart(),
            'total' => $panierService->getTotalPrice(),
            'allQuantityItem' => $panierService->allQuantityItem(),
            'myDelivryInfo' => $myDelivryInfo,
            'myLastFavCart' => $myLastFavCart,
            'itemsInTab' => $itemsInTab,
            'formFavCart' => $formFavCart->createView(),
        ]);
    }


    /**
     * @Route("/account/order/step/two", name="account_order_step_two")
     */
    public function account_order_step_two(): Response
    {
        if (!$this->session->get('panier') || count($this->session->get('panier')) < 1) {
           return $this->redirectToRoute("account_order_step_one", ['error' => 'empty_cart']);
        }

        // lorsque l'étape 2 est visité, on stocke une variable dans la session pour dire qu'il est déjà passé
        $delivry = $this->session->get('delivry', []);
        $delivry['delivry_passed'] = 1;
        $this->session->set('delivry', $delivry);
        
        $user = $this->getUser();
        $myDelivryInfo = $this->repoDelivry->findOneBy(['user' => $user]);

        return $this->render('purchases/order_step/order_step_two.html.twig', [
            'myDelivryInfo' => $myDelivryInfo
        ]);
    }

    /**
     * @Route("/account/order/step/three", name="account_order_step_three")
     */
    public function account_order_step_three(PanierService $panierService, StripeService $stripeService): Response
    {

        /**
         * @var User $user
         */
        $user = $this->getUser();

        if ($this->session->get('panier') == null){
            return  $this->redirectToRoute("account_order_step_one", ['error' => 'empty_cart']);
        }else if ($user->getDelivry() == null) {
            return  $this->redirectToRoute("account_order_step_two", ['error' => 'empty_delivry']);
        }

        $intentSecret = $stripeService->intentSecret();

        return $this->render('purchases/order_step/order_step_three.html.twig', [
            'items' => $panierService->getFullcart(),
            'total' => $panierService->getTotalPrice(),
            'intentSecret' => $intentSecret,

        ]);
    }
    
    /**
     * @Route("/account/order/show", name="account_order_show")
     */
    public function account_order_show(): Response
    {
        $user = $this->getUser();
        $myUniqPaymentOrders = $this->repoOrder->findBy(['user' => $user, 'payment_type' => Order::PAYMENT_TYPE[0]], ['id' => 'DESC']);
        $myRecurringPaymentOrders = $this->repoOrder->findBy(['user' => $user, 'payment_type' => Order::PAYMENT_TYPE[1]], ['id' => 'DESC']);

        return $this->render('purchases/show_my_orders.html.twig', [
            'myUniqPaymentOrders' => $myUniqPaymentOrders,
            'myRecurringPaymentOrders' => $myRecurringPaymentOrders
        ]);
    }

    /**
     * @Route("/account/order/{order}/cart/show", name="account_order_cart_show")
     */
    public function account_order_cart_show(Order $order, PanierService $panierService): Response
    {
        $cart = $order->getCart()[0];
        
        return $this->render('purchases/show_cart_in_order_history.html.twig', [
            'cart' => $order->getCart(),
            'items' => $panierService->getFullFavoriteCart($cart),
            'total' => $panierService->getTotalPriceFavoriteCart($cart) ,
            'allQuantityItem' => $panierService->allQuantityItemInFavoriteCart($cart)
        ]);
    }

    /**
     * @Route("/account/order/{order}/recurring/show", name="account_order_reccuring_show")
     */
    public function account_order_reccuring_show(Order $order, PanierService $panierService): Response
    {
        $intervalUnitSubscription = Order::INTERVAL_UNIT[$order->getStripeData()['stripe_subscription_interval']] ;

        return $this->render('/account/subscription/recurring_payment/show_subscription_detail.html.twig', [
            'order' => $order,
            'intervalUnitSubscription' => $intervalUnitSubscription
        ]);
    }

}
