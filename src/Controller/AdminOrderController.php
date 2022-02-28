<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\User;
use App\Repository\OrderRepository;
use App\Service\Panier\PanierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminOrderController extends AbstractController
{
    protected $repoOrder;

    protected $em;

    public function __construct(OrderRepository $repoOrder, EntityManagerInterface $em)
    {
        $this->repoOrder = $repoOrder;
        $this->em = $em;
    }

    /**
     * @Route("/admin/orders", name="admin_order_list")
     */
    public function admin_order_list(Request $request): Response
    {
        // Si le params url === '1', les utilisateurs sont filtré par le jour de la semaine
        if ($request->query->get('queryPathLine') === '0' or $request->query->get('queryPathLine') === 'null' or empty($request->query->get('queryPathLine'))) {
            $orders = $this->repoOrder->findAll();
        }else if($request->query->get('queryPathLine') === '1') {
            $orders = $this->repoOrder->findAllOrder_shortByDayslotdelivry();
        }        
        
        // On stock les utilisateurs dans un tableau sans les répétés
        $users = [];
        foreach ($orders as $order) {
            $users[] = $order->getUser();
        }
        $users = array_unique($users, SORT_REGULAR);
        $mapbox_apikey = $_ENV['MAPBOX_API_KEY'];
        return $this->render('admin/order/all_orders.html.twig', [
            'orders' => $orders,
            'users' => $users,
            'mapbox_apikey' => $mapbox_apikey
        ]);
    }

    /**
     * @Route("/admin/user/{user}/order", name="admin_user_order")
     */
    public function admin_user_order(User $user): Response
    {
        $userOrders = $this->repoOrder->findBy(['user' => $user]);
        $ordersNotDelivred = $this->repoOrder->findBy(['user' => $user, 'status' => 0]);

        return $this->render('admin/order/show_user_orders.html.twig', [
            'orders' => $userOrders,
            'user' => $user,
            'ordersNotDelivred' => $ordersNotDelivred
        ]);
    }

    /**
     * @Route("/amdin/user/{user}/order/{order}/deliver", name="admin_user_order_deliver")
     */
    public function admin_user_order_deliver(User $user, Order $order): Response
    {   
        $orderUser = $this->repoOrder->findOneBy(['user' => $user, 'id' => $order->getId()]);
        $orderUser->setStatus(1);
        $this->em->persist($orderUser);
        $this->em->flush();
        return $this->redirectToRoute('admin_user_order', ['user' => $user->getId()]);
    }


    /**
     * @Route("/admin/order/{order}/cart/show", name="admin_user_order_cart_show")
     */
    public function admin_user_order_cart_show(Order $order, PanierService $panierService): Response
    {
        $cart = $order->getCart();
        
        return $this->render('admin/order/show_cart_in_order_history.html.twig', [
            'cart' => $order->getCart(),
            'items' => $panierService->getFullFavoriteCart($cart),
            'total' => $panierService->getTotalPriceFavoriteCart($cart) ,
            'allQuantityItem' => $panierService->allQuantityItemInFavoriteCart($cart)
        ]);
    }


}
