<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Client;
use App\Entity\Provider;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class SignUpController extends AbstractController
{
    #[Route('/SignUp', name: 'app_sign_up', methods: ['GET', 'POST'])]
    public function index(
        Request $request, 
        UserPasswordHasherInterface $hasher, 
        EntityManagerInterface $em
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $confirmPassword = $request->request->get('confirmPassword');

            if ($plainPassword !== $confirmPassword) {
                $this->addFlash('error', 'Passwords do not match.');
                return $this->render('signUp/index.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            $user->setPassword(
                $hasher->hashPassword($user, $plainPassword)
            );

            // Determine User Type from the hidden JS field
            $userType = $request->request->all('registration_form')['userType'] ?? $request->request->get('userType');

            if ($userType === 'provider') {
                $user->setRoles(['ROLE_PROVIDER']);

                $provider = new Provider();
                $provider->setUserAccount($user);
                $provider->setYearsOfExperience((int)$form->get('yearsOfExperience')->getData());
                $provider->setCategory($form->get('serviceCategory')->getData());

                $em->persist($provider);
            } else {
                $user->setRoles(['ROLE_CLIENT']);

                $client = new Client();
                $client->setUserAccount($user);

                $em->persist($client);
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Welcome to Service TN! Your account is ready.');

            return $this->redirectToRoute('app_sign_up'); 
        }

        return $this->render('signUp/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}