<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExploreServiceController extends AbstractController
{
    #[Route('/explore-services', name: 'app_explore_services')]
    public function index(CategoryRepository $categoryRepo): Response
    {
        // Fetch all categories dynamically from your database tables
        $categories = $categoryRepo->findAll();

        // Renders your template passing the real database categories
        return $this->render('explore_service/index.html.twig', [
            'categories' => $categories,
        ]);
    }
}