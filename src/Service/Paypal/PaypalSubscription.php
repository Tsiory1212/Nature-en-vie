<?php
namespace App\Service\Paypal;

use App\Entity\CartSubscription;
use Sample\PayPalClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaypalSubscription
{
    private $username;
    private $password;
    private $signature;
    private $offers;
    private $endpoint;

    public function __construct($username = "sb-ikpdu7405512@business.example.com" , $password = "RY&x?5x1", $sandbox = true)
    {
        $this->username = $username;
        $this->password = $password;
        // $this->signature = $signature;
        // $this->offers = $offers;
        $this->endpoint = "https://api-m.". ($sandbox ? "sandbox" : "") .".paypal.com/v2/checkout/orders " ;
    }

    public function subscribe( CartSubscription $panier )
    {
        $curl = curl_init();
        $data = [
            'USER' => $this->username,
            'PWD' => $this->password,
            'SIGNATURE' => $this->signature,
            'METHOD' => 'setExpressCheckout',
            'VERSION' => 86,
            'L_BILLINGTYPE0' =>  'RecurringPayements',
            'L_BILLINGAGREEMENTDESCRIPTION0' =>  $panier->getName(),
            'cancelUrl' => 'https://127.0.0.1:8000/',
            'retunrUrl' => 'https://127.0.0.1:8000/proccess.php'

        ];

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->endpoint,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => http_build_query($data)
        ]);

        $response = curl_exec($curl);
        $responseArray = [];
        parse_str($response, $responseArray);
        dd($responseArray);
    }


}
