<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\CategoryRepository;
use App\Entity\SearchEntity\ProductSearch;
use App\Entity\Category;
use App\Entity\Classement;
use App\Form\SearchForm\ProductSearchType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Service\ApiService;
/**
 * @Route("/api/categories", name="api_categories_")
 */
class CategoryController extends AbstractController
{
    public function __construct(ApiService $api, CategoryRepository $repoCategory)
    {
        $this->repoCategory = $repoCategory;
        $this->api = $api;
    }

    /**
     * @Route("/", name="all", methods={"GET"})
     */
    public function findAll(): JsonResponse
    {
        try{
            $category = $this->repoCategory->findAll();

            return $this->api->success("List of Categories", $category);
        }
        catch(\Exception $error){
            return $this->api->response($error->getCode(), $error->getMessage());
        }
    }

}
