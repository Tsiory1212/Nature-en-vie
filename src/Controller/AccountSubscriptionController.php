<?php

namespace App\Controller;

use App\Entity\FactureAbonnement;
use App\Entity\PauseLivraison;
use App\Form\PauseLivraisonType;
use App\Repository\FactureAbonnementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountSubscriptionController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/account/livraison/continue/mais/abonnement/{id}/continue", name="account_delivery_suspend")
     */
    public function account_delivery_suspend($id, Request $request, FactureAbonnementRepository $repoFacture)
    {
        $pauseLivraison = new PauseLivraison();

        /**
         * @var FactureAbonnement $currentFacture 
         */
        $currentFacture = $repoFacture->find($id);

        $form = $this->createForm(PauseLivraisonType::class, $pauseLivraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentFacture->setPauseLivraison($pauseLivraison);
            $pauseLivraison->setFactureAbonnement($currentFacture);
            
            $this->em->persist($pauseLivraison);
            $this->em->flush();
            $this->addFlash(
               'success',
               'Livraison en pause'
            );
            return $this->redirectToRoute('dashboard');
        }
        return $this->render('account/abonnement/pause_livraison.html.twig', [
            'form' => $form->createView()
        ]);
    }

        /**
     * @Route("/account/livraison/continue/abonnement/{id}", name="account_delivery_continue")
     */
    public function account_delivery_continue($id, FactureAbonnementRepository $repoFacture)
    {
        $user = $this->getUser();
       
        /**
         * @var FactureAbonnement $currentFacture 
         */
        $currentFacture = $repoFacture->find($id);
        
        if ($user !== $currentFacture->getUser() ) {
           return $this->redirectToRoute('dashboard');
        }

        // $currentFacture->setPauseLivraison(null);
        
        $this->em->remove($currentFacture->getPauseLivraison());
        $this->em->flush();
        $this->addFlash(
            'success',
            'Livraison continue'
        );
        return $this->redirectToRoute('dashboard');

    }
}
