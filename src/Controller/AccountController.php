<?php

namespace App\Controller;

use App\Repository\DelivryRepository;
use App\Repository\FactureAbonnementRepository;
use App\Repository\FavoriteCartRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    private $repoFactureAbonnement;

    protected $repoFavoriteCart;

    public function __construct(FactureAbonnementRepository $repoFactureAbonnement, FavoriteCartRepository $repoFavoriteCart)
    {
        $this->repoFactureAbonnement = $repoFactureAbonnement;
        $this->repoFavoriteCart = $repoFavoriteCart;
    }

    /**
     * @Route("/account/dashboard", name="dashboard")
     */
    public function dashboard(DelivryRepository $repoDelivry): Response
    {
        $user = $this->getUser();
        $maLivraison = $repoDelivry->findOneBy(['user' => $user]);
        $mesFactures = $this->repoFactureAbonnement->findBy(['user' => $user]);
        $mesFavoriteCarts = $this->repoFavoriteCart->findBy(['user' => $user]);
    
        return $this->render('account/dashboard.html.twig', [
            'mesFactures' => $mesFactures,
            'mesFavoriteCarts' => $mesFavoriteCarts,
            'maLivraison' => $maLivraison
        ]);
    }
}
