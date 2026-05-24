<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Command;
use App\Entity\Category;
use App\Entity\Provider;
use App\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ClientController extends AbstractController
{
    #[Route('/client/dashboard', name: 'app_client_dashboard')]
    public function dashboard(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // If the logged-in user is a provider, send them to the provider dashboard.
        if ($this->isGranted('ROLE_PROVIDER')) {
            return $this->redirectToRoute('app_provider_dashboard');
        }

        $clientProfile = $this->resolveClientProfile($user, $em);
        if (!$clientProfile) {
            return $this->redirectToRoute('app_login');
        }

        // Handle Posting a New Job Form Submission
        if ($request->isMethod('POST')) {
            $title = $request->request->get('title');
            $description = $request->request->get('description');
            $price = $request->request->get('price');
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

        // Fetch All Requests for History & Stat tracking
        $allRequests = $em->getRepository(Command::class)->findBy(
            ['client' => $clientProfile],
            ['createdAt' => 'DESC']
        );
        $categories = $em->getRepository(Category::class)->findAll();

        // Separate out only your ongoing active requests
        $activeRequests = array_filter($allRequests, function($cmd) {
            return in_array($cmd->getStatus(), ['Open', 'In Progress']);
        });

        // Calculate Dashboard Stats based on active or completed counts safely
        $jobsCount = count($allRequests);
        $totalSpent = 0;
        foreach ($allRequests as $req) {
            if ($req->getStatus() !== 'Cancelled') {
                $totalSpent += (float) $req->getPrice();
            }
        }

        return $this->render('client/dashboard.html.twig', [
            'user' => $user,
            'active_requests' => $activeRequests,
            'all_requests' => $allRequests,
            'categories' => $categories,
            'jobs_count' => $jobsCount,
            'total_spent' => $totalSpent,
        ]);
    }

    #[Route('/client/command/{id}/cancel', name: 'app_client_command_cancel', methods: ['POST'])]
    public function cancelCommand(int $id, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->isGranted('ROLE_PROVIDER')) {
            return $this->redirectToRoute('app_provider_dashboard');
        }

        $clientProfile = $this->resolveClientProfile($user, $em);
        if (!$clientProfile) {
            return $this->redirectToRoute('app_login');
        }

        $command = $em->getRepository(Command::class)->find($id);

        // Security check: Make sure this command belongs to the currently logged-in client
        if ($command && $command->getClient() === $clientProfile && in_array($command->getStatus(), ['Open', 'In Progress'])) {
            $command->setStatus('Cancelled');
            $em->flush();
        }

        return $this->redirectToRoute('app_client_dashboard');
    }

    #[Route('/client/professionals', name: 'app_client_professionals')]
    public function browsePros(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->isGranted('ROLE_PROVIDER')) {
            return $this->redirectToRoute('app_provider_dashboard');
        }

        $clientProfile = $this->resolveClientProfile($user, $em);
        if (!$clientProfile) {
            return $this->redirectToRoute('app_login');
        }

        // Capture query string parameter values sent from search forms
        $searchQuery = $request->query->get('query');
        $locationFilter = $request->query->get('location');
        $categoryFilter = $request->query->get('category');

        // Build a dynamic Query Builder to filter our provider records matching criteria
        $qb = $em->getRepository(Provider::class)->createQueryBuilder('p')
            ->join('p.userAccount', 'u'); // Join the base User entity to search user properties

        if ($searchQuery) {
            // FIXED: Only search fields that actually exist on the userAccount mapping properties
            $qb->andWhere('u.firstName LIKE :query OR u.lastName LIKE :query')
               ->setParameter('query', '%' . $searchQuery . '%');
        }

        if ($locationFilter) {
            // If location filter parameters arrive as strings or IDs, link to the relation mapping field
            $qb->andWhere('u.location = :location')
               ->setParameter('location', $locationFilter);
        }

        if ($categoryFilter) {
            $qb->andWhere('p.category = :category')
               ->setParameter('category', $categoryFilter);
        }

        $providers = $qb->getQuery()->getResult();
        $categories = $em->getRepository(Category::class)->findAll();

        return $this->render('client/professionals.html.twig', [
            'providers' => $providers,
            'categories' => $categories,
            'current_query' => $searchQuery,
            'current_location' => $locationFilter,
            'current_category' => $categoryFilter,
        ]);
    }

    #[Route('/client/settings', name: 'app_client_settings', methods: ['GET', 'POST'])]
    public function settings(
        Request $request, 
        EntityManagerInterface $em, 
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $this->getUser();
        if (!$user) return $this->redirectToRoute('app_login');

        // Handle Form Submission Updates
        if ($request->isMethod('POST')) {
            $firstName = $request->request->get('firstName');
            $lastName = $request->request->get('lastName');
            $locationName = $request->request->get('location');
            $newPassword = $request->request->get('new_password');
            $confirmPassword = $request->request->get('confirm_password');

            // 1. Update Basic Text Fields
            if ($firstName) $user->setFirstName($firstName);
            if ($lastName) $user->setLastName($lastName);
            
            // FIXED: Fetch the Location Entity instead of passing a raw input string to setLocation()
            if ($locationName) {
                $locationEntity = $em->getRepository(Location::class)->findOneBy(['name' => $locationName]);
                
                if ($locationEntity) {
                    $user->setLocation($locationEntity);
                } else {
                    $this->addFlash('error', 'The specified region could not be matched with our registry records.');
                    return $this->redirectToRoute('app_client_settings');
                }
            }

            // 2. Process Password Change if requested
            if (!empty($newPassword)) {
                if ($newPassword !== $confirmPassword) {
                    $this->addFlash('error', 'New passwords do not match!');
                    return $this->redirectToRoute('app_client_settings');
                }
                
                if (strlen($newPassword) < 6) {
                    $this->addFlash('error', 'Password must be at least 6 characters long.');
                    return $this->redirectToRoute('app_client_settings');
                }

                // Hash the plain password securely before updating database
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }

            // Flush changes down to MySQL database via Doctrine
            $em->flush();

            $this->addFlash('success', 'Your account settings have been updated successfully.');
            return $this->redirectToRoute('app_client_settings');
        }

        return $this->render('client/settings.html.twig', [
            'user' => $user,
        ]);
    }

    private function resolveClientProfile($user, EntityManagerInterface $em): ?Client
    {
        $roles = $user->getRoles();
        $isClient = in_array('ROLE_CLIENT', $roles, true);
        $isProvider = in_array('ROLE_PROVIDER', $roles, true);

        if (!$isClient) {
            // Don't add a flash here to avoid repeated messages on every page.
            // The callers will handle redirecting provider users to the provider dashboard.
            return null;
        }

        $clientProfile = $em->getRepository(Client::class)->findOneBy(['userAccount' => $user]);
        if (!$clientProfile) {
            $clientProfile = new Client();
            $clientProfile->setUserAccount($user);
            $em->persist($clientProfile);
            $em->flush();
        }

        return $clientProfile;
    }
}