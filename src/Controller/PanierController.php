<?php

namespace App\Controller;

use App\Service\Panier\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/account")
 */

class PanierController extends AbstractController
{

    /**
     * @Route("/panier", name="panier")
     */
    public function panier(PanierService $panierService)
    {
        return $this->render('/account/panier/panier.html.twig', [
            'items' => $panierService->getFullcart(),
            'total' => $panierService->getTotal()
        ]);
    }

    /**
     * @Route("/panier/add/{id}", name="panier_add")
     */
    public function panier_add($id, PanierService $panierService)
    {
        $panierService->add($id);
        
        return $this->redirectToRoute("home");
    }

    /**
     * @Route("/panier/remove/{id}", name="panier_remove")
     */
    public function panier_remove($id, PanierService $panierService )
    {
        $panierService->remove($id);
        return $this->redirectToRoute("panier");
    }


    /**
     * @Route("/panier/add/one/{id}", name="panier_add_one")
     */
    public function panier_add_one($id, PanierService $panierService)
    {
        $panierService->addOne($id);
        return $this->redirectToRoute("panier");
    }

    /**
     * @Route("/panier/remove/one/{id}", name="panier_remove_one")
     */
    public function panier_remove_one($id, PanierService $panierService)
    {
        $panierService->removeOne($id);
        return $this->redirectToRoute("panier");
    }

    
    /**
     * @Route("/panier/delete/all", name="panier_delete_all")
     */
    public function panier_delete_all(PanierService $panierService)
    {
        $panierService->deleteAll();
        return $this->redirectToRoute("panier");
    }

}
