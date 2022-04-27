<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\DelivryRepository;
use App\Repository\UserRepository;

use App\Entity\SearchEntity\ProductSearch;
use App\Form\SearchForm\ProductSearchType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Service\ApiService;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Delivry;
/**
 * @Route("/api/delivery", name="api_delivery_")
 */
class DeliveryController extends AbstractController
{
    public function __construct(EntityManagerInterface $em,ApiService $api, DelivryRepository $repoDelivery, UserRepository $repoUser)
    {
        $this->repoDelivery = $repoDelivery;
        $this->repoUser = $repoUser;
        $this->em = $em;
        $this->api = $api;
    }

    /**
     * @Route("/", name="all", methods={"GET"})
     */
    public function findAll(): JsonResponse
    {
        try{
            $delivery = $this->repoDelivery->findAll();

            return $this->api->success("List of Delivery", $delivery);
        }
        catch(\Exception $error){
            return $this->api->response($error->getCode(), $error->getMessage());
        }
    }
    
 /**
     * @Route("/my_delivery_infos", name="my_delivery_infos", methods={"GET"})
     */
    public function findMyDeliveryInfo(Request $request): JsonResponse
    {  
        try{
            $bearer = $request->headers->get('Authorization');
            $jwt_secret = $this->getParameter('jwt_secret');
            $payload = $this->api->decode($bearer, $jwt_secret);
            $user = null;
            $myDeliveryInfo = null;
            if(isset($payload)){
                $userId = $payload->userId;
                $user = $this->repoUser->find($userId);
                $myDeliveryInfo = $this->repoDelivery->findOneBy(['user' => $user]);
            }
            
            return $this->api->success("My delivery info", $myDeliveryInfo);
        }
        catch(\Exception $error){
            return $this->api->response($error->getCode(), $error->getMessage());
        }
    }

    /**
     * @Route("/", name="save", methods={"POST"})
     */
    public function save(Request $request): JsonResponse
    {  
        try{
            $body =  json_decode($request->getContent(), true);
            if(!isset($body['address']) || 
                !isset($body['postal_code']) || 
                !isset($body['lat_position']) || 
                !isset($body['lng_position']) || 
                !isset($body['type']) || 
                !isset($body['time_slot']) || 
                !isset($body['day_slot'])) throw new \Exception("Veuillez remplir les informations.");

            $bearer = $request->headers->get('Authorization');
            $jwt_secret = $this->getParameter('jwt_secret');
            $payload = $this->api->decode($bearer, $jwt_secret);
            $user = null;

            $delivry = new Delivry();
            $delivry->setAddress($body['address']);
            $delivry->setPostalCode($body['postal_code']);
            $delivry->setLatPosition($body['lat_position']);
            $delivry->setLngPosition($body['lng_position']);
            $delivry->setType($body['type']);
            $delivry->setTimeSlot($body['time_slot']);
            $delivry->setDaySlot($body['day_slot']);


            if(isset($payload)){
                $userId = $payload->userId;
                $user = $this->repoUser->find($userId);

                $delivry->setUser($user);                
                $this->em->persist($delivry);
                $this->em->flush();
            }
            else  throw new \Exception("Utilisateur invalide.");
            
            return $this->api->success("Saving deliveryInfo successful", $delivry);
        }
        catch(\Exception $error){
            return $this->api->response($error->getCode(), $error->getMessage());
        }
    }

    /**
     * @Route("/", name="update", methods={"PUT"})
     */
    public function update(Request $request): JsonResponse
    {  
        try{
            $body =  json_decode($request->getContent(), true);
            if(!isset($body['address']) || 
                !isset($body['postal_code']) || 
                !isset($body['lat_position']) || 
                !isset($body['lng_position']) || 
                !isset($body['type']) || 
                !isset($body['time_slot']) || 
                !isset($body['day_slot'])) throw new \Exception("Veuillez remplir les informations.");

            $bearer = $request->headers->get('Authorization');
            $jwt_secret = $this->getParameter('jwt_secret');
            $payload = $this->api->decode($bearer, $jwt_secret);
            $user = null;


            if(isset($payload)){
                $userId = $payload->userId;
                $user = $this->repoUser->find($userId);
                $delivry = $this->repoDelivery->findOneBy(['user' => $user]);
                if($delivry == null)   throw new \Exception("Vous n'avez pas encore configurer votre adresse de livraison");
                $delivry->setAddress($body['address']);
                $delivry->setPostalCode($body['postal_code']);
                $delivry->setLatPosition($body['lat_position']);
                $delivry->setLngPosition($body['lng_position']);
                $delivry->setType($body['type']);
                $delivry->setTimeSlot($body['time_slot']);
                $delivry->setDaySlot($body['day_slot']);

                $delivry->setUser($user);                
                $this->em->persist($delivry);
                $this->em->flush();
            }
            else  throw new \Exception("Utilisateur invalide.");
            
            return $this->api->success("Saving deliveryInfo successful", $delivry);
        }
        catch(\Exception $error){
            return $this->api->response($error->getCode(), $error->getMessage());
        }
    }
}
