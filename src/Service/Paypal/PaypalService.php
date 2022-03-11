<?php
namespace App\Service\Paypal;

use App\Repository\CartSubscriptionRepository;
use Exception;

class PaypalService  
{
    public $clientId;
    public $secret;
    public $env;
    public $idSubscriptionPlanPaypal;
    private $repoCartSubscription;

    public function __construct(CartSubscriptionRepository $repoCartSubscription)
    {
        $this->repoCartSubscription = $repoCartSubscription;

        if ($_ENV['PAYPAL_ENV'] == 'sandbox')  {
            $this->clientId = $_ENV['PAYPAL_SANDBOX_CLIENT_ID'];
            $this->secret = $_ENV['PAYPAL_SANDBOX_SECRET'];
            $this->env = '.sandbox';
        } else if($_ENV['PAYPAL_ENV'] == 'live') {
            $this->clientId = $_ENV['PAYPAL_CLIENT_ID'];
            $this->secret = $_ENV['PAYPAL_SECRET'];
            $this->env = '';
        }
        
    }

    /**
     * Permet de recupérer le l'authentification Bearer token sur PayPal
     */
    public function getToken()
    {        
        // Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api-m'.$this->env.'.paypal.com/v1/oauth2/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        curl_setopt($ch, CURLOPT_USERPWD, $this->clientId . ':' . $this->secret);

        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Accept-Language: en_US';
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        // dd($result);
        if(empty($result))die("Error: No response.");
        if (curl_errno($ch)) {
            throw new Exception( curl_error($ch), true);
        }

        curl_close($ch);
        return json_decode($result, true)['access_token'];
    }


    /**
     * Ceci est différent du "access_token", 
     * "client_token" est essentiel pour vérifier les paiements sans utiliser la notification instantanée de paiement (IPN) de PayPal. 
     * Le jeton permet à vos clients de suivre leur processus de paiement dans un canal sécurisé
    */
    public function getClientToken()
    {
        // Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api-m'.$this->env.'.paypal.com/v1/identity/generate-token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer '.$this->getToken();
        $headers[] = 'Accept-Language: en_US';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return json_decode($result, true)['client_token'];
    }


