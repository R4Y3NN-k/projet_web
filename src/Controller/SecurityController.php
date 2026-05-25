<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request; // <-- Imported Request here
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        if ($request->isXmlHttpRequest() || str_contains($request->headers->get('Content-Type', ''), 'application/json')) {
            $error = $authenticationUtils->getLastAuthenticationError();
            if ($error) {
                return $this->json(['error' => $error->getMessageKey()]);
            }
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/post-login', name: 'app_post_login')]
    public function postLogin(): RedirectResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_page');
        }

        // Send admins to admin dashboard
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_admin_dashboard');
        }

        // Send providers to provider dashboard, clients to client dashboard, others to homepage
        if ($this->isGranted('ROLE_PROVIDER')) {
            return $this->redirectToRoute('app_provider_dashboard');
        }

        if ($this->isGranted('ROLE_CLIENT')) {
            return $this->redirectToRoute('app_client_dashboard');
        }

        return $this->redirectToRoute('app_page');
    }
}