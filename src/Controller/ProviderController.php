<?php

namespace App\Controller;

use App\Entity\Command;
use App\Entity\Provider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProviderController extends AbstractController
{
    #[Route('/provider/dashboard', name: 'app_provider_dashboard')]
    public function dashboard(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->redirectToRoute('app_login');

        $provider = $em->getRepository(Provider::class)->findOneBy(['userAccount' => $user]);
        if (!$provider) return $this->redirectToRoute('app_login');

        // Get all jobs assigned to this provider
         $allJobs = $em->getRepository(Command::class)->findBy(['provider' => $provider]);

        // Calculate stats
        $totalEarned = 0;
        $jobsCompleted = 0;
        $currentGigs = [];

        foreach ($allJobs as $job) {
            if ($job->getStatus() === 'Completed') {
                $totalEarned += (float) $job->getPrice();
                $jobsCompleted++;
            } else {
                $currentGigs[] = $job;
            }
        }

        return $this->render('provider/dashboard.html.twig', [
            'provider' => $provider,
            'totalEarned' => $totalEarned,
            'jobsCompleted' => $jobsCompleted,
            'currentGigs' => $currentGigs,
        ]);
    }
    #[Route('/provider/jobs', name: 'app_provider_jobs')]
    public function jobs(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->redirectToRoute('app_login');

        // Get filter parameters
        $searchQuery = $request->query->get('search', '');
        $locationFilter = $request->query->get('location', '');

        // Fetch all available jobs (Open status)
        $queryBuilder = $em->getRepository(Command::class)->createQueryBuilder('c')
            ->where('c.status = :status')
            ->setParameter('status', 'Open')
            ->orderBy('c.createdAt', 'DESC');

        // Filter by search query (title or description)
        if ($searchQuery) {
            $queryBuilder->andWhere('c.title LIKE :search OR c.description LIKE :search')
                ->setParameter('search', '%' . $searchQuery . '%');
        }

        // Filter by location (through client's user location)
        if ($locationFilter) {
            $queryBuilder->join('c.client', 'client')
                ->join('client.userAccount', 'user')
                ->join('user.location', 'location')
                ->andWhere('location.name = :location')
                ->setParameter('location', $locationFilter);
        }

        $jobs = $queryBuilder->getQuery()->getResult();

        // Get all locations for the dropdown
        $locations = $em->getRepository(\App\Entity\Location::class)->findAll();

        return $this->render('provider/jobs.html.twig', [
            'jobs' => $jobs,
            'locations' => $locations,
            'searchQuery' => $searchQuery,
            'selectedLocation' => $locationFilter,
        ]);
    }

    #[Route('/provider/apply', name: 'app_provider_apply', methods: ['POST'])]
    public function apply(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->redirectToRoute('app_login');

        $providerId = $request->request->get('provider_id');
        $jobId = $request->request->get('job_id');

        if ($providerId && $jobId) {
            $provider = $em->getRepository(Provider::class)->find($providerId);
            $job = $em->getRepository(Command::class)->find($jobId);

            if ($provider && $job && $job->getStatus() === 'Open') {
                // Update the job to assign the provider
                $job->setProvider($provider);
                $job->setStatus('Assigned');

                $em->persist($job);
                $em->flush();

                $this->addFlash('success', 'Application submitted successfully! You can now see this job in your dashboard.');
                return $this->redirectToRoute('app_provider_jobs');
            }
        }

        $this->addFlash('error', 'Failed to apply for job. Please try again.');
        return $this->redirectToRoute('app_provider_jobs');
    }

    #[Route('/provider/settings', name: 'app_provider_settings')]
    public function settings(): Response
    {
        return $this->render('provider/settings.html.twig');
    }
}