<?php
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/client')]
#[IsGranted('ROLE_USER')]
class ClientController extends AbstractController
{
    #[Route('/dashboard', name: 'app_client_dashboard')]
    public function dashboard(EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        // 1. Get the Client profile associated with this logged-in User account
        $clientProfile = $em->getRepository(\App\Entity\Client::class)->findOneBy(['user' => $user]);

        if (!$clientProfile) {
            // Fallback safety in case the profile row doesn't exist yet
            return $this->render('client/dashboard.html.twig', [
                'user' => $user,
                'jobs_count' => 0,
                'total_spent' => 0,
                'requests' => []
            ]);
        }

        // 2. Count total commands posted by this client profile
        $jobsCount = $em->createQuery(
            'SELECT COUNT(c.id) FROM App\Entity\Command c WHERE c.client = :client'
        )->setParameter('client', $clientProfile)->getSingleScalarResult();

        // 3. Calculate total money spent on Completed commands
        $totalSpent = $em->createQuery(
            'SELECT SUM(c.price) FROM App\Entity\Command c WHERE c.client = :client AND c.status = :status'
        )->setParameters([
            'client' => $clientProfile,
            'status' => 'Completed'
        ])->getSingleScalarResult() ?? 0;

        // 4. Fetch ongoing commands (excluding Completed ones) ordered by newest
        $activeRequests = $em->createQuery(
            'SELECT c FROM App\Entity\Command c 
             WHERE c.client = :client AND c.status != :completedStatus 
             ORDER BY c.createdAt DESC'
        )->setParameters([
            'client' => $clientProfile,
            'completedStatus' => 'Completed'
        ])->getResult();

        return $this->render('client/dashboard.html.twig', [
            'user' => $user,
            'jobs_count' => $jobsCount,
            'total_spent' => $totalSpent,
            'requests' => $activeRequests
        ]);
    }

    #[Route('/professionals', name: 'app_client_professionals')]
    public function browsePros(EntityManagerInterface $em): Response
    {
        // Fetch all provider profiles to display inside the marketplace grid
        $providers = $em->getRepository(\App\Entity\Provider::class)->findAll();

        return $this->render('client/professionals.html.twig', [
            'providers' => $providers
        ]);
    }

    #[Route('/settings', name: 'app_client_settings')]
    public function settings(): Response
    {
        return $this->render('client/settings.html.twig', [
            'user' => $this->getUser()
        ]);
    }
}

?>