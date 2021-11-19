<?php

namespace App\Controller;

use App\Entity\ProductSearch;
use App\Form\ProductSearchType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;


class AdminAccountController extends AbstractController
{
    protected $paginator;
    protected $repoProduct;

    public function __construct(PaginatorInterface $paginator, ProductRepository $repoProduct)
    {
        $this->paginator = $paginator;
        $this->repoProduct = $repoProduct;
    }


    /**
     * Dashboard Admin
     * 
     * @Route("/admin/dashboard", name="admin_dashboard")
     */
    public function admin_dashboard(Request $request)
    {
        $search = new ProductSearch();
        $form = $this->createForm(ProductSearchType::class, $search);
        $form->handleRequest($request);

        $produits = $this->paginator->paginate(
            $this->repoProduct->findAllVisibleQuery($search),
            $request->query->getInt('page', 1),
            30
        );

        return $this->render('admin/dashboard_admin.html.twig', [
            'produits' => $produits,
            'form' => $form->createView()
        ]);
    }
    
}
