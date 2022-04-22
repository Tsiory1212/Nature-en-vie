<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


use App\Repository\SubscriptionPlanRepository;
use App\Repository\OrderRepository;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\StripeService;
use App\Entity\SubscriptionPlan;
use App\Service\Paypal\PaypalService;
use App\Service\SubscriptionService;
use App\Service\ApiService;
use App\Repository\UserRepository;
use App\Manager\StripeManager;

/**
 * @Route("/api/stripe", name="api_stripe_")
 */
class StripeController extends AbstractController
{
    
    public function __construct( ApiService $api, PaypalService $paypalService, SubscriptionPlanRepository $repoPlan, OrderRepository $repoOrder,StripeManager $stripeManager, SubscriptionService $subscriptionService, UserRepository $repoUser)
    {
        $this->repoPlan = $repoPlan;
        $this->repoOrder = $repoOrder;
        $this->paypalService = $paypalService;
        $this->subscriptionService = $subscriptionService;
        $this->api = $api;
        $this->repoUser = $repoUser;
        $this->stripeManager = $stripeManager;
    }

        /**
     * @Route("/subscription", name="subscription")
     */
    public function subscription(Request $request): Response
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
            $body =  json_decode($request->getContent(), true);
        
            $stripePriceId = $body["stripePriceId"];
            $paymentMethodId = $body["paymentMethodId"];
            $planSubscriptionName = $body["planSubscriptionName"]; 
            $planSubscriptionId = $body["planSubscriptionId"]; 
            $this->stripeManager->persistSubscriptionPlan($stripePriceId, $paymentMethodId, $planSubscriptionName, $planSubscriptionId, $user);
            
            return $this->api->success("Subscription success", null);
        }
        catch(\Exception $error){
            return $this->api->response($error->getCode(), $error->getMessage());
        }
    }
    /**
     * @Route("/checkout", name="checkout")
     */
    public function checkout(Request $request): Response
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
            $body =  json_decode($request->getContent(), true);
            $this->stripeManager->persistPayment($user, $body);

            return $this->api->success("Checkout success", null);
        }
        catch(\Exception $error){
            return $this->api->response($error->getCode(), $error->getMessage());
        }
    }
    
    /**
     * @Route("/payment_intent", name="paymentIntent", methods={"POST"})
     */
    public function paymentIntent(Request $request,  StripeService $stripeService): Response
    { 
        try{
            \Stripe\Stripe::setApiKey($stripeService->getSecretKey()); 
            $body =  json_decode($request->getContent(), true);
            $intentStripe = \Stripe\PaymentIntent::create($body);
            return $this->api->success("Payment Intent", $intentStripe);
        }
        catch(\Exception $error){
            return $this->api->response($error->getCode(), $error->getMessage());
        }
    }
    
    /**
     * @Route("/datas_after_payment", name="datas_after_payment", methods={"POST"})
     */
    public function getDatasAfterPayment(Request $request,  StripeService $stripeService)
    {
        try{
            $body =  json_decode($request->getContent(), true);
            \Stripe\Stripe::setApiKey($stripeService->getSecretKey());

            // (1) => On récupère le IntentId        
            $stripeIntentId = $body['id'];
            
            // (2) => On récupère le PaymentIntent via le IntentId        
            $payment_intent = null;
            if (isset($stripeIntentId)) {
                $payment_intent = \Stripe\PaymentIntent::retrieve($stripeIntentId);
            }

            // (3) => On gère les status du paiement        
            if ($body['status'] === 'succeeded') {
                //TODO 
            } else {
                $payment_intent->cancel();
            }
            return $this->api->success("Payment Intent Datas", $payment_intent);
        }
        catch(\Exception $error){
            return $this->api->response($error->getCode(), $error->getMessage());
        }
    }
}
