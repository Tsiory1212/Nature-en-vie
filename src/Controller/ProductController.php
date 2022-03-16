<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\SearchEntity\ProductSearch;
use App\Form\SearchForm\ProductSearchType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\Panier\PanierService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    protected $repoProduct;
    protected $repoCategory;
    protected $paginator;

    public function __construct(ProductRepository $repoProduct, CategoryRepository $repoCategory, PaginatorInterface $paginator)
    {
        $this->repoProduct = $repoProduct;
        $this->repoCategory = $repoCategory;
        $this->paginator = $paginator;
    }

     /**
     * @Route("/account/all-products", name="all_products")
     */
    public function all_products(Request $request, PanierService $panierService): Response
    {
        $categories_in_navbar = $this->repoCategory->findAll();


        $search = new ProductSearch();
        $form = $this->createForm(ProductSearchType::class, $search)
            ->remove('gamme')
        ;
        $form->handleRequest($request);

        $products = $this->paginator->paginate(
            $this->repoProduct->findAllVisibleQuery($search),
            $request->query->getInt('page', 1),
            30
        );

        return $this->render('home/all_products.html.twig', [
            'products'=> $products,
            'form' => $form->createView(),
            'items' => $panierService->getFullcart(),
            'total' => $panierService->getTotal(),
            'allQuantityItem' => $panierService->allQuantityItem(),
            'categories_in_navbar' => $categories_in_navbar,

        ]);
    }
    
    /**
     * @Route("/product/{id}/show/{slug}", name="product_show", requirements={"slug": "[a-z0-9\-]*"})
     */
    public function product_show(Product $currentProduct, string $slug, SessionInterface $session, PanierService $panierService): Response
    {

        if ($currentProduct->getSlug() !== $slug) {
            return $this->redirectToRoute('product_show', [
                'id' => $currentProduct->getId(),
                'slug' => $currentProduct->getSlug()
            ], 301);
        }

        $relatedProducts = $this->repoProduct->findBy(['category' => $currentProduct->getCategory()], null, 11);
        $panier = $session->get('panier', []);
        $quantity_item = 0;
        $categories_in_navbar = $this->repoCategory->findAll();

        // On prend la quantité du produit dans le panier si le produit existe dans le panier
        $productInCart = array_key_exists($currentProduct->getReferenceId(), $panier);
       
        if (!empty($panier)) {
            if ($productInCart) {
                $quantity_item = $panier[$currentProduct->getReferenceId()];
            }
        } 
        
        return $this->render('product/single_product.html.twig', [
            'categories_in_navbar' => $categories_in_navbar,
            'currentProduct' => $currentProduct,
            'relatedProducts' => $relatedProducts ,
            'quantity_item' => $quantity_item,
            'productInCart' => $productInCart,
            'items' => $panierService->getFullcart(),
            'allQuantityItem' => $panierService->allQuantityItem(),
            'total' => $panierService->getTotal()
        ]);
    }
}
