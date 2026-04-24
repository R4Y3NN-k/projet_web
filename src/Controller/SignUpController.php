<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SignUpController extends AbstractController
{
    #[Route('/SignUp', name: 'app_sign_up')]
    public function index(): Response
    {
        return $this->render('signUp/index.html.twig', [
            'controller_name' => 'SignUpController',
        ]);
    }
}
