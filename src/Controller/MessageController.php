<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use App\Service\Workflow\Classes\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/messages')]
class MessageController extends AbstractController
{
    private NotificationService $notificationService;
    private EntityManagerInterface $entityManager;

    public function __construct(NotificationService $notificationService, EntityManagerInterface $entityManager)
    {
        $this->notificationService = $notificationService;
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'messages_list', methods: ['GET'])]
    public function list(MessageRepository $messageRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $messages = $messageRepository->findUserMessages($user);
        $notifications = $messageRepository->findAppNotifications($user);

        // Récupérer uniquement ID et email des utilisateurs
        $users = $entityManager->createQueryBuilder()
            ->select('u.id, u.email')
            ->from(User::class, 'u')
            ->getQuery()
            ->getResult();

        return $this->render('messages/index.html.twig', [
            'messages' => $messages,
            'notifications' => $notifications,
            'users' => $users,
        ]);
    }

    #[Route('/send', name: 'send_message', methods: ['POST'])]
    public function send(Request $request): Response
    {
        $sender = $this->getUser();
        $receiverId = $request->request->get('receiver_id');
        $content = $request->request->get('content');

        $receiver = $this->entityManager->getRepository(User::class)->find($receiverId);
        if (!$receiver) {
            return $this->json(['error' => 'Receiver not found'], Response::HTTP_NOT_FOUND);
        }

        $this->notificationService->sendMessageOrNotification($sender, $receiver, $content);
        return $this->redirectToRoute('messages_list');
    }

    #[Route('/{id}', name: 'message_view', methods: ['GET'])]
    public function view(Message $message): Response
    {
        $user = $this->getUser();
        if ($message->getReceiver() !== $user && $message->getSender() !== $user) {
            throw $this->createAccessDeniedException();
        }

        $message->setIsRead(true);
        $this->entityManager->flush();

        return $this->render('messages/view.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('/notifications/{id}/mark-as-read', name: 'notification_mark_as_read', methods: ['POST', 'GET'])]
    public function markAsRead(Message $message): Response
    {
        if ($message->getReceiver() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $message->setIsRead(true);
        $this->entityManager->flush();

        return $this->redirectToRoute('messages_list');
    }
}
