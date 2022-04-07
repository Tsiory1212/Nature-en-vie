<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\OrderRepository;
use App\Repository\SubscriptionPlanRepository;
use App\Service\Panier\PanierService;
use Doctrine\ORM\EntityManagerInterface;

use function PHPUnit\Framework\isNull;

class StripeService
{
    private $secretKey;
    public $publishablaKey;
    protected $em;
    protected $repoPlan;
    protected $panierService;
    protected $repoOrder;

    public function __construct(EntityManagerInterface $em, SubscriptionPlanRepository $repoPlan, PanierService $panierService, OrderRepository $repoOrder)
    {
        if ($_ENV['STRIPE_ENV'] === 'test') {
            $this->secretKey = $_ENV['STRIPE_SECRET_KEY_TEST'];
            $this->publishablaKey = $_ENV['STRIPE_PUBLISHABLE_KEY_TEST'];
        } else {
            $this->secretKey = $_ENV['STRIPE_SECRET_KEY_LIVE'];
            $this->publishablaKey = $_ENV['STRIPE_PUBLISHABLE_KEY_LIVE'];
        }    

        $this->em = $em;
        $this->repoPlan = $repoPlan;
        $this->panierService = $panierService;
        $this->repoOrder = $repoOrder;
    }

    /**
     * Undocumented function
     *
     * @param Product $product
     * @return object
     */
    public function paymentIntent()
    {
        \Stripe\Stripe::setApiKey($this->secretKey); 
        
        $totalPrice = $this->panierService->getTotalPrice();
     
        if ($totalPrice > 0) {
            $totalPrice = $totalPrice;
        }else{
            $totalPrice = 1;
        }
        $intentStripe = \Stripe\PaymentIntent::create([
            'amount' => $totalPrice * 100,
            'currency' =>  'eur',
            'payment_method_types' =>  ['card']
        ]);

        return $intentStripe;
    }


    public function intentSecret()
    {
        $intent = $this->paymentIntent();

        return $intent['client_secret'] ?? null;
    }
    
  
    /**
     *
     * @param array $stripeParameter --- POST's result
     * @param Product $product
     */
    public function getDatasAfterPayment(array $stripeParameter)
    {
        \Stripe\Stripe::setApiKey($this->secretKey);

        $resource = null;
        // (1) => On récupère le IntentId        
        $stripeIntenId = $stripeParameter['id'];
        
        // (2) => On récupère le PaymentIntent via le IntentId        
        $payement_intent = null;
        if (isset($stripeIntenId)) {
            $payement_intent = \Stripe\PaymentIntent::retrieve($stripeIntenId);
        }

        // (3) => On gère les status du paiement        
        if ($stripeParameter['status'] === 'succeeded') {
            //TODO 
        } else {
            $payement_intent->cancel();
        }

        return $payement_intent;
    }
    
    /**
     * Permet de créer un plan d'abonnement
     *
     * @param integer $amount
     * @param string $interval
     * @param string $productId
     * @param [type] $trialPeriodDays
     * @param [type] $nickname
     * @return void
     */
    public function createPlan(int $amount, string $interval, string $productId, $trialPeriodDays = NULL, $nickname = NULL)
    {
        $stripe = new \Stripe\StripeClient($this->secretKey);

        $plan = $stripe->plans->create([
            'amount' => $amount * 100,
            'currency' => 'eur',
            'interval' => $interval,
            'product' => $productId
        ]);

        // On vérifie si le période de test ou le nickname est initialisé
        switch (true) {
            case $trialPeriodDays !== null:
                $stripe->plans->update(
                    $plan->id,
                    ['trial_period_days' => $trialPeriodDays]
                );     
            case $nickname !== null:
                $stripe->plans->update(
                    $plan->id,
                    ['nickname' => $nickname]
                );          
        }

        return $plan;
    }


    /**
     * Permet de créer un price
     *
     * @param integer $unit_amount
     * @param string $interval_unit
     * @param string $productId
     * @param string $nickname
     * @return void
     */
    public function createPrice(int $unit_amount, string $interval_unit, string $productId, string $nickname)
    {
        $stripe = new \Stripe\StripeClient($this->secretKey);

        $price = $stripe->prices->create([
            'unit_amount' => $unit_amount * 100,
            'currency' => 'eur',
            'recurring' => ['interval' => $interval_unit],
            'product' =>  $productId,
            'nickname' => $nickname
        ]);

        return $price;
    }

    /**
     * Permet de modifier un price
     *
     * @param integer $unit_amount
     * @param string $interval_unit
     * @param string $productId
     * @param string $nickname
     */
    public function updatePrice(string $priceId, int $unit_amount = null, string $interval_unit = null, string $nickname = null)
    {
        $stripe = new \Stripe\StripeClient($this->secretKey);

        $price = $stripe->prices->update(
            $priceId,
            [
                // 'unit_amount' => $unit_amount * 100,
                // 'recurring' => ['interval' => $interval_unit],
                'nickname' => $nickname
            ]
        );

        return $price;
    }

