<?php

namespace App\Controller;

use App\Entity\Delivry;
use App\Entity\Order;
use App\Entity\User;
use App\Form\OrderDelivryType;
use App\Repository\OrderRepository;
use App\Repository\PauseDelivryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountDelivryController extends AbstractController
{
    private $repoOrder;
    private $em;

    public function __construct(OrderRepository $repoOrder, EntityManagerInterface $em)
    {
        $this->repoOrder = $repoOrder;
        $this->em = $em;
    }

    

    /**
     * @Route("/account/delivry/edit", name="account_delivry_edit")
     */
    public function account_delivry_edit(Request $request): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $delivry = $user->getDelivry();
        if ($delivry == null) {
            $delivry = new Delivry();
        }
        
        $ordersNotDelivred = $this->repoOrder->findBy(['user' => $user, 'status_delivry' => 0]);
        if (count($user->getOrders()) == 0) {
           $nbrOrdersNotDelivred = 0;
        } else {
            $nbrOrdersNotDelivred = count($ordersNotDelivred);
        }
        
        $formDelivry = $this->createForm(OrderDelivryType::class, $delivry);
        $formDelivry->handleRequest($request);
        if ($formDelivry->isSubmitted() && $formDelivry->isValid()) {
            $delivry->setUser($this->getUser());

            $this->em->persist($delivry);
            $this->em->flush();

            // if ( $this->session->get('panier')) {
            //     return $this->redirectToRoute('account_order_step_two');
            // } else {
                return $this->redirectToRoute('dashboard');
            // }

            $this->em->persist($delivry);
            $this->em->flush();
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('account/delivry/create_delivry.html.twig', [
            'formDelivry' => $formDelivry->createView(),
            'delivry' => $delivry,
            'ordersNotDelivred' => $ordersNotDelivred,
            'nbrOrdersNotDelivred' => $nbrOrdersNotDelivred
        ]);
    }




    /**
     * @Route("/account/plan/commande/{id}/delivry/continue", name="account_plan_delivry_continue")
     */
    public function account_plan_delivry_continue($id, OrderRepository $repoOrder, PauseDelivryRepository $repoPause)
    {
        $user = $this->getUser();
       
        $currentOrder = $repoOrder->find($id);
        
        if ($user !== $currentOrder->getUser() ) {
           return $this->redirectToRoute('dashboard');
        };

        $this->em->remove($currentOrder->getPauseDelivry());
        $this->em->flush();
        $this->addFlash(
            'success',
            'Votre livraison continue'
        );
        return $this->redirectToRoute('account_order_plan_show_detail', ['orderId' => $currentOrder->getId()]);

    }
}
