<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Service\Panier\PanierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\DataTransformer\StringToFloatTransformer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PaypalController extends AbstractController
{
    protected $em;
    protected $session;

    public function __construct(EntityManagerInterface $em, SessionInterface $session)
    {
        $this->em = $em;
        $this->session = $session;
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
}
