<?php

namespace App\Controller;

use App\Entity\SearchEntity\UserSearch;
use App\Form\SearchForm\UserSearchType;
use App\Repository\CartSubscriptionRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminUserController extends AbstractController
{

    private $em;
    private $repoProduct;
    private $repoUser;
    private $repoSubscription;
    private $paginator;
    private $repoOrder;

    public function __construct(EntityManagerInterface $em, ProductRepository $repoProduct, UserRepository $repoUser, CartSubscriptionRepository $repoSubscription, PaginatorInterface $paginator, OrderRepository $repoOrder)
    {
        $this->em = $em;
        $this->repoProduct = $repoProduct;
        $this->repoUser = $repoUser;
        $this->repoSubscription = $repoSubscription;
        $this->paginator = $paginator;
        $this->repoOrder = $repoOrder;
    }

    /**
     * @Route("/users", name="admin_user_list")
     */
    public function admin_user_list(Request $request): Response
    {
        $nbrProducts = count($this->repoProduct->findAll());
        $nbrUsers = count($this->repoUser->findAll());
        $nbrSubscriptions = count($this->repoSubscription->findBy(['active' => 1]));
        $nbrOrders = count($this->repoOrder->findAll());

        $search = new UserSearch();
        $form = $this->createForm(UserSearchType::class, $search);
        $form->handleRequest($request);

        $users = $this->paginator->paginate(
            $this->repoUser->findAllQuery($search),
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('admin/user/list_user.html.twig', [
            'users'=> $users,
            'form' => $form->createView(),
            'nbrProducts' => $nbrProducts,
            'nbrUsers' => $nbrUsers,
            'nbrOrders' => $nbrOrders,
            'nbrSubscriptions' => $nbrSubscriptions
        ]);    
    }
}
