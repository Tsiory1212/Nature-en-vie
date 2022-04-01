<?php

namespace App\Controller;

use App\Entity\CartSubscription;
use App\Entity\SubscriptionPlan;
use App\Form\CartSubscriptionType;
use App\Form\SubscriptionPlanType;
use App\Repository\CartSubscriptionRepository;
use App\Repository\FactureAbonnementRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\SubscriptionPlanRepository;
use App\Repository\UserRepository;
use App\Service\Paypal\PaypalService;
use App\Service\StripeService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use BBapp\providers\payment\paypal\PaypalPlanManager as PlanManager;
use Sample\PayPalClient;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManager;

/**
 * @Route("/admin")
 */
class AdminSubscriptionController extends AbstractController
{
    private $em;
    private $repoPlan;
    private $repoProduct;
    private $repoUser;
    protected $stripeService;
    protected $repoOrder;

    public function __construct(EntityManagerInterface $em, SubscriptionPlanRepository $repoPlan, ProductRepository $repoProduct, UserRepository $repoUser, StripeService $stripeService, OrderRepository $repoOrder)
    {
        $this->em = $em;
        $this->repoPlan = $repoPlan;
        $this->repoProduct = $repoProduct;
        $this->repoUser = $repoUser;
        $this->stripeService = $stripeService;
        $this->repoOrder = $repoOrder;
    }


    /**
     * @Route("/subscription/liste", name="admin_subscription_list")
     */
    public function admin_subscription_list(): Response
    {
        $nbrProducts = count($this->repoProduct->findAll());
        $nbrUsers = count($this->repoUser->findAll());
        $nbrSubscriptions = count($this->repoPlan->findBy(['status' => 'active']));
        $nbrSubscriptionsDisabled = count($this->repoPlan->findBy(['status' => 0]));

        $subscriptions = $this->repoPlan->findBy(['status' => 'active']);
        
        return $this->render('admin/subscription/list_subscription.html.twig', [
            'subscriptions' => $subscriptions,
            'nbrProducts' => $nbrProducts,
            'nbrUsers' => $nbrUsers,
            'nbrSubscriptions' => $nbrSubscriptions,
            'nbrSubscriptionsDisabled' => $nbrSubscriptionsDisabled
        ]);
    }

    /**
     * @Route("/subscription/disabled/liste", name="admin_subscription_disabled_list")
     */
    public function admin_subscription_disabled_list(): Response
    {
        $nbrProducts = count($this->repoProduct->findAll());
        $nbrUsers = count($this->repoUser->findAll());
        $nbrSubscriptions = count($this->repoPlan->findBy(['active' => 1]));
        $subscriptionsDisabled = $this->repoPlan->findBy(['active' => 0]);

        return $this->render('admin/subscription/list_subscription_disabled.html.twig', [
            'nbrProducts' => $nbrProducts,
            'nbrUsers' => $nbrUsers,
            'nbrSubscriptions' => $nbrSubscriptions,
            'subscriptionsDisabled' => $subscriptionsDisabled
        ]);
    }

