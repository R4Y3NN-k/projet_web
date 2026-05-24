<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Command;
use App\Entity\Category;
use App\Entity\Provider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ClientController extends AbstractController
{
    #[Route('/client/dashboard', name: 'app_client_dashboard')]
    public function dashboard(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->redirectToRoute('app_login');

        $clientProfile = $em->getRepository(Client::class)->findOneBy(['userAccount' => $user]);

        // Catch the Job Modal Submission
        if ($request->isMethod('POST')) {
            $title = $request->request->get('title');
            $description = $request->request->get('description');
            $price = $request->request->get('price'); // Added price capture!
            $categoryId = $request->request->get('category');

            if ($title && $description && $price && $categoryId) {
                $categoryEntity = $em->getRepository(Category::class)->find($categoryId);

                if ($categoryEntity) {
                    $command = new Command();
                    $command->setTitle($title);
                    $command->setDescription($description);
                    $command->setPrice($price);
                    $command->setStatus('Open');
                    $command->setCreatedAt(new \DateTimeImmutable());
                    $command->setClient($clientProfile);
                    $command->setCategory($categoryEntity);

                    $em->persist($command);
                    $em->flush();

                    return $this->redirectToRoute('app_client_dashboard');
                }
            }
        }

        // Fetch data for the Twig view
        $requests = $em->getRepository(Command::class)->findBy(
            ['client' => $clientProfile],
            ['createdAt' => 'DESC']
        );
        $categories = $em->getRepository(Category::class)->findAll();

        // Calculate Dashboard Stats
        $jobsCount = count($requests);
        $totalSpent = 0;
        foreach ($requests as $req) {
            $totalSpent += (float) $req->getPrice(); // Simple sum of all job prices
        }

        return $this->render('client/dashboard.html.twig', [
            'user' => $user,
            'requests' => $requests,
            'categories' => $categories,
            'jobs_count' => $jobsCount,
            'total_spent' => $totalSpent,
        ]);
    }

    #[Route('/client/professionals', name: 'app_client_professionals')]
    public function browsePros(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->redirectToRoute('app_login');

        // Fetch all providers to display on the Browse Pros page
        $providers = $em->getRepository(Provider::class)->findAll();

        return $this->render('client/professionals.html.twig', [
            'providers' => $providers,
        ]);
    }

    #[Route('/client/settings', name: 'app_client_settings')]
    public function settings(): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->redirectToRoute('app_login');

        return $this->render('client/settings.html.twig', [
            'user' => $user,
        ]);
    }
}