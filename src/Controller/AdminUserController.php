<?php

namespace App\Controller;

use App\Entity\SearchEntity\UserSearch;
use App\Form\SearchForm\UserSearchType;
use App\Repository\UserRepository;
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
    protected $paginator;
    protected $repoUser;

    public function __construct(PaginatorInterface $paginator, UserRepository $repoUser)
    {
        $this->paginator = $paginator;
        $this->repoUser = $repoUser;
    }

    /**
     * @Route("/users", name="admin_user_list")
     */
    public function admin_user_list(Request $request): Response
    {
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
            'form' => $form->createView()
        ]);    }
}
