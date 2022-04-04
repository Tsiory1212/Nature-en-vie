<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\DelivryRepository;
use App\Repository\FactureAbonnementRepository;
use App\Repository\FavoriteCartRepository;
use App\Repository\OrderRepository;
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
    public function dashboard(DelivryRepository $repoDelivry): Response
    {
        $user = $this->getUser();
        $maLivraison = $repoDelivry->findOneBy(['user' => $user]);
        $myPlanSubscriptions = $this->repoOrder->findBy(['user' => $user, 'payment_type' => Order::PAYMENT_TYPE[2]]);
        $mesFavoriteCarts = $this->repoFavoriteCart->findBy(['user' => $user]);
        
        return $this->render('account/dashboard.html.twig', [
            'myPlanSubscriptions' => $myPlanSubscriptions,
            'mesFavoriteCarts' => $mesFavoriteCarts,
            'maLivraison' => $maLivraison
        ]);
    }
}
