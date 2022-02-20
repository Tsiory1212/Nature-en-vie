<?php

namespace App\Controller;

use App\Entity\CartSubscription;
use App\Entity\FactureAbonnement;
use App\Entity\PauseLivraison;
use App\Form\PauseLivraisonType;
use App\Repository\CartSubscriptionRepository;
use App\Repository\FactureAbonnementRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * @Route("/abonnements", name="subscription_list")
     */
    public function subscription_list( CartSubscriptionRepository $repoCartSubscription)
    {
        $grandPanier = $repoCartSubscription->findOneBy(['nameSubscriptionPlan' => 'Grand Panier']);
        $moyenPanier = $repoCartSubscription->findOneBy(['nameSubscriptionPlan' => 'Moyen panier']);
        $petitPanier = $repoCartSubscription->findOneBy(['nameSubscriptionPlan' => 'Petit panier']);
   
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
        $paypal_client_id = $_ENV['PAYPAL_CLIENT_ID'];
        $idSubscriptionPlanPaypal = $repoAbonnement->find($subscription->getId())->getIdSubscriptionPlanPaypal() ;

        return $this->render('subscription/show_subscription.html.twig', [
            'paypal_client_id' => $paypal_client_id,
            'subscription' => $subscription,
            'idSubscriptionPlanPaypal' => $idSubscriptionPlanPaypal
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
