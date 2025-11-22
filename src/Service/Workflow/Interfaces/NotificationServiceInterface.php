<?php
namespace App\Service\Workflow\Interfaces;
use App\Entity\User;
use Symfony\Component\Mime\Address;

interface NotificationServiceInterface
{
    public function sendEmailNotification(User $user): void;
    public function sendUINotification(User $user): void;
    public function sendTemplatedEmail(string|Address $from, string $to, string $subject, ?string $cc, ?string $template, ?array $templateVars): void;
}