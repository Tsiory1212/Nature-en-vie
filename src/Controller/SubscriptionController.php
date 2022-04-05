<?php

namespace App\Controller;

use App\Entity\CartSubscription;
use App\Entity\FactureAbonnement;
use App\Entity\SubscriptionPlan;
use App\Repository\CartSubscriptionRepository;
use App\Repository\OrderRepository;
use App\Repository\SubscriptionPlanRepository;
use App\Service\Panier\PanierService;
use App\Service\Paypal\PaypalService;
use App\Service\StripeService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Plan;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController extends AbstractController
{
    private $em;
    protected $paypalService;
    protected $repoPlan;

    protected $repoOrder;

    public function __construct(EntityManagerInterface $em, PaypalService $paypalService, SubscriptionPlanRepository $repoPlan, OrderRepository $repoOrder)
    {
        $this->em = $em;
        $this->paypalService = $paypalService;
        $this->repoPlan = $repoPlan;
        $this->repoOrder = $repoOrder;
    }


    /**
     * @Route("/abonnements", name="subscription_list")
     */
    public function subscription_list()
    {
        $grandPanier = $this->repoPlan->findOneBy(['name' => 'Grand Panier', 'status' => 'active']);
        $panierMoyen = $this->repoPlan->findOneBy(['name' => 'Panier Moyen', 'status' => 'active']);
        $petitPanier = $this->repoPlan->findOneBy(['name' => 'Petit panier', 'status' => 'active']);

   
        $subscriptions = $this->repoPlan->findAll();
        return $this->render('subscription/list_subscription.html.twig', [
            'subscriptions' => $subscriptions,
            'grandPanier' => $grandPanier,
            'panierMoyen' => $panierMoyen,
            'petitPanier' => $petitPanier
        ]);
    }

    /**
     * @Route("/abonnement/{id}/show", name="cart_subscription_show")
     */
    public function cart_subscription_show(SubscriptionPlan $plan, StripeService $stripeService): Response
    {  
        $user = $this->getUser();
        $paypal_env = $_ENV['PAYPAL_ENV'];
        $paypalClientId = $this->paypalService->clientId;
        
        if ($user) {
            $order = $this->repoOrder->findOneBy(['user' => $user, 'subscription_plan' => $plan]);
            if ($order === null) {
                $intentSecret = $stripeService->intentSecret();
            }else {
                $intentSecret = '';
            }
        }else{
            $intentSecret = '';
            $order = null;
        }

        $inerval_unit = $plan::INTERVAL_UNIT[$plan->getIntervalUnit()];

        return $this->render('subscription/show_subscription.html.twig', [
            'subscription' => $plan,
            'order' => $order,
            'paypal_clientId' => $paypalClientId,
            'paypal_env' => $paypal_env,
            'intentSecret' => $intentSecret,
            'inerval_unit' => $inerval_unit
        ]);
    }

    /**
     * @Route("/account/abonnement/{id}/{subcriptionId}", name="account_subscription_cart")
     */
    public function account_subscription_cart($id, $subcriptionId): Response
    {
        $user = $this->getUser();
        $currentCartSubscription = $this->repoPlan->find($id);
        
        $facture = new FactureAbonnement();
        $facture->setSubscriptionId($subcriptionId);
        $facture->setUser($user);
        $facture->setCartSubscription($currentCartSubscription);

        
        //on traite la date de fin d'abonnnement 
        $date = new DateTime();
        $durationOfSubscription = $facture->getCartSubscription()->getDurationMonthSubscription();
        $dateSubscriptionEnd = $date->modify('first day of '. $durationOfSubscription .' month');
        $facture->setSubscriptionEnd($dateSubscriptionEnd);

        $this->em->persist($facture);
        $this->em->flush();

        return $this->json([
            'code' => 200, 
            'message' => 'Subscription successfull',
        ]);    
    }


}
