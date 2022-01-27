<?php

namespace App\Controller;

use App\Entity\CartSubscription;
use App\Form\CartSubscriptionType;
use App\Repository\CartSubscriptionRepository;
use App\Repository\FactureAbonnementRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Service\Paypal\PaypalService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use BBapp\providers\payment\paypal\PaypalPlanManager as PlanManager;
use Sample\PayPalClient;

/**
 * @Route("/admin")
 */
class AdminSubscriptionController extends AbstractController
{
    private $em;
    private $repoSubscription;
    private $repoFactureAbonnement;
    private $repoProduct;
    private $repoUser;

    public function __construct(EntityManagerInterface $em, CartSubscriptionRepository $repoSubscription, FactureAbonnementRepository $repoFactureAbonnement, ProductRepository $repoProduct, UserRepository $repoUser)
    {
        $this->em = $em;
        $this->repoSubscription = $repoSubscription;
        $this->repoFactureAbonnement = $repoFactureAbonnement;
        $this->repoProduct = $repoProduct;
        $this->repoUser = $repoUser;
    }


    /**
     * @Route("/subscription", name="admin_subscription_list")
     */
    public function admin_subscription_list(): Response
    {
        $nbrProducts = count($this->repoProduct->findAll());
        $nbrUsers = count($this->repoUser->findAll());
        $nbrSubscriptions = count($this->repoSubscription->findAll());

        $subscriptions = $this->repoSubscription->findAll();
        return $this->render('admin/subscription/list_subscription.html.twig', [
            'subscriptions' => $subscriptions,
            'nbrProducts' => $nbrProducts,
            'nbrUsers' => $nbrUsers,
            'nbrSubscriptions' => $nbrSubscriptions
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
            $nameSubscriptionPlan = $form->getData()->getNameSubscriptionPlan();
            $descriptionSubscriptionPlan = $form->getData()->getDescriptionSubscriptionPlan();
            $priceSubscription = $form->getData()->getPriceSubscription();
            $durationMonthSubscription = $form->getData()->getDurationMonthSubscription();
            $productNumber = $form->getData()->getIdProductPlanPaypal();

            if (is_null($productNumber)) {
                return $this->redirectToRoute('admin_subscription_create', ['error' => 'null_product']);
            }
            // On utilise le service paypal pour synchroniser le traitrement dans PAYPAL.com
            $paypalService->createSubscriptionPlan(
                $productNumber,
                $nameSubscriptionPlan, 
                $descriptionSubscriptionPlan, 
                $durationMonthSubscription,
                $priceSubscription
            );

            $abonnement->setIdProductPlanPaypal($productNumber);
            $abonnement->setIdSubscriptionPlanPaypal($paypalService->idSubscriptionPlanPaypal);

            $this->em->persist($abonnement);
            $this->em->flush();

            $this->addFlash(
               'success',
               'Abonnement ajouté avec succès'
            );
            return $this->redirectToRoute('admin_subscription_list');
        }
        return $this->render('admin/subscription/create_subscription.html.twig', [
            'form' => $form->createView(),
        ]);
    }

 
     /**
     * @Route("/subcription/{id}/edit", name="admin_subscription_edit")
     */
    public function admin_subscription_edit(CartSubscription $subscription, Request $request, PaypalService $paypalService): Response
    {
        $form = $this->createForm(CartSubscriptionType::class, $subscription)
            ->remove('idProductPlanPaypal')
            ->remove('priceSubscription')
            ->remove('durationMonthSubscription')
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            //On recupère les données soumises
            $nameSubscriptionPlan = $form->getData()->getNameSubscriptionPlan();
            $descriptionSubscriptionPlan = $form->getData()->getDescriptionSubscriptionPlan();
            
            // On utilise le service paypal pour synchroniser le traitrement dans PAYPAL.com
            $paypalService->editSubscriptionPlan(
                $subscription->getIdSubscriptionPlanPaypal(),
                $nameSubscriptionPlan, 
                $descriptionSubscriptionPlan, 
            );


            $this->em->persist($subscription);
            $this->em->flush();
            $this->addFlash(
               'success',
               'Abonnement modifié avec succès'
            );
            return $this->redirectToRoute('admin_subscription_list');
        }
        return $this->render('admin/subscription/edit_subscription.html.twig', [
            'form' => $form->createView(),
            'subscription' => $subscription
        ]);
    }

    /**
     * Permet de supprimer un plan d'abonnement
     * 
     * @Route("/subscription/plan/{id}/delete", name="admin_subscription_plan_delete")
     */
    public function admin_subscription_plan_delete(CartSubscription $cartSubscription, Request $request, PaypalService $paypalService): Response
    {
        if ($this->isCsrfTokenValid('delete'. $cartSubscription->getId(), $request->get('_token'))) {
            $this->em->remove($cartSubscription);
            $this->em->flush();
            // $paypalService->deleteSubscriptionPlan($cartSubscription->getIdSubscriptionPlanPaypal());
                        
            $this->addFlash(
                'danger',
                'Plan d\'abonnement supprimé'
             );
        }
        return $this->redirectToRoute('admin_subscription_list');
    }


    /**
     * Permet de lister tous les abonnées
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
     * Permet de lister les abonnées par rapport à un abonnement
     * 
     * @Route("/subscribers/plan/{id}", name="admin_subscriber_plan_list")
     */
    public function admin_subscriber_plan_list(CartSubscription $cartSubscription): Response
    {
        $factures = $this->repoFactureAbonnement->findBy(['cartSubscription' => $cartSubscription]);

        return $this->render('admin/subscription/list_subscriber.html.twig', [
            'factures' => $factures
        ]);
    }

}