    /**
     * Permet de créer une/des commandes.
     */
    public function createOrder($price)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api-m'.$this->env.'.paypal.com/v2/checkout/orders');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{
            \"intent\": \"CAPTURE\",
            \"purchase_units\": [
                {
                    \"amount\": {
                          \"currency_code\": \"EUR\",
                            \"value\": \"$price\"
                    }
                }
            ]
        }");
       
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer '.$this->getToken();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return json_decode($result, true);

    }

        /**
     * Permet de capturer une commande.
     */
    public function captureOrder($orderId)
    {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, 'https://api-m'.$this->env.'.paypal.com/v2/checkout/orders/'.$orderId.'/capture');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer '.$this->getToken();
        // $headers[] = 'Paypal-Request-Id: 7b92603e-77ed-4896-8e78-5dea2050476a';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return json_decode($result, true);

    }
    
    /**
     * Permet de traiter la requête pour gérer une abonnement
     */
    // public function createSubscription(
    //     string $planId, 
    //     $startTime, 
    //     float $price, 
    //     string $emailAddress, 
    //     string $fullName, 
    //     string $addressLine1, 
    //     string $addressLine2, 
    //     string $postalCode, 
    //     string $countryCode, 
    //     string $locale_lang, 
    //     string $paypalRequestId)
    // {
    //     $ch = curl_init();

    //     curl_setopt($ch, CURLOPT_URL, 'https://api-m'.$this->env.'.paypal.com/v1/billing/subscriptions');
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($ch, CURLOPT_POST, 1);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, "{ 
    //         \"plan_id\": \"$planId\",
    //         \"start_time\": \"$startTime\",
    //         \"quantity\": \"1\",
    //         \"shipping_amount\": {
    //             \"currency_code\": \"EUR\",
    //             \"value\": \"$price\"\n  
    //         },
    //         \"subscriber\": {
    //             \"name\": {
    //                 \"given_name\": \" \",
    //                 \"surname\": \" \"
    //             },
    //             \"email_address\": \"$emailAddress\",
    //             \"shipping_address\": {
    //                 \"name\": {
    //                     \"full_name\": \"$fullName\"
    //                 },
    //                 \"address\": {
    //                     \"address_line_1\": \"$addressLine1\",
    //                     \"address_line_2\": \"$addressLine2\",
    //                     \"admin_area_2\": \"San Jose\",
    //                     \"admin_area_1\": \"CA\",
    //                     \"postal_code\": \"$postalCode\",
    //                     \"country_code\": \"$countryCode\"
    //                 }
    //             }
    //         },
    //         \"application_context\": {
    //             \"brand_name\": \"walmart\",
    //             \"locale\": \"$locale_lang\",
    //             \"shipping_preference\": \"SET_PROVIDED_ADDRESS\",
    //             \"user_action\": \"SUBSCRIBE_NOW\",
    //             \"payment_method\": {
    //                 \"payer_selected\": \"PAYPAL\",
    //                 \"payee_preferred\": \"IMMEDIATE_PAYMENT_REQUIRED\"
    //             },
    //             \"return_url\": \"https://example.com/returnUrl\",
    //             \"cancel_url\": \"https://example.com/cancelUrl\"
    //         }
    //     }");

    //     $headers = array();
    //     $headers[] = 'Content-Type: application/json';
    //     $headers[] = 'Authorization: Bearer '.$this->getToken();
    //     $headers[] = 'Paypal-Request-Id: '.$paypalRequestId;
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    //     $result = curl_exec($ch);
    //     if (curl_errno($ch)) {
    //         return throw new Exception( curl_error($ch), true);
    //     }
    //     curl_close($ch);
    //     $response =  json_decode($result, true);
    //     return $response['plans'];
    // }

   /**
     * Permet de traiter la requête pour gérer une abonnement
     */
    public function createSubscription(
        string $planId, 
        string $emailAddress, 
        string $fullName, 
        string $addressLine1, 
        string $countryCode, 
    ){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api-m'.$this->env.'.paypal.com/v1/billing/subscriptions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{ 
            \"plan_id\": \"$planId\",
            \"quantity\": \"1\",
            \"shipping_amount\": {
                \"currency_code\": \"EUR\",
                \"value\": \"0.00\"\n  
            },
            \"subscriber\": {
                \"email_address\": \"$emailAddress\",
                \"shipping_address\": {
                    \"name\": {
                        \"full_name\": \"$fullName\"
                    },
                    \"address\": {
                        \"address_line_1\": \"$addressLine1\",
                        \"country_code\": \"$countryCode\"
                    }
                }
            }
        }");

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer '.$this->getToken();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return throw new Exception( curl_error($ch), true);
        }
        curl_close($ch);
        $response =  json_decode($result, true);
        return $response;
    }
    
    public function showSubscriptionDetails($subscriptionId)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api-m'.$this->env.'.paypal.com/v1/billing/subscriptions/'.$subscriptionId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer '.$this->getToken();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $response =  json_decode($result, true);
        dd($response);
        return $response;
    }

    /**
     * Permet de capturer une abonnement---
     * Capture un paiement autorisé de l'abonné sur l'abonnement.
     */
    public function captureSubscription(string $subscriptionId){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api-m'.$this->env.'.paypal.com/v1/billing/subscriptions/I-BW452GLLEP1G/capture');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{
            \"note\": \"Charging as the balance reached the limit\",
            \"capture_type\": \"OUTSTANDING_BALANCE\",
            \"amount\": {
                \"currency_code\": \"USD\",
                \"value\": \"100\"
            }
        }");

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer '.$this->getToken();
        // $headers[] = 'Paypal-Request-Id: CAPTURE-160919-A0051';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        $response =  json_decode($result, true);
        return $response['plans'];
    }
    

    /**
     * Permet de recupérer tous les plans d'abonnement dans le produit PROD-0L957644DP166743N (Nature_en_vie)
     * 
     * PROD-4DK438462M382914V => product_id_Sandbox 
     * PROD-0L957644DP166743N => product_id_Live 
     *
     * @param string $productId
     */
    public function getAllSubscriptionPlanInProduct(){
        // On récupère les plans en fonction de l'environnement
        if ($_ENV['PAYPAL_ENV'] == 'sandbox')  {
            $productId = 'PROD-4DK438462M382914V';
        } else {
            $productId = 'PROD-0L957644DP166743N';
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api-m'.$this->env.'.paypal.com/v1/billing/plans?page=1&product_id='.$productId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer '.$this->getToken();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return throw new Exception( curl_error($ch), true);
        }
        curl_close($ch);
        $response =  json_decode($result, true);
        return $response['plans'];
    }

    /**
     * Permet de recupérer/filtrer tous les plans d'abonnement après condition (if plansInBd is in plansInApi))
     *
     * @return array
     */
    public function getPlanSubscriptionAfterCondition()
    {
        $planInBd = $this->repoCartSubscription->findBy(['active' => 1]);
        $plansIdInApi = [];
        foreach ($this->getAllSubscriptionPlanInProduct() as $plan) {
            $plansIdInApi[] = $plan['id'];
        }
      
        foreach ($planInBd as $plan) {
            if (in_array($plan->getIdSubscriptionPlanPaypal(), $plansIdInApi) ) {
               $plansAfterCondition[] = $plan;
            }
        }
        return $plansAfterCondition;
    }


    /**
     * Permer de recupérer tous les produits catalogues dans PayPal 
     */
    public function getAllProducts()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api-m'.$this->env.'.paypal.com/v1/catalogs/products?page=1&total_required=true');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer '.$this->getToken();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception( curl_error($ch), true);
        }
        curl_close($ch);
        $response =  json_decode($result, true);
        return $response['products'];
    }

    /**
     * Permet de créer un produit afin de le relier par un plan d'abonnement
     *
     * @param string $name
     * @param string $description
     */
    public function createProduct($name, $description)
    {
//    curl -v -X POST https://api-m.sandbox.paypal.com/v1/catalogs/products
//   -H "Content-Type: application/json"   -H "Authorization: Bearer Access-Token"   -H "PayPal-Request-Id: PRODUCT-18062020-001"   -d '{
//   "name": "Video Streaming Service",
//   "description": "A video streaming service",
//   "type": "SERVICE",
//   "category": "SOFTWARE",
//   "image_url": "https://example.com/streaming.jpg",
//   "home_url": "https://example.com/home"
// }'
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api-m'.$this->env.'.paypal.com/v1/catalogs/products');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n  \"name\" : $name ,\n\"description\": $description\n}");

        $headers = array();
        $headers[] = 'Authorization: Bearer '.$this->getToken();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return json_decode($result, true);  
    }

        /**
     * Permet de créer un produit afin de le relier par un plan d'abonnement
     *
     * @param string $name
     * @param string $description
     */
    public function editProduct($productId, $productName, $productDescription)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api-m'.$this->env.'.paypal.com/v1/catalogs/products/'+$productId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_POSTFIELDS, "[
            {
                \"path\": \"/name\",
                \"value\": \"$productName\",
                \"op\": \"replace\"
            },
            {
                \"path\": \"/description\",
                \"value\": \"$productDescription\",
                \"op\": \"replace\"
            }
        ]");
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer '.$this->getToken();

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return json_decode($result, true);  
    }


    
    /**
     * Permet de créer un plan d'abonnement.
     * Mais aussi de recupérer/renvoyer le idSubscriptionPlanPaypal
     * 
     * NB : le type du price doit être en string
     *
     * @param string $planName
     * @param string $planDescription
     * @param integer $durationMonth
     * @param string $price
     */
    public function createSubscriptionPlan(string $productId, string $planName, string $planDescription, string $interval_unit, int $durationMonth, string $price)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api-m'.$this->env.'.paypal.com/v1/billing/plans');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{
            \"product_id\": \"$productId\",      
            \"name\": \"$planName\",      
            \"description\": \"$planDescription\",      
            \"billing_cycles\": [        
                {            
                    \"frequency\": {                
                        \"interval_unit\": \"$interval_unit\",                
                        \"interval_count\": 1            
                    },           
                    \"tenure_type\": \"REGULAR\",            
                    \"sequence\": 1,            
                    \"total_cycles\": $durationMonth,            
                    \"pricing_scheme\": {                
                        \"fixed_price\": {                    
                            \"value\": \"$price\",                    
                            \"currency_code\": \"EUR\"                
                        }            
                    }        
                }      
            ],      
            \"payment_preferences\": {        
                \"auto_bill_outstanding\": true,        
                \"setup_fee_failure_action\": \"CONTINUE\",        
                \"payment_failure_threshold\": 3      
            }    
        }");

        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Authorization: Bearer '.$this->getToken();
        $headers[] = 'Prefer: return=representation';
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception( curl_error($ch), true);
        }
        curl_close($ch);
        $response =  json_decode($result, true);

        $this->idSubscriptionPlanPaypal =  $response['id'];
        return $response;
    }


    
    /**
     * Permet de modifier un plan d'abonnement
     * NB : le type du price doit être en string
     *
     * @param string $planBillingId
     * @param string $planName
     * @param string $planDescription
     * @param integer $durationMonth
     * @param string $price
     */
    public function editSubscriptionPlan(string $planSubscriptionId, string $planName, string $planDescription)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api-m'.$this->env.'.paypal.com/v1/billing/plans/'.$planSubscriptionId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_POSTFIELDS, "[
            {
                \"path\": \"/name\",
                \"value\": \"$planName\",
                \"op\": \"replace\"
            },
            {
                \"path\": \"/description\",
                \"value\": \"$planDescription\",
                \"op\": \"replace\"
            }
        ]");


        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Authorization: Bearer '.$this->getToken();
        $headers[] = 'Prefer: return=representation';
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception( curl_error($ch), true);
        }
        curl_close($ch);
        $response =  json_decode($result, true);
        return $response;
    }


    /**
     * Permet de désactiver un abonnement
     *
     * @param string $planSubscriptionId
     */
    public function deactiveSubscriptionPlan(string $planSubscriptionId)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api-m".$this->env.".com/v1/billing/plans/$planSubscriptionId/deactivate");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer '.$this->getToken();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);


        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception( curl_error($ch), true);
        }
        curl_close($ch);
        $response =  json_decode($result, true);
        return $response;
    }


    /**
     * Permet d'activer un abonnement
     *
     * @param string $planSubscriptionId
     */
    public function activeSubscriptionPlan(string $planSubscriptionId)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api-m".$this->env.".paypal.com/v1/billing/plans/$planSubscriptionId/activate");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer '.$this->getToken();

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception( curl_error($ch), true);
        }
        curl_close($ch);
        $response =  json_decode($result, true);
        return $response;
    }



    /**
     * Permet de supprimer un plan d'abonnement
     * see : https://developer.paypal.com/api/subscriptions/v1/
     *
     * @param string $productId
     */
    public function deleteSubscriptionPlan(string $planIdPaypal){
        // Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api-m'.$this->env.'.paypal.com/v1/billing/plans/'.$planIdPaypal);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');

        // curl_setopt($ch, CURLOPT_POSTFIELDS, "[\n  {\n    \"op\": \"remove\",\n       }\n]");




        // curl_setopt($ch, CURLOPT_POSTFIELDS, "[\n  {\n    \"op\": \"remove\",\n    \"path\": \"/payment_preferences/payment_failure_threshold\"\n  }\n]");




        // curl_setopt($ch, CURLOPT_POSTFIELDS, "[\n  {\n    \"value\": {\n        \"state\": \"DELETED\"\n     },\n    \"op\": \"replace\",\n    \"path\": \"/payment_preferences/payment_failure_threshold\"\n  }\n]");

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer '.$this->getToken();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception( curl_error($ch), true);
        }
        curl_close($ch);
        $response =  json_decode($result, true);
        return $response;
    }

    public function getCurrentTime()
    {
        return date('Y-m-d\\TH:i:s\\Z', time());
    }

    public function pushRequestID($id)
    {
        // push $id to your database and relate it to your customer
        
        //paypal_agreement_requests
        // customer_id VARCHAR(16), agreement_id VARCHAR(14), created_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    }

    public function getAprovalURL($planid)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api-m'.$this->env.'.paypal.com/v1/billing/subscriptions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        $time = $this->getCurrentTime();

        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n      \"plan_id\": $planid,\n      \"start_time\": $time,\n      \"application_context\": {\n        \"brand_name\": \"example company\",\n        \"locale\": \"fr-FR\",\n        \"shipping_preference\": \"SET_PROVIDED_ADDRESS\",\n        \"user_action\": \"SUBSCRIBE_NOW\",\n        \"payment_method\": {\n          \"payer_selected\": \"PAYPAL\",\n          \"payee_preferred\": \"IMMEDIATE_PAYMENT_REQUIRED\"\n        },\n        \"return_url\": \"https://example.com/returnUrl\",\n        \"cancel_url\": \"https://example.com/cancelUrl\"\n      }\n    }");

        $headers = array();
        $headers[] = 'Accept: application/json';
        // $headers[] = 'Authorization: _ENV["Bearer .this->getToken()"];
        $headers[] = 'Authorization: _ENV["Bearer .this->getToken()"]';
        $headers[] = 'Prefer: return=representation';
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception( curl_error($ch), true);
        }
        curl_close($ch);
        $response = json_decode($result, true);
        $this->pushRequestID($response->id);
        return $response->link[0]->href;

    }

    public function customerIdFromRequestId($id)
    {
       //SELECT customer_id FROM paypal_agreement_requests WHERE agreement_id=:id;
       // :id => $id;
       // return $data->customer_id;
    }

    public function getAgreement($id)
    {
        // Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api-m'.$this->env.'.paypal.com/v1/payments/billing-agreements/'.$id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer '.$this->getToken();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return json_decode($result, true);

    }

    /*** ORIGINAL */
    // public function processBody($body = file_get_contents('php://input'))
    // {
    //     $agreementId = $body->ressource->id;

    //     $agreement = $this->getAgreement($id);
    //     $status = $agreement->status;



    //     if ($status == 'ACTIVE') {
    //         $customer = new Customer();
    //         $customer = $customer->fromId($this->customerIdFromRequestId());
    //         $customer->setSubscribed(true);
    //     } else if($status == 'ACTIVE') {
    //         $customer->setSubscribed(true);
    //     } else{
    //         $customer->setSubscribed(false);
    //         $customer->sendNotification("your plan subscription status its ".$status.", please, renew it by clicking here: ".$agreement->links[1]->href." or cancel it here: ".$agreement->links[0]->href);
    //     }
    // }

    public function processBody($id)
    {
        $body = file_get_contents('php://input');
        $agreementId = $body->ressource->id;

        $agreement = $this->getAgreement($id);
        $status = $agreement->status;



        if ($status == 'ACTIVE') {
            $customer = new Customer();
            $customer = $customer->fromId($this->customerIdFromRequestId($id));
            $customer->setSubscribed(true);
        } else if($status == 'ACTIVE') {
            $customer->setSubscribed(true);
        } else{
            $customer->setSubscribed(false);
            $customer->sendNotification("your plan subscription status its ".$status.", please, renew it by clicking here: ".$agreement->links[1]->href." or cancel it here: ".$agreement->links[0]->href);
        }
    }

/**
    ---------------------REDIRECT TO APPROVE------------------------
    $paypalAPI = new Paypal();
    $agreementURL = $paypalAPI->getAprovalURL();
    header("Location:".$agreementURL);

    ---------------------WEBHOOK RESPONSE ENDPOINT------------------------
    $paypalAPI = new Paypal();
    $paypalAPI->processBody(file_get_contents('php://input));
 */
}
