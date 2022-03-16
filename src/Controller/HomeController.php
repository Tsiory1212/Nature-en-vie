<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\SearchEntity\ProductSearch;
use App\Form\SearchForm\ProductSearchType;
use App\Repository\BlogRepository;
use App\Repository\CartSubscriptionRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\Panier\PanierService;
use App\Service\Paypal\PaypalService;
use App\Service\Paypal\PaypalSubscription;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Sample\PayPalClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class HomeController extends AbstractController
{
    protected $paginator;
    protected $repoProduct;
    protected $repoCategory;
    protected $repoSubCart;
    protected $repoBlog;
    protected $repoCartSubscription;

    public function __construct(PaginatorInterface $paginator, ProductRepository $repoProduct, CategoryRepository $repoCategory, CartSubscriptionRepository $repoSubCart, BlogRepository $repoBlog, CartSubscriptionRepository $repoCartSubscription)
    {
        $this->paginator = $paginator;
        $this->repoProduct = $repoProduct;
        $this->repoCategory = $repoCategory;
        $this->repoSubCart = $repoSubCart;
        $this->repoBlog = $repoBlog;
        $this->repoCartSubscription = $repoCartSubscription;
    }

    

    /**
     * @Route("/", name="home")
     */
    // public function home(PanierService $panierService): Response
    // {

    //     $cat_legume = $this->repoCategory->findOneBy(['id' => 1]);
    //     $produits_legumes = $this->repoProduct->findBy(['category' => $cat_legume], null, 10, rand(0, 10));
                
    //     $cat_fruit = $this->repoCategory->findOneBy(['id' => 5]);
    //     $produits_fruits = $this->repoProduct->findBy(['category' => $cat_fruit], null, 10, rand(0, 10));

    //     $cat_epicerie = $this->repoCategory->findOneBy(['id' => 12]);
    //     $produits_epiceries = $this->repoProduct->findBy(['category' => $cat_epicerie], null, 10, rand(0, 10));

    //     $cat_boisson = $this->repoCategory->findOneBy(['id' => 6]);
    //     $produits_boissons = $this->repoProduct->findBy(['category' => $cat_boisson], null, 10, rand(0, 10));

    //     $categories_in_navbar = $this->repoCategory->findAll();

    //     return $this->render('home/home.html.twig', [
    //         'fruits' => $produits_fruits,
    //         'legumes' => $produits_legumes,
    //         'epiceries' => $produits_epiceries,
    //         'boissons' => $produits_boissons,
    //         'categories_in_navbar' => $categories_in_navbar,

    //         'items' => $panierService->getFullcart(),
    //         'total' => $panierService->getTotal(),
    //         'allQuantityItem' => $panierService->allQuantityItem(),
    //     ]);
    // }


    /**
     * @Route("/", name="home")
     *
     * @return Response
     */
    public function home_subscription(): Response
    {
        $grandPanier = $this->repoCartSubscription->findOneBy(['nameSubscriptionPlan' => 'Grand Panier', 'active' => true]);
        $moyenPanier = $this->repoCartSubscription->findOneBy(['nameSubscriptionPlan' => 'Moyen panier', 'active' => true]);
        $petitPanier = $this->repoCartSubscription->findOneBy(['nameSubscriptionPlan' => 'Petit panier', 'active' => true]);

        $fruitJuices = $this->repoProduct->findBy(['category' => '12'], ['id' => 'ASC'], 10);
        $blogs = $this->repoBlog->findBy([], ['created_at' => 'DESC'], 3);

        return $this->render('home/home_subscription.html.twig', [
            'blogs' => $blogs,
            'fruitJuices' => $fruitJuices,
            'grandPanier' => $grandPanier,
            'moyenPanier' => $moyenPanier,
            'petitPanier' => $petitPanier
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
     * @Route("/professionnel", name="professionnel")
     */
    public function professionnel(): Response
    {
        $grandPanier = $this->repoCartSubscription->findOneBy(['nameSubscriptionPlan' => 'Grand Panier']);
        $moyenPanier = $this->repoCartSubscription->findOneBy(['nameSubscriptionPlan' => 'Moyen panier']);
        $petitPanier = $this->repoCartSubscription->findOneBy(['nameSubscriptionPlan' => 'Petit panier']);

        return $this->render('home/professionnel.html.twig', [
            'grandPanier' => $grandPanier,
            'moyenPanier' => $moyenPanier,
            'petitPanier' => $petitPanier
        ]);
    }

    /**
     * @Route("/ferme/contact", name="contact")
     */
    public function contact(): Response
    {
        return $this->render('home/ferme/autres/contact.html.twig');
    }

    /**
     * @Route("/blog", name="blog")
     */
    public function blog(PaginatorInterface $paginator, Request $request): Response
    {
        $blogs = $paginator->paginate(
            $this->repoBlog->findAll(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('home/blog/blogs.html.twig', [
            'blogs' => $blogs
        ]);
    }

    /**
     * @Route("/blog/{id}/single", name="home_blog_show")
     */
    public function home_blog_show(Blog $currentblog): Response
    {
        $recentPosts = $this->repoBlog->findBy([], ['created_at' => 'DESC'], 4);
        return $this->render('home/blog/show_blog.html.twig', [
            'currentblog' => $currentblog,
            'recentPosts' => $recentPosts
        ]);
    }

    /**
     * @Route("/le-demeter", name="demeter")
     */
    public function demeter(): Response
    {
        return $this->render('home/demeter.html.twig', []);
    }

}
