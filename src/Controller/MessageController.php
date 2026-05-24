<?php

namespace App\Controller;

use App\Entity\Command;
use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MessageController extends AbstractController
{
    #[Route('/client/chat/{id}', name: 'app_client_chat', methods: ['GET', 'POST'])]
    public function clientChat(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->redirectToRoute('app_login');

        // 1. Find the specific job/command
        $command = $em->getRepository(Command::class)->find($id);
        if (!$command) {
            throw $this->createNotFoundException('Job request not found.');
        }

        // 2. Security Check: Make sure this job actually belongs to the logged-in client
        if ($command->getClient()->getUserAccount() !== $user) {
            throw $this->createAccessDeniedException('You do not have access to this conversation.');
        }

        // 3. Handle sending a new message (Form POST submission)
        if ($request->isMethod('POST')) {
            $content = trim($request->request->get('content'));
            if (!empty($content)) {
                $message = new Message();
                $message->setContent($content);
                $message->setCreatedAt(new \DateTimeImmutable());
                $message->setSender($user);
                $message->setCommand($command);

                $em->persist($message);
                $em->flush();

                // Refresh the page to clear the form input and show the new message
                return $this->redirectToRoute('app_client_chat', ['id' => $id]);
            }
        }

        // 4. Fetch all existing messages for this specific job ordered by time
        $messages = $em->getRepository(Message::class)->findBy(
            ['command' => $command],
            ['createdAt' => 'ASC']
        );

        return $this->render('client/chat.html.twig', [
            'command' => $command,
            'messages' => $messages,
            'user' => $user
        ]);
    }
}