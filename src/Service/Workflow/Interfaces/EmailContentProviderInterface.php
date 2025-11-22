<?php

namespace App\Service\Workflow\Interfaces;

use App\Entity\User;

interface EmailContentProviderInterface
{
    public function getEmailContent(User $user): string;
}
