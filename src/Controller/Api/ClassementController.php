<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\ClassementRepository;
use App\Entity\SearchEntity\ProductSearch;
use App\Form\SearchForm\ProductSearchType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Service\ApiService;
/**
 * @Route("/api/classements", name="api_classements_")
 */
class ClassementController extends AbstractController
{
    public function __construct(ApiService $api, ClassementRepository $repoClassement)
    {
        $this->repoClassement = $repoClassement;
        $this->api = $api;
    }

    /**
     * @Route("/", name="all", methods={"GET"})
     */
    public function findAll(): JsonResponse
    {
        try{
            $classements = $this->repoClassement->findAll();

            return $this->api->success("List of Classements", $classements);
        }
        catch(\Exception $error){
            return $this->api->response($error->getCode(), $error->getMessage());
        }
    }

}
