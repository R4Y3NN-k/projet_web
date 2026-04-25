<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProviderController extends AbstractController
{
    #[Route('/provider/dashboard', name: 'app_provider_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('provider/dashboard.html.twig');
    }

    #[Route('/provider/jobs', name: 'app_provider_jobs')]
    public function jobs(): Response
    {
        return $this->render('provider/jobs.html.twig');
    }

    #[Route('/provider/settings', name: 'app_provider_settings')]
    public function settings(): Response
    {
        return $this->render('provider/settings.html.twig');
    }
}