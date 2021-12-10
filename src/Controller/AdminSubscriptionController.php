<?php

namespace App\Controller;

use App\Entity\CartSubscription;
use App\Form\CartSubscriptionType;
use App\Repository\CartSubscriptionRepository;
use App\Repository\FactureAbonnementRepository;
use App\Service\Paypal\PaypalService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminSubscriptionController extends AbstractController
{
    private $em;
    private $repoSubscription;
    private $repoFactureAbonnement;

    public function __construct(EntityManagerInterface $em, CartSubscriptionRepository $repoSubscription, FactureAbonnementRepository $repoFactureAbonnement)
    {
        $this->em = $em;
        $this->repoSubscription = $repoSubscription;
        $this->repoFactureAbonnement = $repoFactureAbonnement;
    }


    /**
     * @Route("/subscription", name="admin_subscription_list")
     */
    public function admin_subscription_list(): Response
    {
        $subscriptions = $this->repoSubscription->findAll();
        return $this->render('admin/subscription/list_subscription.html.twig', [
            'subscriptions' => $subscriptions
        ]);
    }

    /**
     * @Route("/subcription/create", name="admin_subscription_create")
     */
    public function admin_subscription_create(Request $request, PaypalService $paypalService): Response
    {

        $abonnement = new CartSubscription();

        $form = $this->createForm(CartSubscriptionType::class, $abonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //On recupère les données soumises
            $nameProductPlan = "PROD-30J4697439268140M";
            $nameSubscriptionPlan = $form->getData()->getNameSubscriptionPlan();
            $descriptionSubscriptionPlan = $form->getData()->getDescriptionSubscriptionPlan();
            $priceSubscription = $form->getData()->getPriceSubscription();
            $durationMonthSubscription = $form->getData()->getDurationMonthSubscription();

          

            // On utilise le service paypal pour synchroniser le traitrement dans PAYPAL.com
            $paypalService->createSubscriptionPlan(
                $nameProductPlan,
                $nameSubscriptionPlan, 
                $descriptionSubscriptionPlan, 
                $durationMonthSubscription,
                $priceSubscription
            );

            $abonnement->setIdProductPlanPaypal("PROD-30J4697439268140M");
            $abonnement->setIdSubscriptionPlanPaypal($paypalService->idSubscriptionPlanPaypal);

            $this->em->persist($abonnement);
            $this->em->flush();

            $this->addFlash(
               'success',
               'Abonnement ajouté avec succès'
            );
            return $this->redirectToRoute('admin_subcription_list');
        }
        return $this->render('admin/subscription/create_subscription.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/subscribers", name="admin_subscriber_list")
     */
    public function admin_subscriber_list(): Response
    {
        $factures = $this->repoFactureAbonnement->findAll();

        return $this->render('admin/subscription/list_subscriber.html.twig', [
            'factures' => $factures
        ]);
    }

    /**
     * @Route("/subscribers/plan/{id}", name="admin_subscriber_plan_list")
     */
    public function admin_subscriber_plan_list(CartSubscription $cartSubscription): Response
    {
        $factures = $this->repoFactureAbonnement->findBy(['cartSubscription' => $cartSubscription]);

        return $this->render('admin/subscription/list_subscriber.html.twig', [
            'factures' => $factures
        ]);
    }

    
     /**
     * @Route("/subcription/{id}/edit", name="admin_subscription_edit")
     */
    public function admin_product_edit(CartSubscription $subscription, Request $request, PaypalService $paypalService): Response
    {
        $form = $this->createForm(CartSubscriptionType::class, $subscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            //On recupère les données soumises
            $nameProductPlan = "PROD-30J4697439268140M";
            $nameSubscriptionPlan = $form->getData()->getNameSubscriptionPlan();
            $descriptionSubscriptionPlan = $form->getData()->getDescriptionSubscriptionPlan();
            $priceSubscription = $form->getData()->getPriceSubscription();
            $durationMonthSubscription = $form->getData()->getDurationMonthSubscription();



            // On utilise le service paypal pour synchroniser le traitrement dans PAYPAL.com
            $paypalService->editSubscriptionPlan(
                $nameProductPlan,
                $nameSubscriptionPlan, 
                $descriptionSubscriptionPlan, 
                $durationMonthSubscription,
                $priceSubscription
            );

            // $subscription->setIdProductPlanPaypal("PROD-30J4697439268140M");
            $subscription->setIdSubscriptionPlanPaypal($paypalService->idSubscriptionPlanPaypal);

            $this->em->persist($subscription);
            $this->em->flush();
            $this->addFlash(
               'success',
               'Abonnement modifié avec succès'
            );
            return $this->redirectToRoute('admin_subscription_list');
        }
        return $this->render('admin/subscription/create_subscription.html.twig', [
            'form' => $form->createView(),
            'subscription' => $subscription
        ]);
    }

}
