<?php

namespace App\Controller;

use App\Entity\Command;
use App\Entity\Provider;
use App\Entity\Category;
use App\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class ProviderController extends AbstractController
{
    #[Route('/provider/dashboard', name: 'app_provider_dashboard')]
    public function dashboard(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            // Log cookies and a small diagnostic entry to help debug session issues
            @file_put_contents(
                __DIR__ . '/../../var/log/provider_debug.log',
                sprintf("%s - anonymous access to provider dashboard. COOKIE=%s\n", (new \DateTime())->format('c'), json_encode($_COOKIE)) ,
                FILE_APPEND
            );

            return $this->redirectToRoute('app_login');
        }

        // Try to find the Provider linked to this user. Use a fallback by id
        // because sometimes the user object from the session can be a proxy
        // or different instance that prevents direct object equality matching.
        $providerRepo = $em->getRepository(Provider::class);
        $provider = $providerRepo->findOneBy(['userAccount' => $user]);

        // Fallback: query by the user's id to avoid object identity/proxy issues
        if (!$provider && $user && method_exists($user, 'getId') && $user->getId()) {
            $qb = $providerRepo->createQueryBuilder('p')
                ->join('p.userAccount', 'u')
                ->andWhere('u.id = :uid')
                ->setParameter('uid', $user->getId())
                ->setMaxResults(1);

            $provider = $qb->getQuery()->getOneOrNullResult();
        }

        if (!$provider) {
            // If the user has provider role, create a minimal Provider profile
            // automatically so the dashboard behaves like the client dashboard
            if (in_array('ROLE_PROVIDER', $user->getRoles(), true) || $this->isGranted('ROLE_PROVIDER')) {
                $provider = new Provider();
                $provider->setUserAccount($user);
                $provider->setYearsOfExperience(0);

                // Attempt to set a default category if one exists (category is non-nullable)
                $defaultCategory = $em->getRepository(Category::class)->findOneBy([]);
                if ($defaultCategory) {
                    $provider->setCategory($defaultCategory);
                }

                $em->persist($provider);
                $em->flush();
                // continue to render dashboard with newly created provider
            } else {
                // Otherwise treat them as not authorized for provider area.
                return $this->redirectToRoute('app_login');
            }
        }

        // Get all jobs assigned to this provider
        $allJobs = $em->getRepository(Command::class)->findBy(['provider' => $provider]);

        // Calculate stats and separate dashboard buckets
        $totalEarned = 0;
        $jobsCompleted = 0;
        $waitingForClientResponse = [];
        $workingJobs = [];

        foreach ($allJobs as $job) {
            if ($job->getStatus() === 'Completed') {
                $totalEarned += (float) $job->getPrice();
                $jobsCompleted++;
            } elseif ($job->getStatus() === 'Assigned') {
                $waitingForClientResponse[] = $job;
            } elseif ($job->getStatus() === 'In Progress') {
                $workingJobs[] = $job;
            }
        }

        return $this->render('provider/dashboard.html.twig', [
            'provider' => $provider,
            'totalEarned' => $totalEarned,
            'jobsCompleted' => $jobsCompleted,
            'waitingForClientResponse' => $waitingForClientResponse,
            'workingJobs' => $workingJobs,
        ]);
    }

    #[Route('/debug/whoami', name: 'app_debug_whoami')]
    public function whoami(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse([
                'user' => null,
                'cookies' => $_COOKIE,
            ]);
        }

        return new JsonResponse([
            'user' => [
                'id' => method_exists($user, 'getId') ? $user->getId() : null,
                'email' => method_exists($user, 'getEmail') ? $user->getEmail() : null,
                'roles' => method_exists($user, 'getRoles') ? $user->getRoles() : null,
            ],
            'cookies' => $_COOKIE,
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

    #[Route('/provider/command/{id}/start', name: 'app_provider_start_work', methods: ['POST'])]
    public function startWork(int $id, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $provider = $em->getRepository(Provider::class)->findOneBy(['userAccount' => $user]);
        $job = $em->getRepository(Command::class)->find($id);

        if ($provider && $job && $job->getProvider() === $provider && $job->getStatus() === 'Assigned') {
            $job->setStatus('In Progress');
            $em->flush();

            $this->addFlash('success', 'Job started. You can now mark it as completed when finished.');
        } else {
            $this->addFlash('error', 'Unable to start work on this job.');
        }

        return $this->redirectToRoute('app_provider_dashboard');
    }

    #[Route('/provider/command/{id}/complete', name: 'app_provider_complete_work', methods: ['POST'])]
    public function completeWork(int $id, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $provider = $em->getRepository(Provider::class)->findOneBy(['userAccount' => $user]);
        $job = $em->getRepository(Command::class)->find($id);

        if ($provider && $job && $job->getProvider() === $provider && $job->getStatus() === 'In Progress') {
            $job->setStatus('Completed');
            $em->flush();

            $this->addFlash('success', 'Work completed successfully and sent for client review.');
        } else {
            $this->addFlash('error', 'Unable to mark this job as completed.');
        }

        return $this->redirectToRoute('app_provider_dashboard');
    }

    #[Route('/provider/settings', name: 'app_provider_settings', methods: ['GET', 'POST'])]
    public function settings(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if (!in_array('ROLE_PROVIDER', $user->getRoles(), true)) {
            return $this->redirectToRoute('app_login');
        }

        $categories = $em->getRepository(Category::class)->findAll();
        $locations = $em->getRepository(Location::class)->findAll();
        $provider = $em->getRepository(Provider::class)->findOneBy(['userAccount' => $user]);

        if (!$provider) {
            $provider = new Provider();
            $provider->setUserAccount($user);
            $provider->setYearsOfExperience(0);
            if (!empty($categories)) {
                $provider->setCategory($categories[0]);
            }
            $em->persist($provider);
            $em->flush();

            $this->addFlash('info', 'Your provider profile was created. Please complete your professional details.');
            return $this->redirectToRoute('app_provider_settings');
        }

        if ($request->isMethod('POST')) {
            $firstName = trim($request->request->get('firstName', ''));
            $lastName = trim($request->request->get('lastName', ''));
            $yearsOfExperience = (int)$request->request->get('yearsOfExperience', $provider->getYearsOfExperience() ?? 0);
            $categoryId = $request->request->get('category');
            $locationId = $request->request->get('location');
            $newPassword = $request->request->get('new_password');
            $confirmPassword = $request->request->get('confirm_password');

            if ($firstName) {
                $user->setFirstName($firstName);
            }
            if ($lastName) {
                $user->setLastName($lastName);
            }

            if ($yearsOfExperience >= 0) {
                $provider->setYearsOfExperience($yearsOfExperience);
            }

            if ($categoryId) {
                $category = $em->getRepository(Category::class)->find($categoryId);
                if ($category) {
                    $provider->setCategory($category);
                }
            }

            if ($locationId) {
                $location = $em->getRepository(Location::class)->find($locationId);
                if ($location) {
                    $user->setLocation($location);
                }
            }

            if (!empty($newPassword)) {
                if ($newPassword !== $confirmPassword) {
                    $this->addFlash('error', 'New passwords do not match.');
                    return $this->redirectToRoute('app_provider_settings');
                }

                if (strlen($newPassword) < 6) {
                    $this->addFlash('error', 'Password must be at least 6 characters long.');
                    return $this->redirectToRoute('app_provider_settings');
                }

                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }

            $em->flush();
            $this->addFlash('success', 'Provider settings saved successfully.');
            return $this->redirectToRoute('app_provider_settings');
        }

        return $this->render('provider/settings.html.twig', [
            'provider' => $provider,
            'categories' => $categories,
            'locations' => $locations,
            'user' => $user,
        ]);
    }
}