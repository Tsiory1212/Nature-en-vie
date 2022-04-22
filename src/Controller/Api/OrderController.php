<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SubscriptionPlanRepository;
use App\Repository\OrderRepository;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\StripeService;
use App\Entity\SubscriptionPlan;
use App\Service\Paypal\PaypalService;
use App\Service\SubscriptionService;
use App\Service\ApiService;
use App\Repository\DelivryRepository;
use App\Repository\UserRepository;

/**
 * @Route("/api/orders", name="api_orders_")
 */
class OrderController extends AbstractController
{
    public function __construct( ApiService $api, PaypalService $paypalService, DelivryRepository $repoDelivry, SubscriptionPlanRepository $repoPlan, OrderRepository $repoOrder, SubscriptionService $subscriptionService, UserRepository $repoUser)
    {
        $this->repoPlan = $repoPlan;
        $this->repoOrder = $repoOrder;
        $this->paypalService = $paypalService;
        $this->subscriptionService = $subscriptionService;
        $this->api = $api;
        $this->repoDelivry = $repoDelivry;
        $this->repoUser = $repoUser;

    }
    /**
     * @Route("/delivery_infos", name="delivery_infos", methods={"GET"})
     */
    public function findDeliveryInfo(): JsonResponse
    {
        try{
            $bearer = $request->headers->get('Authorization');
            $jwt_secret = $this->getParameter('jwt_secret');
            $payload = $this->api->decode($bearer, $jwt_secret);
            $user = null;
            if(isset($payload)){
                $userId = $payload->userId;
                $user = $this->repoUser->find($userId);
            }
            
            $myDelivryInfo = $this->repoDelivry->findOneBy(['user' => $user]);
            return $this->api->success("Delivery info", $myDelivryInfo);
        }
        catch(\Exception $error){
            return $this->api->response($error->getCode(), $error->getMessage());
        }
    }
}
