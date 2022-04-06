<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\DelivryRepository;
use App\Repository\FactureAbonnementRepository;
use App\Repository\FavoriteCartRepository;
use App\Repository\OrderRepository;
use App\Service\SubscriptionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    protected $repoOrder;
    protected $repoFavoriteCart;

    public function __construct(OrderRepository $repoOrder, FavoriteCartRepository $repoFavoriteCart)
    {
        $this->repoOrder = $repoOrder;
        $this->repoFavoriteCart = $repoFavoriteCart;
    }


    /**
     * @Route("/account/dashboard", name="dashboard")
     */
    public function dashboard(DelivryRepository $repoDelivry, SubscriptionService $subscriptionService): Response
    {
        $user = $this->getUser();
        $maLivraison = $repoDelivry->findOneBy(['user' => $user]);
        $myOrderPlanSubscriptions = $this->repoOrder->findBy(['user' => $user, 'payment_type' => Order::PAYMENT_TYPE[2]]);
        $myOrderPlanSubscriptionsActive = $subscriptionService->getActiveOrderPlanSubscription($myOrderPlanSubscriptions);
        $mesFavoriteCarts = $this->repoFavoriteCart->findBy(['user' => $user]);
        
        return $this->render('account/dashboard.html.twig', [
            'myOrderPlanSubscriptions' => $myOrderPlanSubscriptionsActive,
            'mesFavoriteCarts' => $mesFavoriteCarts,
            'maLivraison' => $maLivraison
        ]);
    }
}
