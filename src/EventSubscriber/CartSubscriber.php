<?php
namespace App\EventSubscriber;

use App\Repository\CartSubscriptionRepository;
use App\Repository\SubscriptionPlanRepository;
use App\Service\Panier\PanierService;
use App\Service\Paypal\PaypalService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

/**
 * Class CartSubscriber
 *
 *  Représente le Listener écoutant l'événement 'kernel.controller'.
 *
 */
class CartSubscriber implements EventSubscriberInterface {
 
    private Environment $environment;
    private $em;
    private $repoPlan;
    private $paypalService;
    private $panierService;

    public function __construct(Environment $environment, EntityManagerInterface $em, SubscriptionPlanRepository $repoPlan, PaypalService $paypalService, PanierService $panierService)
    {
        $this->environment = $environment;
        $this->em = $em;
        $this->repoPlan = $repoPlan;
        $this->paypalService = $paypalService;
        $this->panierService = $panierService;
    }

 
    /**
     * Injection de la variable globale carts à Twig
     *
     * @param ControllerEvent $event
     */
    public function onKernelController(ControllerEvent $event) {
        $subscriptionPlans = $this->repoPlan->findBy(['status' => 'active']);

        $cartItems['items'] = $this->panierService->getFullcart();
        $cartItems['quantity'] = $this->panierService->allQuantityItem();
        $cartItems['totalPrice'] = $this->panierService->getTotalPrice();

  
        $this->environment->addGlobal('subscriptionPlans_subcriberEvent', $subscriptionPlans);
        $this->environment->addGlobal('cartItems_subcriberEvent', $cartItems);
        
    }
 
    /**
     *
     * @return array
     */
    public static function getSubscribedEvents(): array {
        return [
            'kernel.controller' => 'onKernelController',
        ];
    }
}