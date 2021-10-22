<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class HomeController extends AbstractController
{
    protected $paginator;
    protected $repoProduct;

    public function __construct(PaginatorInterface $paginator, ProductRepository $repoProduct)
    {
        $this->paginator = $paginator;
        $this->repoProduct = $repoProduct;
    }
    
    /**
     * @Route("/", name="home")
     *
     * @return Response
     */
    public function index(): Response
    {
        $produits = $this->repoProduct->findAll();

        return $this->render('home/home.html.twig', [
            'produits' => $produits
        ]);
    }
}