    /**
     * Permet de créer un produit
     *
     * @param string $name
     */
    public function createProduct(string $name)
    {
        $stripe = new \Stripe\StripeClient($this->secretKey);

        $product = $stripe->products->create([
            'name' => $name
        ]);

        return $product;
    }


    /**
     * Permet de modifier un produit
     *
     * @param string $name
     */
    public function updateProduct(string $productId, string $name)
    {
        $stripe = new \Stripe\StripeClient($this->secretKey);

        $product = $stripe->products->update(
            $productId,
            [
                'name' => $name
            ]
        );

        return $product;
    }


    /**
     * Permet de supprimer un produit
     *
     * NB: un produit relié à un Price ne peut pas être supprimé
     * @param string $productId
     */
    public function deleteProduct(string $productId)
    {
        $stripe = new \Stripe\StripeClient($this->secretKey);

        $product = $stripe->products->delete(
            $productId,
            []
        );

        return $product;
    }


    /**
     * Permet de récupérer tous les plan d'abonnement
     * @return object 
     */
    public function allPlans(int $limit)
    {
        $stripe = new \Stripe\StripeClient($this->secretKey);

        $plans = $stripe->plans->all(['limit' => $limit]);
        return $plans;
    }

    /**
     * Permet de récupérer un plan via son Id
     * @return object 
     */
    public function getPlan(string $planId)
    {
        $stripe = new \Stripe\StripeClient($this->secretKey);

        $plan = $stripe->plans->retrieve(
            $planId,
            []
        );
        return $plan;
    }


    public function deletePlan(string $planId)
    {
        $stripe = new \Stripe\StripeClient($this->secretKey);

        $plan = $stripe->plans->delete(
            $planId,
            []
        );

        return $plan;
    }


    /**
     * Permet de récupérer tous les produits
     * @return object 
     */
    public function allProducts(int $limit)
    {
        $stripe = new \Stripe\StripeClient($this->secretKey);

        $products = $stripe->products->all([
            'limit' => $limit,
            'active' => true
        ]);

        return $products;
    }

    /**
     * Permet de récupérer un produit par son Id
     *
     * @param string $productId
     * @return object
     */
    public function getProduct(string $productId)
    {
        $stripe = new \Stripe\StripeClient($this->secretKey);

        $product = $stripe->products->retrieve(
            $productId
        );

        return $product;
    }


    /**
     * Permet de créer un produit, et après rattaché un abonnement à ce produit
     *
     */
    public function createProductPlan(string $productName, int $amountPlan, string $intervalPlan, $trialPeriodeDays = 0, $nickname = NULL)
    {
        $product = $this->createProduct($productName);
        $productId = $product->id;
        $plan = $this->createPlan($amountPlan, $intervalPlan, $productId, $trialPeriodeDays, $nickname);
        return $plan;
    }

    
    /**
     * Permet de créer un client dans Stripe (souvent relié par un abonnement)
     */
    public function createCustomer($email, $name, $desciption)
    {
        $stripe = new \Stripe\StripeClient($this->secretKey);

        $customer = $stripe->customers->create([
            'description' => $desciption,
            'email' => $email,
            'name' => $name
        ]);

        return $customer;
    }
    

    public function getCustomer(string $customerId)
    {
        $stripe = new \Stripe\StripeClient($this->secretKey);

        $customer = $stripe->customers->retrieve(
            $customerId,
            [ ]
        );
        return $customer;
    }

    public function updateCustomer(string $customerId, $paymentMethodId)
    {
        $stripe = new \Stripe\StripeClient($this->secretKey);

        $stripe->customers->update(
            $customerId, 
            [
                'invoice_settings' => [
                    'default_payment_method' => $paymentMethodId
                ]
            ]
        );
    }

    public function getPaymentMethods($paymentMethodId)
    {
        $stripe = new \Stripe\StripeClient($this->secretKey);

        $payment_method = $stripe->paymentMethods->retrieve(
            $paymentMethodId
        );

        return $payment_method;
    }
    

    /**
     * Attache un objet PaymentMethod à un client.
     *
     * @param [type] $paymentMethodId
     * @param [type] $customerId
     */
    public function attachPaymentMethodToACustomer(string $paymentMethodId, string $customerId)
    {
        $stripe = new \Stripe\StripeClient($this->secretKey);

        $action = $stripe->paymentMethods->attach(
            $paymentMethodId,
            ['customer' => $customerId]
        );
        return $action;
    }

