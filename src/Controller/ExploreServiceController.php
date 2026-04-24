<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ExploreServiceController extends AbstractController
{
    #[Route('/explore/service', name: 'app_explore_service')]
    public function index(): Response
    {
        return $this->render('explore_service/index.html.twig', [
            'controller_name' => 'ExploreServiceController',
        ]);
    }
}
