<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\SubscriptionPlanRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;


class AdminAccountController extends AbstractController
{
    private $paginator;
    private $repoProduct;
    private $repoUser;
    private $repoPlan;

    public function __construct(PaginatorInterface $paginator, ProductRepository $repoProduct, UserRepository $repoUser, SubscriptionPlanRepository $repoPlan)
    {
        $this->paginator = $paginator;
        $this->repoProduct = $repoProduct;
        $this->repoUser = $repoUser;
        $this->repoPlan = $repoPlan;
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
        $subscriptions = $this->repoPlan->findAll();

        return $this->render('admin/dashboard_admin.html.twig', [
            'products' => $products,
            'users' => $users,
            'subscriptions' => $subscriptions,
        ]);
    }
    
}
