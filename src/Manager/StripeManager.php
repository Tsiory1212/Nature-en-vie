<?php
namespace App\Manager;

use App\Entity\Order;
use App\Entity\User;
use App\Repository\OrderRepository;
use App\Repository\SubscriptionPlanRepository;
use App\Service\Panier\PanierService;
use App\Service\StripeService;
use Doctrine\ORM\EntityManagerInterface;

class StripeManager {
    protected $em;

    /**
     * @var StripeService $stripeService
     */
    protected $stripeService;

    /**
     * @var PanierService $panierService
     */
    protected $panierService;

    protected $repoPlan;
    protected $repoOrder;

    public function __construct(EntityManagerInterface $em, StripeService $stripeService, PanierService $panierService, SubscriptionPlanRepository $repoPlan, OrderRepository $repoOrder)
    {
        $this->em = $em;
        $this->stripeService = $stripeService;
        $this->panierService = $panierService;
        $this->repoPlan = $repoPlan;
        $this->repoOrder = $repoOrder;
    }

 

    /**
     * Permet d'enregistrer dans la base de données les datas obtenus après le paiement
     */
    public function persistPayment($user, $stripeParameter)
    {
        $data =  $this->stripeService->getDatasAfterPayment($stripeParameter);
        // dd($data);
        if ($data) {
            $resource = [
                'stripe_brand' => $data['charges']['data'][0]['payment_method_details']['card']['brand'],
                'stripe_last4' => $data['charges']['data'][0]['payment_method_details']['card']['last4'],
                'stripe_charges_id' => $data['charges']['data'][0]['id'],
                'stripe_status' => $data['charges']['data'][0]['status'],
                'stripe_client_secret' => $data['client_secret']
            ];
        }

        if ($resource !== null ) {
            
            $order = new Order();
            $paymentType = $order::PAYMENT_TYPE[0];

            $order->setUser($user);
            $order->setCart([$this->panierService->putItemsIntoArray()]);
            $order->setTotalPrice($this->panierService->getTotalPrice());
            $order->setUpdatedAt(new \DateTime());
            $order->setCreatedAt(new \DateTime());
            $order->setReference(uniqid('', false));
            $order->setPaymentType($paymentType);
            $order->setStripeData($resource);

            $this->em->persist($order);
            $this->em->flush();

            // On détruit la session
            $this->panierService->destroyPanierSession();
        }

    }


    public function persistSubscription(int $amountSubscription, string $interva_unit, $iteration, $paymentMethodId, User $user)
    {
        $data = $this->stripeService->getDatasAfterSubscription($paymentMethodId, $amountSubscription, $interva_unit, $iteration, $user);

        if ($data) {
            $resource = [
                'stripe_subscription_id' => $data['id'],
                'stripe_customer_id' => $data['customer'],
                'stripe_price_id' => $data['items']['data'][0]['plan']['id'],
                'stripe_product_id' => $data['items']['data'][0]['plan']['product'],
                'stripe_amount' => $data['items']['data'][0]['plan']['amount'],
                'stripe_subscription_interval' => $data['items']['data'][0]['plan']['interval'],
                'stripe_subscription_status' => $data['status']
            ];
        }

        if ($resource !== null ) {
            
            $order = new Order();
            $paymentType = $order::PAYMENT_TYPE[1];

            $order->setUser($user);
            $order->setCart([$this->panierService->putItemsIntoArray()]);
            $order->setTotalPrice($this->panierService->getTotalPrice());
            $order->setUpdatedAt(new \DateTime());
            $order->setCreatedAt(new \DateTime());
            $order->setReference(uniqid('', false));
            $order->setPaymentType($paymentType);
            $order->setStripeData($resource);

            $this->em->persist($order);
            $this->em->flush();

            // On détruit la session
            $this->panierService->destroyPanierSession();
        }
    }

    public function persistSubscriptionPlan($stripePriceId, $paymentMethodId, $planSubscriptionName, $planSubscriptionId, $user)
    {
        $data = $this->stripeService->getDatasAfterSubscriptionPlan($stripePriceId, $paymentMethodId, $planSubscriptionName, $user);
        $plan = $this->repoPlan->findOneBy(['id' => $planSubscriptionId]);
        
        $planDatas = [
            'planSubscriptionId' => $planSubscriptionId,
            'planSubscriptionName' => $planSubscriptionName,
            'planSubscriptionAmount' => $plan->getAmount()
        ];

        if ($data) {
            $resource = [
                'stripe_subscription_id' => $data['id'],
                'stripe_customer_id' => $data['customer'],
                'stripe_price_id' => $data['items']['data'][0]['plan']['id'],
                'stripe_product_id' => $data['items']['data'][0]['plan']['product'],
                'stripe_amount' => $data['items']['data'][0]['plan']['amount'],
                'stripe_subscription_interval' => $data['items']['data'][0]['plan']['interval'],
                'stripe_subscription_status' => $data['status']
            ];
        }

        if ($resource !== null ) {
            $order = new Order();
            $paymentType = $order::PAYMENT_TYPE[2];

            $order->setUser($user);
            $order->setSubscriptionPlanDatas($planDatas);
            $order->setTotalPrice($plan->getAmount());
            $order->setUpdatedAt(new \DateTime());
            $order->setCreatedAt(new \DateTime());
            $order->setReference(uniqid('', false));
            $order->setPaymentType($paymentType);
            $order->setStripeData($resource);
            $order->setSubscriptionPlan($plan);

            $this->em->persist($order);
            $this->em->flush();
        }
    }

    public function cancelSubscription($orderId, $stripeSubscriptionId)
    {
        $data = $this->stripeService->cancelSubscription($stripeSubscriptionId);
        
        $order = $this->repoOrder->findOneBy(['id' => $orderId]);
        
        if ($data) {
            $order->setUpdatedAt(new \DateTime());
            $order->setStatusStripeData($data->status);
            $this->em->persist($order);
            $this->em->flush();
        }

        return $data;
    }
}