<?php

namespace App\Service;

use App\Entity\SubscriptionPlan;

class SubscriptionService
{

    /**
     * Permet de récupérer toutes les commnade d'abonnment en status "ACTIVE"
     *
     * @param array $orderPlan
     * @return array
     */
    public function getActiveOrderPlanSubscription(array $ordersPlan)
    {
        $orderPlanSubscriptionsActive = [];
        foreach ($ordersPlan as $order) {
            if ($order->getStripeData()['stripe_subscription_status'] === 'active') {
                $orderPlanSubscriptionsActive[] = $order;
            }
        }

        return $orderPlanSubscriptionsActive;
    }


    /**
     * Permet de récupérer le nom des plans 
     *
     * @param array $plans
     * @return array
     */
    public function getNamesPlan($plans)
    {
        $namesPlan = [];
        /** @var SubscriptionPlan $plan  */
        foreach ($plans as $plan) {
            $namesPlan[] = $plan->getName();
        }
        return $namesPlan;
    }
}