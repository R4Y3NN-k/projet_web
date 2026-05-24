<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProviderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PageController extends AbstractController
{
    #[Route('/', name: 'app_page')]
    public function index(ProviderRepository $providerRepo, CategoryRepository $categoryRepo): Response
    {
        $providers = $providerRepo->findAll();
        $categories = $categoryRepo->findAll();

        return $this->render('page/index.html.twig', [
            'providers' => $providers,
            'categories' => $categories,
        ]);
    }
}
