<?php
namespace App\EventSubscriber;

use App\Repository\CartSubscriptionRepository;
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
    private $repoCartSubscription;
    private $paypalService;

    public function __construct(Environment $environment, EntityManagerInterface $em, CartSubscriptionRepository $repoCartSubscription, PaypalService $paypalService)
    {
        $this->environment = $environment;
        $this->em = $em;
        $this->repoCartSubscription = $repoCartSubscription;
        $this->paypalService = $paypalService;
    }

 
    /**
     * Injection de la variable globale carts à Twig
     *
     * @param ControllerEvent $event
     */
    public function onKernelController(ControllerEvent $event) {
        $subscriptionPlans = $this->paypalService->getPlanSubscriptionAfterCondition();

 
        //
        //$this->environment->addGlobal('cart', $this->em->getRepository(Cart::class)->findAll());
        // Injection de la variable cart dans Twig
        $this->environment->addGlobal('subscriptionPlans', $subscriptionPlans);
       // dans une vue twig, on peut faire {{ dump(cart) }}
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