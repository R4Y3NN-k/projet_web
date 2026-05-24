<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProviderRepository;
use App\Repository\LocationRepository; // 1. Import your Location Repository
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PageController extends AbstractController
{
    #[Route('/', name: 'app_page')]
    public function index(
        ProviderRepository $providerRepo, 
        CategoryRepository $categoryRepo,
        LocationRepository $locationRepo 
    ): Response {
       
        $providers = $providerRepo->findBy([], ['yearsOfExperience' => 'DESC'], 3);
        $categories = $categoryRepo->findAll();
        
     
        $locations = $locationRepo->findAll(); 

        return $this->render('page/index.html.twig', [
            'providers' => $providers,
            'categories' => $categories,
            'locations' => $locations, 
        ]);
    }
}