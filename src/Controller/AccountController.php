<?php

namespace App\Controller;

use App\Repository\FactureAbonnementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    private $repoFactureAbonnement;

    public function __construct(FactureAbonnementRepository $repoFactureAbonnement)
    {
        $this->repoFactureAbonnement = $repoFactureAbonnement;
    }

    /**
     * @Route("/account", name="dashboard")
     */
    public function dashboard(): Response
    {
        $user = $this->getUser();
        $mesFactures = $this->repoFactureAbonnement->findBy(['user' => $user]);

        return $this->render('account/dashboard.html.twig', [
            'mesFactures' => $mesFactures
        ]);
    }
}
