<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    /**
     * @Route("/account", name="dashboard")
     */
    public function dashboard(): Response
    {
        return $this->render('account/dashboard.html.twig', []);
    }
}
