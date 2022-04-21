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
/**
 * @Route("/api/plans", name="api_plans_")
 */
class PlansController extends AbstractController
{
    protected $repoPlan;

    public function __construct( ApiService $api, PaypalService $paypalService, SubscriptionPlanRepository $repoPlan, OrderRepository $repoOrder, SubscriptionService $subscriptionService)
    {
        $this->repoPlan = $repoPlan;
        $this->repoOrder = $repoOrder;
        $this->paypalService = $paypalService;
        $this->subscriptionService = $subscriptionService;
        $this->api = $api;
    }

    /**
     * @Route("/", name="all", methods={"GET"})
     */
    public function findAll(): JsonResponse
    {
        try{
            $grandPanier = $this->repoPlan->findOneBy(['name' => 'Grand Panier', 'status' => 'active']);
            $panierMoyen = $this->repoPlan->findOneBy(['name' => 'Panier moyen', 'status' => 'active']);
            $petitPanier = $this->repoPlan->findOneBy(['name' => 'Petit panier', 'status' => 'active']);

            $plans = [
                'grand_panier'=> $grandPanier,
                'panier_moyen'=> $panierMoyen,
                'petit_panier'=> $petitPanier
            ];
            return $this->api->success("List of Plans", $plans);
        }
        catch(\Exception $error){
            return $this->api->response($error->getCode(), $error->getMessage());
        }
    }

    /**
     * @Route("/{id}", name="details", methods={"GET"})
     */
    public function findDetails(SubscriptionPlan $plan, StripeService $stripeService): JsonResponse
    {  
        try{
            $user = $this->getUser();
            $paypal_env = $_ENV['PAYPAL_ENV'];
            $paypalClientId = $this->paypalService->clientId;
            
            if ($user) {
                $orders = $this->repoOrder->findBy(['user' => $user, 'subscription_plan' => $plan]);
                $activeOrderPlan = $this->subscriptionService->getActiveOrderPlanSubscription($orders);

                if (empty($activeOrderPlan)) {
                    $intentSecret = $stripeService->intentSecret();
                    $order = [];
                }else {
                    $order = $activeOrderPlan;
                    $intentSecret = '';
                }
            }else{
                $intentSecret = '';
                $order = [];
            }

            $inerval_unit = $plan::INTERVAL_UNIT[$plan->getIntervalUnit()];

            $rep = [
                'subscription' => $plan,
                'order' => $order,
                'paypal_clientId' => $paypalClientId,
                'paypal_env' => $paypal_env,
                'intentSecret' => $intentSecret,
                'inerval_unit' => $inerval_unit
            ];
            return $this->api->success("Details of Plan", $rep);
        }
        catch(\Exception $error){
            return $this->api->response($error->getCode(), $error->getMessage());
        }
    }

    // /**
    //  * @Route("/{id}/subscription/{subcriptionId}", name="subscription", methods={"GET"})
    //  */
    // public function subscription($id, $subcriptionId): JsonResponse
    // {
        
    //     $user = $this->getUser();
    //     $currentCartSubscription = $this->repoPlan->find($id);
        
    //     $facture = new FactureAbonnement();
    //     $facture->setSubscriptionId($subcriptionId);
    //     $facture->setUser($user);
    //     $facture->setCartSubscription($currentCartSubscription);

        
    //     //on traite la date de fin d'abonnnement 
    //     $date = new DateTime();
    //     $durationOfSubscription = $facture->getCartSubscription()->getDurationMonthSubscription();
    //     $dateSubscriptionEnd = $date->modify('first day of '. $durationOfSubscription .' month');
    //     $facture->setSubscriptionEnd($dateSubscriptionEnd);

    //     $this->em->persist($facture);
    //     $this->em->flush();

    //     return $this->json([
    //         'code' => 200, 
    //         'message' => 'Subscription successful',
    //     ]);    
    // }

    // /**
    //  * @Route("/account/stripe/subscription/plan/check", name="account_stripe_subscription_plan")
    //  */
    // public function account_stripe_subscription_plan(Request $request): Response
    // {
    //     // var data = {
    //     //     "stripePriceId": stripePriceId,
    //     //     "paymentMethodId": paymentMethodId,
    //     //     "planSubscriptionName": planSubscriptionName,
    //     //     "planSubscriptionId": planSubscriptionId
    //     // };

    //     /**
    //      * @var User
    //      */
    //     $user = $this->getUser();

    //     $dataPostAjax = $request->getContent();

    //     $jsonToArray =  json_decode($dataPostAjax, true);
        
    //     $stripePriceId = $jsonToArray["data"]["stripePriceId"];
    //     $paymentMethodId = $jsonToArray["data"]["paymentMethodId"];
    //     $planSubscriptionName = $jsonToArray["data"]["planSubscriptionName"]; 
    //     $planSubscriptionId = $jsonToArray["data"]["planSubscriptionId"]; 
    //     if ($request->getMethod() === "POST") {
    //         $this->stripeManager->persistSubscriptionPlan($stripePriceId, $paymentMethodId, $planSubscriptionName, $planSubscriptionId, $user);
    //     }
    //     return $this->json([
    //         'stripe_subscription_plan_checkout' => 'successfully'
    //     ]);

    //     // return $this->redirectToRoute('dashboard', ['stripe_subscription_plan_checkout' => 'successfully']);

    // }
}
