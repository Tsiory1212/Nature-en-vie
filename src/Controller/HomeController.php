<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class HomeController extends AbstractController
{
    protected $paginator;
    protected $repoProduct;
    protected $repoCategory;

    public function __construct(PaginatorInterface $paginator, ProductRepository $repoProduct, CategoryRepository $repoCategory)
    {
        $this->paginator = $paginator;
        $this->repoProduct = $repoProduct;
        $this->repoCategory = $repoCategory;
    }

    
    /**
     * @Route("/", name="home")
     *
     * @return Response
     */
    public function home(): Response
    {
        $produits = $this->repoProduct->findAll();
        $categories = $this->repoCategory->findAll();

        return $this->render('home/home.html.twig', [
            'produits' => $produits,
            'categories' => $categories
        ]);
    }
}