    /**
     * @return object
     * NB: les clients "Invité" ne sont pas pris en compte !
     * @see https://support.stripe.com/questions/guest-customer-faq
     */
    public function allCustomers()
    {
        $stripe = new \Stripe\StripeClient($this->secretKey);

        $customers = $stripe->customers->all(['limit' => 3]);
        return $customers;
    }

    
    /**
     * Permet de verifier si un plan créér 
     *
     * @param [type] $namePlan
     * @return boolean
     */
    public function plan_isInActivePlans($namePlan)
    {
        $testResults = [];
        $activePlansInBd = $this->repoPlan->findBy(['status' => 'active']);
        foreach ($activePlansInBd as $activePlan) {
            if ($namePlan === $activePlan->getName()) {
                $testResults[] = true;
            } else {
                $testResults[] = false;
            }
        }
        if (in_array(true, $testResults)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Permet de créer un nouveau Price ou (Plan) d'un produit 
     * Un produit qui est à titre de "Abonnement panier sur-mesure"
     */
    public function create_Subscription_ProductAndPrice($amount, $interval_unit)
    {
        $stripe = new \Stripe\StripeClient($this->secretKey);

        $reccurringSubscription = $this->repoOrder->findOneBy( ['payment_type' => Order::PAYMENT_TYPE[1]]);
        
        if($reccurringSubscription){
            $productId = $reccurringSubscription->getStripeData()['stripe_product_id'];
        }else{
            $product = $this->createProduct('Abonnement panier sur-mesure');
            $productId = $product['id'];
        }

        $price = $stripe->prices->create([
            'unit_amount' => $amount * 100,
            'currency' => 'eur',
            'recurring' => [
                'interval' => $interval_unit
            ],
            'product' => $productId
        ]);

        return $price;
    }

    /**
     * Permet de créer un abonnement sans fin, mais peut être annulé
     *
     * @param [type] $customerId
     * @param [type] $priceId
     * @return void
     */
    public function createSubscription($customerId, $priceId)
    {
        $stripe = new \Stripe\StripeClient($this->secretKey);

        $subscription = $stripe->subscriptions->create([
            'customer' => $customerId,
            'items' => [
                [
                    'price' => $priceId,
                    'quantity' => 1,
                ],
            ]
        ]);

        return $subscription;
    
    }



    /**
     * Permet de se désaboner à un abonnment
     *
     * @param string $subscriptionId
     */
    public function cancelSubscription($subscriptionId)
    {
        $stripe = new \Stripe\StripeClient($this->secretKey);

        $subscription = $stripe->subscriptions->cancel(
            $subscriptionId,
            []
        );

        
        return $subscription;
    }


    /**
     * Permet de créer un abonnemnet programmé/plannifié (ex: pendant 3 mois, 1 ans, ...)
     * On peut aussi démarrer l'abonnement dans une date future via le params 'start_date'
     */
    public function createScheduleSubscription( string $priceId, int $iterations, string $subscriptionId)
    {
        $stripe = new \Stripe\StripeClient($this->secretKey);

        $date = (new \DateTime())->getTimestamp();

        $createScheduleSubscription = $stripe->subscriptionSchedules->create([
            // 'from_subscription' => $subscriptionId,
            // 'start_date' => $date,
            'end_behavior' => 'cancel',
            'phases' => [
                [
                    'items' => [
                        [
                            'price' => $priceId,
                            'quantity' => 1,
                        ],
                    ],
                    'iterations' => $iterations,
                ],
            ]
        ]);
        return $createScheduleSubscription;
    }

    public function getSubscription($subscriptionId)
    {
        $stripe = new \Stripe\StripeClient($this->secretKey);

        $subscription = $stripe->subscriptions->retrieve(
            $subscriptionId,
            []
        );

        return $subscription;
    }

    public function allSubscriptions()
    {
        $stripe = new \Stripe\StripeClient($this->secretKey);

        $subscriptions = $stripe->subscriptions->all(['limit' => 10]);

        return $subscriptions;
    }


    public function getDatasAfterSubscription($paymentMethodId, $amountSubscription, $interval_unit, $iteration, User $user)
    {
        $paymentMethod = $this->getPaymentMethods($paymentMethodId);

        if ($user->getStripeCustomerId() == null) {
            $customer = $this->createCustomer($user->getEmail(), $user->getLastname(), 'Client abonnement panier sur-mesure');
            $paymentMethod->attach([ 'customer' => $customer['id'] ]);;
            $this->updateCustomer($customer['id'], $paymentMethodId);
                    
            $user->setStripeCustomerId($customer['id']);
            $this->em->persist($user);
            $this->em->flush();
        }else{
            $bddCustomerId = $user->getStripeCustomerId();
            $customer = $this->getCustomer($bddCustomerId);
        }
 
        $price = $this->create_Subscription_ProductAndPrice($amountSubscription, $interval_unit);
        $subscription = $this->createSubscription($customer['id'], $price['id']);
        // $ScheduleSubscription = $this->createScheduleSubscription($price['id'], $iteration, $subscription['id']);
        
        return $subscription;
    }

    public function getDatasAfterSubscriptionPlan($priceId, $paymentMethodId, $planSubscriptionName, User $user)
    {
        $paymentMethod = $this->getPaymentMethods($paymentMethodId);

        if ($user->getStripeCustomerId() == null) {
            $customer = $this->createCustomer($user->getEmail(), $user->getLastname(), "Client abonnement $planSubscriptionName");
            $paymentMethod->attach([ 'customer' => $customer['id'] ]);;
            $this->updateCustomer($customer['id'], $paymentMethodId);
                    
            $user->setStripeCustomerId($customer['id']);
            $this->em->persist($user);
            $this->em->flush();
        }else{
            $bddCustomerId = $user->getStripeCustomerId();
            $customer = $this->getCustomer($bddCustomerId);
        }
 
        $subscription = $this->createSubscription($customer['id'], $priceId);
                
        return $subscription;
    }
}