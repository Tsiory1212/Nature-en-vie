<?php

namespace App\Controller;

use App\Entity\ProductSearch;
use App\Form\ProductSearchType;
use App\Repository\CartSubscriptionRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;


class AdminAccountController extends AbstractController
{
    private $paginator;
    private $repoProduct;
    private $repoUser;
    private $repoSubscription;

    public function __construct(PaginatorInterface $paginator, ProductRepository $repoProduct, UserRepository $repoUser, CartSubscriptionRepository $repoSubscription)
    {
        $this->paginator = $paginator;
        $this->repoProduct = $repoProduct;
        $this->repoUser = $repoUser;
        $this->repoSubscription = $repoSubscription;
    }


    /**
     * Dashboard Admin
     * 
     * @Route("/admin/dashboard", name="admin_dashboard")
     */
    public function admin_dashboard(Request $request)
    {
        $products = $this->repoProduct->findAll();
        $users = $this->repoUser->findAll();
        $subscriptions = $this->repoSubscription->findAll();

        return $this->render('admin/dashboard_admin.html.twig', [
            'products' => $products,
            'users' => $users,
            'subscriptions' => $subscriptions,
        ]);
    }
    
}
