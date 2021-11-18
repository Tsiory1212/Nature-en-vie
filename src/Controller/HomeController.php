<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\Panier\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
    public function home(PanierService $panierService): Response
    {
        $produits = $this->repoProduct->findAll();
        $categories = $this->repoCategory->findAll();

        return $this->render('home/home.html.twig', [
            'produits' => $produits,
            'categories' => $categories,
            'items' => $panierService->getFullcart(),
            'total' => $panierService->getTotal(),
            'allQuantityItem' => $panierService->allQuantityItem(),
        ]);
    }

    /**
     * @Route("/ferme/presentation-de-la-ferme", name="presentation_ferme")
     */
    public function presentation_ferme(): Response
    {
        return $this->render('home/ferme/presentation_ferme.html.twig');
    }

    /**
     * @Route("/ferme/Comment-ça-fonctionne", name="Comment_ca_fonctionne")
     */
    public function Comment_ca_fonctionne(): Response
    {
        return $this->render('home/ferme/comment_ca_fonctionne.html.twig');
    }

    /**
     * @Route("/ferme/les-produit-de-notre-boutique-bio", name="les_produit_de_notre_boutique_bio")
     */
    public function les_produit_de_notre_boutique_bio(): Response
    {
        return $this->render('home/ferme/les_produit_de_notre_boutique_bio.html.twig');
    }

    /**
     * @Route("/ferme/les-marches", name="les_marches")
     */
    public function les_marches(): Response
    {
        return $this->render('home/ferme/les_marches.html.twig');
    }

    
    /**
     * @Route("/ferme/commerce-de-gros-et-demi-gros", name="commerce_de_gros_et_demi_gros")
     */
    public function commerce_de_gros_et_demi_gros(): Response
    {
        return $this->render('home/ferme/autres/commerce_de_gros_et_demi_gros.html.twig');
    }

    /**
     * @Route("/ferme/commandes", name="commandes")
     */
    public function commandes(): Response
    {
        return $this->render('home/ferme/autres/commandes.html.twig');
    }

    /**
     * @Route("/ferme/contact", name="contact")
     */
    public function contact(): Response
    {
        return $this->render('home/ferme/autres/contact.html.twig');
    }


}
