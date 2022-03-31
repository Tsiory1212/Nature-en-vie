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

    protected $repoFavoriteCart;

    public function __construct( FavoriteCartRepository $repoFavoriteCart)
    {
        $this->repoFavoriteCart = $repoFavoriteCart;
    }

    /**
     * @Route("/account/dashboard", name="dashboard")
     */
    public function dashboard(DelivryRepository $repoDelivry): Response
    {
        $user = $this->getUser();
        $maLivraison = $repoDelivry->findOneBy(['user' => $user]);
        // $mesFactures = $this->repoFactureAbonnement->findBy(['user' => $user]);
        $mesFavoriteCarts = $this->repoFavoriteCart->findBy(['user' => $user]);
        
        return $this->render('account/dashboard.html.twig', [
            // 'mesFactures' => $mesFactures,
            'mesFavoriteCarts' => $mesFavoriteCarts,
            'maLivraison' => $maLivraison
        ]);
    }
}
