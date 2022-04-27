<?php

namespace App\Controller;

use App\Entity\FactureAbonnement;
use App\Entity\Order;
use App\Entity\PauseDelivry;
use App\Entity\PauseLivraison;
use App\Entity\SubscriptionPlan;
use App\Form\PauseDelivryType;
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
     * @Route("/account/order/{orderId}/plan/show/detail", name="account_order_plan_show_detail")
     */
    public function account_order_plan_show_detail(Order $orderId, Request $request ): Response
    {

        $orderPlan = $this->repoOrder->findOneBy(['id' => $orderId]);
        $pausePlan = new PauseDelivry();
        $form = $this->createForm(PauseDelivryType::class, $pausePlan);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pausePlan->setOrderPaused($orderId);
            $this->em->persist($pausePlan);
            $this->em->flush();
            $this->addFlash(
               'success',
               'Vous avez defini une pause de temps'
            );
            return $this->redirectToRoute('account_order_plan_show_detail', ['orderId' => $orderId->getId()]);
        }

        $intervalUnitSubscription = SubscriptionPlan::INTERVAL_UNIT[$orderPlan->getStripeData()['stripe_subscription_interval']] ;
        return $this->renderForm('/account/subscription/plan/show_subscription_plan_detail.html.twig', [
            'orderPlan' => $orderPlan,
            'intervalUnitSubscription' => $intervalUnitSubscription,
            'form' => $form
        ]);
    }

    /**
     * @Route("/account/order/{orderId}/subscription/{stripeSubscriptionId}/cancel", name="account_subscription_cancel")
     */
    public function account_subscription_cancel($orderId, $stripeSubscriptionId): Response
    {
        $subscription = $this->stripeManager->cancelSubscription($orderId, $stripeSubscriptionId);

        return $this->json([
            'code' => 200, 
            'message' => 'subscription_plan_canceled',
            'status_subscription_plan' => $subscription->status
        ]);
    }



}
