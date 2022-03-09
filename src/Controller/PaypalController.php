<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Service\Panier\PanierService;
use App\Service\Paypal\PaypalService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\DataTransformer\StringToFloatTransformer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PaypalController extends AbstractController
{
    private $em;
    protected $session;
    private $paypalService;

    public function __construct(EntityManagerInterface $em, SessionInterface $session, PaypalService $paypalService)
    {
        $this->em = $em;
        $this->session = $session;
        $this->paypalService = $paypalService;
    }

  /**
   * @param Request $request
   * @Route("/accout/paypal/payement/check", name="account_payment_paypal")
   */ 
    public function account_payment_paypal(PanierService $panierService, Request $request, SessionInterface $session)
    {
      $user = $this->getUser();
      
      //1- on récupère les données postées par ajax
      $payement_informations =  $request->request->all();

      //2- On verifie si on a le bon token
      if ($this->isCsrfTokenValid('order'. $user->getId(),  $request->headers->get('token'))) {
        $order = new Order();
        
        $cart = $panierService->putItemsIntoArray();
        $total_price = floatval($payement_informations["purchase_units"][0]["amount"]["value"]);
        $payer_id = $payement_informations["payer"]["payer_id"];
        $payer_email_address = $payement_informations["payer"]["email_address"];
        $transaction_number = $payement_informations["purchase_units"][0]["payments"]["captures"][0]["id"];
        

        $order->setCart($cart);
        $order->setTotalPrice($total_price);
        $order->setPayerIdPaypal($payer_id);
        $order->setPayerEmailPaypal($payer_email_address);
        $order->setTransactionNumberPaypal($transaction_number);
        $order->setUser($user);

        $this->em->persist($order);
        $this->em->flush();

        //3- on detruit les sessions
        $session->remove("panier");
        $session->remove("delivry");
          
        return $this->json([
          'paypal_purchase'=>'Transaction successfull',
          'status' => '200'
        ]);
      }else{
        return $this->json([
          'paypal_purchase'=>'error',
          'cause' => 'invalid token',
          'status' => '300'
        ]);
      }
      
    }

    /**
     * @Route("/account/paypal/subscription/create/{planId}/{email}/{fullName}/{address}/{countryCode}", name="account_paypal_create_subscription")
     */
    public function account_paypal_create_subscription($planId, $email, $fullName, $address, $countryCode)
    {
      $priceSubscription = 100;
        $subscriptionId = $this->paypalService->createSubscription($planId, $email, $fullName, $address, $countryCode);   
        return $this->json([
          'code' => 200, 
          'message' => 'create_subscription_ok',
          'subscriptionId' => $subscriptionId["id"],
          'links' =>  $subscriptionId["links"]
        ]);
        
    }

    /**
     * @Route("/test/show/detail/subcription/{subscriptionId}", name="test_show_detail_subscription")
     */
    public function test_show_detail_subscription($subscriptionId): Response
    {
      return $this->json(
        $this->paypalService->showSubscriptionDetails($subscriptionId)   
      );
    }

       /**
     * @Route("/test/deux/card/credit/paypal/{orderId}", name="test_deux_card_credit_paypal")
     */
    public function test_deux_card_credit_paypal($orderId)
    {
      // return $this->json(
      //   $this->paypalService->captureOrder($orderId)   
      // );
      return $this->json(
        $this->paypalService->showSubscriptionDetails($orderId)   
      );
    }
}
