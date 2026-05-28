<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Provider;
use App\Entity\Client;
use App\Entity\Command;
use App\Entity\Category;
use App\Entity\Report;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin', name: 'app_admin_')]
class AdminController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(EntityManagerInterface $em): Response
    {
        // Get platform statistics
        $totalUsers = count($em->getRepository(User::class)->findAll());
        $totalProviders = count($em->getRepository(Provider::class)->findAll());
        $totalClients = count($em->getRepository(Client::class)->findAll());
        $totalJobs = count($em->getRepository(Command::class)->findAll());
        
        // Calculate total earnings
        $totalEarnings = 0;
        $completedJobs = $em->getRepository(Command::class)->findBy(['status' => 'Completed']);
        foreach ($completedJobs as $job) {
            $totalEarnings += (float) $job->getPrice();
        }
        
        // Get recent jobs
        $recentJobs = $em->getRepository(Command::class)
            ->findBy([], ['createdAt' => 'DESC'], 10);
        
        // Count jobs by status
        $openJobs = count($em->getRepository(Command::class)->findBy(['status' => 'Open']));
        $assignedJobs = count($em->getRepository(Command::class)->findBy(['status' => 'Assigned']));
        $inProgressJobs = count($em->getRepository(Command::class)->findBy(['status' => 'In Progress']));
        $completedJobsCount = count($completedJobs);
        
        return $this->render('admin/dashboard.html.twig', [
            'totalUsers' => $totalUsers,
            'totalProviders' => $totalProviders,
            'totalClients' => $totalClients,
            'totalJobs' => $totalJobs,
            'totalEarnings' => $totalEarnings,
            'recentJobs' => $recentJobs,
            'openJobs' => $openJobs,
            'assignedJobs' => $assignedJobs,
            'inProgressJobs' => $inProgressJobs,
            'completedJobs' => $completedJobsCount,
        ]);
    }

    #[Route('/users', name: 'users')]
    public function manageUsers(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository(User::class)->findAll();
        
        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/users/{id}', name: 'user_detail')]
    public function userDetail(int $id, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->find($id);
        
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
        
        return $this->render('admin/user_detail.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/users/{id}/suspend', name: 'user_suspend', methods: ['POST'])]
    public function suspendUser(int $id, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->find($id);
        
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
        
        // Add suspend logic here (you might want to add a 'suspended' field to User entity)
        $this->addFlash('success', 'User has been suspended.');
        
        return $this->redirectToRoute('app_admin_user_detail', ['id' => $id]);
    }

    #[Route('/providers', name: 'providers')]
    public function manageProviders(EntityManagerInterface $em): Response
    {
        $providers = $em->getRepository(Provider::class)->findAll();
        
        return $this->render('admin/providers.html.twig', [
            'providers' => $providers,
        ]);
    }

    #[Route('/clients', name: 'clients')]
    public function manageClients(EntityManagerInterface $em): Response
    {
        $clients = $em->getRepository(Client::class)->findAll();
        
        return $this->render('admin/clients.html.twig', [
            'clients' => $clients,
        ]);
    }

    #[Route('/jobs', name: 'jobs')]
    public function manageJobs(EntityManagerInterface $em): Response
    {
        $jobs = $em->getRepository(Command::class)->findBy([], ['createdAt' => 'DESC']);
        
        return $this->render('admin/jobs.html.twig', [
            'jobs' => $jobs,
        ]);
    }

    #[Route('/jobs/{id}', name: 'job_detail')]
    public function jobDetail(int $id, EntityManagerInterface $em): Response
    {
        $job = $em->getRepository(Command::class)->find($id);
        
        if (!$job) {
            throw $this->createNotFoundException('Job not found');
        }
        
        return $this->render('admin/job_detail.html.twig', [
            'job' => $job,
        ]);
    }

    #[Route('/categories', name: 'categories')]
    public function manageCategories(EntityManagerInterface $em): Response
    {
        $categories = $em->getRepository(Category::class)->findAll();
        
        return $this->render('admin/categories.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/settings', name: 'settings')]
    public function settings(): Response
    {
        return $this->render('admin/settings.html.twig');
    }

    #[Route('/reports', name: 'reports')]
    public function reports(EntityManagerInterface $em): Response
    {
        // 1. Fetch the actual report entities from the database sorted by newest first
        $reports = $em->getRepository(Report::class)->findBy([], ['createdAt' => 'DESC']);

        // 2. Pass the defined collection variable safely to your Twig layout
        return $this->render('admin/reports.html.twig', [
            'reports' => $reports,
        ]);
    }
}