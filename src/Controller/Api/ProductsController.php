<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\ProductRepository;
use App\Entity\SearchEntity\ProductSearch;
use App\Entity\Category;
use App\Entity\Classement;
use App\Form\SearchForm\ProductSearchType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Service\ApiService;
/**
 * @Route("/api/products", name="api_products_")
 */
class ProductsController extends AbstractController
{
    protected $repoPlan;

    public function __construct(ApiService $api, ProductRepository $repoProduct, PaginatorInterface $paginator)
    {
        $this->repoProduct = $repoProduct;
        $this->paginator = $paginator;
        $this->api = $api;
    }

    /**
     * @Route("/", name="all", methods={"GET"})
     */
    public function findAll(): JsonResponse
    {
        try{
            $search = new ProductSearch();
            $products = $this->repoProduct->findAll();

            return $this->api->success("List of Products", $products);
        }
        catch(\Exception $error){
            return $this->api->response($error->getCode(), $error->getMessage());
        }
    }

    /**
     * @Route("/{id}", name="details", methods={"GET"})
     */
    public function findById($id): JsonResponse
    {
        try{
            $search = new ProductSearch();
            $product = $this->repoProduct->find($id);

            return $this->api->success("Details of Product", $product);
        }
        catch(\Exception $error){
            return $this->api->response($error->getCode(), $error->getMessage());
        }
    }

    /**
     * @Route("/search", name="search", methods={"GET"})
     */
    public function search(Request $request): JsonResponse
    {
        try{
            $search = new ProductSearch();
            $name = $request->query->get("name");
            $categoryName = $request->query->get("category");
            $classementRef = $request->query->get("classement");
            $maxPrice = $request->query->get("maxPrice");
            
            $search->setName($name);
            $search->setMaxPrice($maxPrice);
            if(isset($categoryName)) {
                $category = new Category();
                $category->setName($categoryName);
                $search->setCategory($category);
            }
            if(isset($classementRef)){
                $classement = new Classement();
                $classement->setRef($classementRef);
                $search->setClassement($classement);
            } 

            $products = $this->paginator->paginate(
                $this->repoProduct->findAllQuery($search, false),
                $request->query->getInt('page', 1),
                30
            );
            
            return $this->api->success("Product : Search results", $products->getItems());
        }
        catch(\Exception $error){
            return $this->api->response($error->getCode(), $error->getMessage());
        }
    }
}
