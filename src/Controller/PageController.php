<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProviderRepository;
use App\Repository\LocationRepository; // 1. Keeps your Location Repository import
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PageController extends AbstractController
{
    #[Route('/', name: 'app_page')]
    public function index(
        ProviderRepository $providerRepo, 
        CategoryRepository $categoryRepo,
        LocationRepository $locationRepo // 2. Keeps your dynamic method injection
    ): Response {
        
        // 3. Keeps your teammate's security redirect feature
        if ($this->isGranted('ROLE_PROVIDER')) {
            return $this->redirectToRoute('app_provider_dashboard');
        }

        // Limit to top 3 providers based on experience for the home page showcase
        $providers = $providerRepo->findBy([], ['yearsOfExperience' => 'DESC'], 3);
        $categories = $categoryRepo->findAll();
        
        // 4. Keeps your dynamic location fetching logic
        $locations = $locationRepo->findAll(); 

        return $this->render('page/index.html.twig', [
            'providers' => $providers,
            'categories' => $categories,
            'locations' => $locations, // Safely passed down to Twig
        ]);
    }
}