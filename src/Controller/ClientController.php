<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    #[Route('/client/dashboard', name: 'app_client_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('client/dashboard.html.twig');
    }

    #[Route('/client/professionals', name: 'app_client_professionals')]
    public function professionals(): Response
    {
        return $this->render('client/professionals.html.twig');
    }

    #[Route('/client/settings', name: 'app_client_settings')]
    public function settings(): Response
    {
        return $this->render('client/settings.html.twig');
    }
}