<?php

namespace App\Service\User;

use App\Entity\User;
use App\Service\Workflow\Classes\SubscriptionWorkflowService;
use App\Service\Email\EmailVerificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationService
{
    private readonly EntityManagerInterface $entityManager;
    private readonly UserPasswordHasherInterface $passwordHasher;
    private readonly SubscriptionWorkflowService $subscriptionWorkflowService;
    private readonly EmailVerificationService $emailVerificationService;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        SubscriptionWorkflowService $subscriptionWorkflowService,
        EmailVerificationService $emailVerificationService
    ) {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->subscriptionWorkflowService = $subscriptionWorkflowService;
        $this->emailVerificationService = $emailVerificationService;
    }

    public function registerUser(User $user, FormInterface $form): void
    {
        $this->subscriptionWorkflowService->startSubscription($user);

        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $form->get('password')->getData()
            )
        );
        $user->setTrustedTokenVersion(1);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->emailVerificationService->sendVerificationEmail($user);
    }
}
