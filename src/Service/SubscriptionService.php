<?php

namespace App\Service;

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
}