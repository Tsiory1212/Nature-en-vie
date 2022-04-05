<?php

namespace App\Controller;

use App\Entity\FactureAbonnement;
use App\Entity\Order;
use App\Entity\PauseDelivry;
use App\Entity\PauseLivraison;
use App\Entity\SubscriptionPlan;
use App\Form\PauseLivraisonType;
use App\Manager\StripeManager;
use App\Repository\FactureAbonnementRepository;
use App\Repository\OrderRepository;
use App\Service\Paypal\PaypalService;
use App\Service\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountSubscriptionController extends AbstractController
{
    private $em;

    protected $paypalService;

    /** @var OrderRepository $repoOrder */
    protected $repoOrder;

    /** @var StripeService $stripeService */
    protected $stripeService;

    protected $stripeManager;

    public function __construct(EntityManagerInterface $em, PaypalService $paypalService, OrderRepository $repoOrder, StripeService $stripeService, StripeManager $stripeManager)
    {
        $this->em = $em;
        $this->paypalService = $paypalService;
        $this->repoOrder = $repoOrder;
        $this->stripeService = $stripeService;
        $this->stripeManager = $stripeManager;
    }

    /**
     * @Route("/account/livraison/continue/mais/abonnement/{id}/continue", name="account_delivery_suspend")
     */
    public function account_delivery_suspend($id, Request $request, FactureAbonnementRepository $repoFacture)
    {
        $pauseDelivry = new PauseDelivry();

        /**
         * @var FactureAbonnement $currentFacture 
         */
        $currentFacture = $repoFacture->find($id);

        $form = $this->createForm(PauseDelivryType::class, $pauseDelivry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentFacture->setPauseLivraison($pauseDelivry);
            $pauseDelivry->setFactureAbonnement($currentFacture);
            
            $this->em->persist($pauseDelivry);
            $this->em->flush();
            $this->addFlash(
               'success',
               'Livraison en pause'
            );
            return $this->redirectToRoute('dashboard');
        }
        return $this->render('account/subscription/pause_delivry.html.twig', [
            'form' => $form->createView()
        ]);
    }

        /**
     * @Route("/account/livraison/continue/abonnement/{id}", name="account_delivery_continue")
     */
    public function account_delivery_continue($id, FactureAbonnementRepository $repoFacture)
    {
        $user = $this->getUser();
       
        /**
         * @var FactureAbonnement $currentFacture 
         */
        $currentFacture = $repoFacture->find($id);
        
        if ($user !== $currentFacture->getUser() ) {
           return $this->redirectToRoute('dashboard');
        }

        // $currentFacture->setPauseLivraison(null);
        
        $this->em->remove($currentFacture->getPauseLivraison());
        $this->em->flush();
        $this->addFlash(
            'success',
            'Livraison continue'
        );
        return $this->redirectToRoute('dashboard');

    }

    /**
     * @Route("/account/order/{orderId}/plan/show/detail", name="account_order_plan_show_detail")
     */
    public function account_order_plan_show_detail(Order $orderId ): Response
    {

        $orderPlan = $this->repoOrder->findOneBy(['id' => $orderId]);

        $intervalUnitSubscription = SubscriptionPlan::INTERVAL_UNIT[$orderPlan->getStripeData()['stripe_subscription_interval']] ;
        return $this->render('/account/subscription/plan/show_subscription_plan_detail.html.twig', [
            'orderPlan' => $orderPlan,
            'intervalUnitSubscription' => $intervalUnitSubscription
        ]);
    }

    /**
     * @Route("/account/order/{orderId}/plan/subscription/{stripeSubscriptionId}/cancel", name="account_plan_subscription_cancel")
     */
    public function account_plan_subscription_cancel($orderId, $stripeSubscriptionId): Response
    {
        // dd(
        //     $this->stripeService->getSubscription($subscriptionId)
        // );
        $subscription = $this->stripeManager->cancelSubscriptionPlan($orderId, $stripeSubscriptionId);

        return $this->json([
            'code' => 200, 
            'message' => 'subscription_plan_canceled',
            'status_subscription_plan' => $subscription->status
        ]);
    }
}
