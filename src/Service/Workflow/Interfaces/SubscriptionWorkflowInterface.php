<?php

namespace App\Service\Workflow\Interfaces;

use App\Entity\User;

interface SubscriptionWorkflowInterface
{
    public function startSubscription(User $user): void;
    public function validateMail(User $user, bool $isValid): void;
    public function validateUser(User $user, bool $isValid): void;
    public function confirmSubscription(User $user, bool $byUser): void;
}
