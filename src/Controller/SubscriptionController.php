<?php

namespace App\Controller;

use App\Entity\CartSubscription;
use App\Entity\FactureAbonnement;
use App\Repository\CartSubscriptionRepository;
use App\Service\Paypal\PaypalService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController extends AbstractController
{
    private $em;
    protected $paypalService;
    protected $repoSubCart;

    public function __construct(EntityManagerInterface $em, PaypalService $paypalService, CartSubscriptionRepository $repoSubCart)
    {
        $this->em = $em;
        $this->paypalService = $paypalService;
        $this->repoSubCart = $repoSubCart;
    }


    /**
     * @Route("/abonnements", name="subscription_list")
     */
    public function subscription_list( CartSubscriptionRepository $repoCartSubscription)
    {
        $grandPanier = $repoCartSubscription->findOneBy(['nameSubscriptionPlan' => 'Grand Panier', 'active' => true]);
        $moyenPanier = $repoCartSubscription->findOneBy(['nameSubscriptionPlan' => 'Moyen panier', 'active' => true]);
        $petitPanier = $repoCartSubscription->findOneBy(['nameSubscriptionPlan' => 'Petit panier', 'active' => true]);

   
        $subscriptions = $repoCartSubscription->findAll();
        return $this->render('subscription/list_subscription.html.twig', [
            'subscriptions' => $subscriptions,
            'grandPanier' => $grandPanier,
            'moyenPanier' => $moyenPanier,
            'petitPanier' => $petitPanier
        ]);
    }

    /**
     * @Route("/abonnement/{id}/show", name="cart_subscription_show")
     */
    public function cart_subscription_show(CartSubscription $subscription, CartSubscriptionRepository $repoAbonnement): Response
    {  
        $paypal_env = $_ENV['PAYPAL_ENV'];
        $paypalClientId = $this->paypalService->clientId;
        $paypalClientToken = $this->paypalService->getClientToken();
        
        $idSubscriptionPlanPaypal = $repoAbonnement->find($subscription->getId())->getIdSubscriptionPlanPaypal() ;

        return $this->render('subscription/show_subscription.html.twig', [
            'subscription' => $subscription,
            'idSubscriptionPlanPaypal' => $idSubscriptionPlanPaypal,
            'paypal_clientId' => $paypalClientId,
            'paypal_clientToken' => $paypalClientToken,
            'paypal_env' => $paypal_env 
        ]);
    }

    /**
     * @Route("/account/abonnement/{id}/{subcriptionId}", name="account_subscription_cart")
     */
    public function account_subscription_cart($id, $subcriptionId, CartSubscriptionRepository $repoCartSubscription): Response
    {
        $user = $this->getUser();
        $currentCartSubscription = $repoCartSubscription->find($id);
        
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
