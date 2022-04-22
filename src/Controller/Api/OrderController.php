<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

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
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
/**
 * @Route("/api/orders", name="api_orders_")
 */
class OrderController extends AbstractController
{
    public function __construct( EntityManagerInterface $em, ApiService $api, PaypalService $paypalService, DelivryRepository $repoDelivry, SubscriptionPlanRepository $repoPlan, OrderRepository $repoOrder, SubscriptionService $subscriptionService, UserRepository $repoUser)
    {
        $this->repoPlan = $repoPlan;
        $this->repoOrder = $repoOrder;
        $this->paypalService = $paypalService;
        $this->subscriptionService = $subscriptionService;
        $this->api = $api;
        $this->repoDelivry = $repoDelivry;
        $this->repoUser = $repoUser;
        $this->em = $em;

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

    
     /**
     * @Route("/", name="save", methods={"POST"})
     */
    function saveOrder(Request $request){
        try{
            $bearer = $request->headers->get('Authorization');
            $jwt_secret = $this->getParameter('jwt_secret');
            $payload = $this->api->decode($bearer, $jwt_secret);
            $user = null;
            if(isset($payload)){
                $userId = $payload->userId;
                $user = $this->repoUser->find($userId);
            }
            $body = json_decode($request->getContent(), true);
            $order = new Order();
            $paymentType = $order::PAYMENT_TYPE[0];

            $order->setUser($user);
            $order->setCart($body['cart']);
            $order->setTotalPrice($body['totalPrice']);
            $order->setUpdatedAt(new \DateTime());
            $order->setCreatedAt(new \DateTime());
            $order->setReference(uniqid('', false));
            $order->setPaymentType($paymentType);
            $order->setStripeData($body['stripeData']);

            $this->em->persist($order);
            $this->em->flush();
            return $this->api->success("Saving order successful", $order);
        }
        catch(\Exception $error){
            return $this->api->response($error->getCode(), $error->getMessage());
        }
    }

    /**
     * @Route("/", name="my_order", methods={"GET"})
     */
    public function getMyOrders(Request $request): Response
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

            $myUniqPaymentOrders = $this->repoOrder->findBy(['user' => $user, 'payment_type' => Order::PAYMENT_TYPE[0]], ['id' => 'DESC']);
            $myRecurringPaymentOrders = $this->repoOrder->findBy(['user' => $user, 'payment_type' => Order::PAYMENT_TYPE[1]], ['id' => 'DESC']);

            return $this->api->success("My orders",  [
                'myUniqPaymentOrders' => $myUniqPaymentOrders,
                'myRecurringPaymentOrders' => $myRecurringPaymentOrders
            ]);
        }
        catch(\Exception $error){
            return $this->api->response($error->getCode(), $error->getMessage());
        }
    }
}
