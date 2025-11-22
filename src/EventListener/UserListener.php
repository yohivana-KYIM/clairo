<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\Security\EncryptionService;

class UserListener
{

    public function __construct(private readonly EncryptionService $encryptionService)
    {
    }

    public function prePersist(User $user): void
    {
        $this->encryptPasswordHistory($user);
    }

    public function preUpdate(User $user, PreUpdateEventArgs $event): void
    {
        if ($event->hasChangedField('password')) {
            $this->updatePasswordHistory($user, $event);
        }

        $this->encryptPasswordHistory($user);
    }

    public function postLoad(User $user): void
    {
        $this->decryptPasswordHistory($user);
    }

    private function updatePasswordHistory(User $user, PreUpdateEventArgs $event): void
    {
        $oldPassword = $event->getOldValue('password');
        $newPassword = $event->getNewValue('password');

        // Add the old password to the history
        $passwordHistory = $user->getPasswordHistory() ?? [];
        $passwordHistory[] = $oldPassword;

        // Keep only the last 5 passwords
        $passwordHistory = array_slice($passwordHistory, -5);

        // Update the user entity
        $user->setPasswordHistory($passwordHistory);
        $user->setPassword($newPassword);
    }

    private function encryptPasswordHistory(User $user): void
    {
        if ($user->getPasswordHistory() !== null) {
            $encryptedHistory = $this->encryptionService->encrypt(json_encode($user->getPasswordHistory()));
            $user->setPasswordHistory([$encryptedHistory]);
        }
    }

    private function decryptPasswordHistory(User $user): void
    {
        if ($user->getPasswordHistory() !== null) {
            $decryptedHistory = json_decode(
                $this->encryptionService->decrypt($user->getPasswordHistory()[0]),
                true
            );
            $user->setPasswordHistory($decryptedHistory);
        }
    }
}