    /**
     * @Route("/subcription/create", name="admin_subscription_create")
     */
    public function admin_subscription_create(Request $request): Response
    {
        $plan = new SubscriptionPlan();

        $form = $this->createForm(SubscriptionPlanType::class, $plan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //On recupère les données soumises
            $nameSubscriptionPlan = $form->get('name')->getData();
            $amountSubscription = $form->get('amount')->getData();
            // $durationMonthSubscription = $form->getData()->getDurationMonthSubscription();
            $interval_unit = $form->get('interval_unit')->getData();
            // $trialPeriodDays = $form->get('trialPeriodDays')->getData();
            $description = $form->get('description')->getData();
            
            // On annulle la création du plan si il y a duplication de nom (vérification seulment dans les plans actifsd)
            if ($this->stripeService->plan_isInActivePlans($nameSubscriptionPlan)) {
                $this->addFlash(
                    'danger',
                    "Duplication de l'abonnement $nameSubscriptionPlan , penser à désactiver l'ancien abonnement avant de créer un abonnement de même nom"
                );
                
                return $this->redirectToRoute('admin_subscription_list');
            }

            // On utilise le stripeService pour synchroniser le traitrement dans Stripe
            $productStripe = $this->stripeService->createProduct($nameSubscriptionPlan);
            $productId = $productStripe->id;
            $plan->setProductIdStripe($productId);

            $priceStripe = $this->stripeService->createPrice($amountSubscription, $interval_unit, $productId, $description);
            $plan->setPriceIdStripe($priceStripe->id);

            // $planStripe = $this->stripeService->createPlan($amountSubscription, $interval_unit, $productId, $trialPeriodDays, $description);
            // $plan->setPlanIdStripe($planStripe->id);

            $plan->setStatus('active');
            $this->em->persist($plan);
            $this->em->flush();

            $this->addFlash(
               'success',
               'Abonnement créé avec succès'
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
    public function admin_subscription_edit(SubscriptionPlan $plan, Request $request, PaypalService $paypalService): Response
    {
        $form = $this->createForm(CartSubscriptionType::class, $plan)
            ->remove('idProductPlanPaypal')
            ->remove('priceSubscription')
            ->remove('durationMonthSubscription')
            ->remove('interval_unit')
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            //On recupère les données soumises
            $nameSubscriptionPlan = $form->getData()->getNameSubscriptionPlan();
            $descriptionSubscriptionPlan = $form->getData()->getDescriptionSubscriptionPlan();
            
            // On annulle la création du plan si il y a duplication de nom (vérification seulment dans les plans actifsd)
            if ($this->paypalService->plan_isInActivePlans($nameSubscriptionPlan)) {
                $this->addFlash(
                    'danger',
                    "Duplication de l'abonnement $nameSubscriptionPlan , penser à désactiver l'ancien abonnement avant de créer un abonnement de même nom"
                );
                
                return $this->redirectToRoute('admin_subscription_list');
            }

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
     * @Route("/subscription/plan/{id}/deactive", name="admin_subscription_plan_deactivate")
     */
    public function admin_subscription_plan_deactivate(CartSubscription $cartSubscription, Request $request, PaypalService $paypalService): Response
    {        
        if ($this->isCsrfTokenValid('deactivate', $request->get('_token'))) {
            $cartSubscription->setActive(0);
            $this->em->persist($cartSubscription);
            $this->em->flush();
            $paypalService->deactiveSubscriptionPlan($cartSubscription->getIdSubscriptionPlanPaypal());
                        
            $this->addFlash(
                'danger',
                'Plan d\'abonnement désactivé'
             );
             return $this->redirectToRoute('admin_subscription_list');
        }else{
            return $this->redirectToRoute('admin_subscription_list', ['error' => 'invalid_token']);
        }

    }

    /**
     * @Route("/subscription/plan/{id}/activate", name="admin_subscription_plan_activate")
     */
    public function admin_subscription_plan_activate(CartSubscription $cartSubscription, Request $request, PaypalService $paypalService): Response
    {        
        if ($this->isCsrfTokenValid('activate_plan', $request->get('_token'))) {
            $cartSubscription->setActive(1);
            $this->em->persist($cartSubscription);
            $this->em->flush();
            $paypalService->activeSubscriptionPlan($cartSubscription->getIdSubscriptionPlanPaypal());
                        
            $this->addFlash(
                'success',
                'Plan d\'abonnement activé'
             );
             return $this->redirectToRoute('admin_subscription_list');
        }else{
            return $this->redirectToRoute('admin_subscription_list', ['error' => 'invalid_token']);
        }

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
        // $factures = $this->repoFactureAbonnement->findAll();

        return $this->render('admin/subscription/list_subscriber.html.twig', [
            'factures' => $factures
        ]);
    }

    /**
     * Permet de lister les abonnées par rapport à un abonnement
     * 
     * @Route("/subscribers/plan/{id}", name="admin_subscriber_plan_list")
     */
    public function admin_subscriber_plan_list(SubscriptionPlan $plan): Response
    {

        $orders = $this->repoOrder->findBy(['subscription_plan' => $plan->getId()]);

        return $this->render('admin/subscription/list_subscriber.html.twig', [
            'orders' => $orders
        ]);
    }

}
