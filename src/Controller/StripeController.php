<?php
namespace App\Controller;

use App\Entity\User;
use App\Manager\StripeManager;
use App\Service\StripeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Message;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    /** 
     * @var StripeManager
     */
    protected $stripeManager;

    public function __construct(StripeManager $stripeManager)
    {
        $this->stripeManager = $stripeManager;
    }

    /**
     * Permet de finaliser le paiement après un payment_intent
     * 
     * @Route("/accout/stripe/payment/check", name="account_stripe_payment")
     */ 
    public function account_stripe_payment(Request $request): Response
    {
        $user = $this->getUser();

        if ($request->getMethod() === "POST") {
            $this->stripeManager->persistPayment($user, $_POST);
        }

        return $this->redirectToRoute('dashboard', ['stripe_checkout' => 'successfully']);
    }

    /**
     * @Route("/account/stripe/subscription/ckeck", name="account_stripe_subscription")
     */
    public function account_stripe_subscription(Request $request, StripeService $stipeService): Response
    {
        /**
         * @var User
         */
        $user = $this->getUser();

        $dataPostAjax = $request->getContent();

        $jsonToArray =  json_decode($dataPostAjax, true);
        
        $amountSubscription = (int) $jsonToArray["data"]["price"];
        $interva_unit = $jsonToArray["data"]["interval_unit"];
        $paymentMethodId = $jsonToArray["data"]["paymentMethodId"];
        $iteration = $jsonToArray["data"]["iteration"];
        

        if ($request->getMethod() === "POST") {
            $this->stripeManager->persistSubscription($amountSubscription, $interva_unit, $iteration, $paymentMethodId, $user);
        }


        return $this->redirectToRoute('dashboard', ['stripe_subscription_checkout' => 'successfully']);
    }

    /**
     * @Route("/account/stripe/subscription/plan/check", name="account_stripe_subscription_plan")
     */
    public function account_stripe_subscription_plan(Request $request): Response
    {
        /**
         * @var User
         */
        $user = $this->getUser();

        $dataPostAjax = $request->getContent();

        $jsonToArray =  json_decode($dataPostAjax, true);
        
        $stripePriceId = $jsonToArray["data"]["stripePriceId"];
        $paymentMethodId = $jsonToArray["data"]["paymentMethodId"];
        $planSubscriptionName = $jsonToArray["data"]["planSubscriptionName"]; 
        $planSubscriptionId = $jsonToArray["data"]["planSubscriptionId"]; 
        if ($request->getMethod() === "POST") {
            $this->stripeManager->persistSubscriptionPlan($stripePriceId, $paymentMethodId, $planSubscriptionName, $planSubscriptionId, $user);
        }
        return $this->json([
            'stripe_subscription_plan_checkout' => 'successfully'
        ]);

        // return $this->redirectToRoute('dashboard', ['stripe_subscription_plan_checkout' => 'successfully']);

    }
    

}