<?php

namespace App\Service\EntityManagerServices;

use App\Entity\User;
use App\Entity\Entreprise;
use App\Service\SettingsService;
use App\Service\Workflow\Classes\NotificationService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserManagerService
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly ValidatorInterface $validator,
        private readonly NotificationService $notificationService,
        private readonly SettingsService $settingsService,
        private readonly string $defaultPassword
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function notifyReferent(Entreprise $mere, Entreprise $filiale): void
    {
        $this->notificationService->sendTemplatedEmail(
            from: $this->settingsService->get('system_email'),
            to: $mere->getEmailReferent(),
            subject: 'Nouvelle filiale enregistrée',
            cc: $filiale->getEmailReferent(),
            template: 'filiale/filialeEnregistree.html.twig',
            templateVars: ['filiale' => $filiale, 'mere' => $mere]
        );
    }

    /**
     * Creates or updates users and assigns roles based on emails provided for an Entreprise.
     *
     * @param array $emails [roleKey => email]
     * @param Entreprise $entreprise
     */
    public function manageUsers(array $emails, Entreprise $entreprise): void
    {
        foreach ($emails as $roleKey => $email) {
            if (!$email) {
                continue;
            }

            $this->validateEmail($email);

            $user = $this->findOrCreateUser($email);
            $this->assignRole($user);
            $this->entityManager->persist($user);

            $this->assignEntrepriseRole($entreprise, $roleKey, $email);
        }
    }

    /**
     * Updates users and roles if their emails have changed.
     *
     * @param array $emails [roleKey => email]
     * @param Entreprise $entreprise
     */
    public function updateUsers(array $emails, Entreprise $entreprise): void
    {
        foreach ($emails as $roleKey => $newEmail) {
            if (!$newEmail) {
                continue;
            }

            $this->validateEmail($newEmail);

            $existingEmail = $this->getEntrepriseRoleEmail($entreprise, $roleKey);

            if ($existingEmail !== $newEmail) {
                $this->handleRoleChange($existingEmail, $newEmail, $roleKey, $entreprise);
            }
        }
    }

    /**
     * Validates an email using Symfony's validation component.
     *
     * @param string $email
     */
    private function validateEmail(string $email): void
    {
        $violations = $this->validator->validate($email, [
            new Assert\NotBlank(),
            new Assert\Email(),
        ]);

        if (count($violations) > 0) {
            throw new InvalidArgumentException("Invalid email: $email");
        }
    }

    /**
     * Finds an existing user or creates a new one.
     *
     * @param string $email
     * @return User
     */
    private function findOrCreateUser(string $email): User
    {
        $user = $this->findUserByEmail($email);

        if (!$user) {
            $user = new User();
            $user->setEmail($email);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, $this->defaultPassword));
            $user->setIsVerified(true);
            $user->setCreatedAt(new DateTimeImmutable());
        }

        return $user;
    }

    /**
     * Finds a user by email.
     *
     * @param string $email
     * @return User|null
     */
    private function findUserByEmail(string $email): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
    }

    /**
     * Assigns a role to a user if it is not already assigned.
     *
     * @param User $user
     * @param string|null $role
     */
    private function assignRole(User $user, ?string $role = 'ROLE_REFSECU'): void
    {
        $roles = $user->getRoles();
        if (!in_array($role, $roles, true)) {
            $roles[] = $role;
            $user->setRoles($roles);
        }
    }

    /**
     * Removes a role from a user if it exists.
     *
     * @param User $user
     * @param string|null $role
     */
    private function removeRole(User $user, ?string $role = 'ROLE_REFSECU'): void
    {
        $roles = $user->getRoles();
        if (in_array($role, $roles, true)) {
            $user->setRoles(array_diff($roles, [$role]));
        }
    }

    /**
     * Assigns an email to a specific role in an Entreprise.
     *
     * @param Entreprise $entreprise
     * @param string $roleKey
     * @param string $email
     */
    private function assignEntrepriseRole(Entreprise $entreprise, string $roleKey, string $email): void
    {
        match ($roleKey) {
            'referent' => $entreprise->setEmailReferent($email),
            'suppleant1' => $entreprise->setSuppleant1($email),
            'suppleant2' => $entreprise->setSuppleant2($email),
            default => throw new InvalidArgumentException("Invalid role key: $roleKey"),
        };
    }

    /**
     * Retrieves the email associated with a specific role in an Entreprise.
     *
     * @param Entreprise $entreprise
     * @param string $roleKey
     * @return string|null
     */
    private function getEntrepriseRoleEmail(Entreprise $entreprise, string $roleKey): ?string
    {
        return match ($roleKey) {
            'referent' => $entreprise->getEmailReferent(),
            'suppleant1' => $entreprise->getSuppleant1(),
            'suppleant2' => $entreprise->getSuppleant2(),
            default => throw new InvalidArgumentException("Invalid role key: $roleKey"),
        };
    }

    /**
     * Handles changes to user roles when emails are updated.
     *
     * @param string|null $oldEmail
     * @param string $newEmail
     * @param string $roleKey
     * @param Entreprise $entreprise
     */
    private function handleRoleChange(?string $oldEmail, string $newEmail, string $roleKey, Entreprise $entreprise): void
    {
        // Remove the role from the old user
        if ($oldEmail) {
            $oldUser = $this->findUserByEmail($oldEmail);
            if ($oldUser) {
                $this->removeRole($oldUser);
                $this->entityManager->persist($oldUser);
            }
        }

        // Assign the role to the new user
        $newUser = $this->findOrCreateUser($newEmail);
        $this->assignRole($newUser);
        $this->entityManager->persist($newUser);

        // Update the entreprise with the new email
        $this->assignEntrepriseRole($entreprise, $roleKey, $newEmail);
    }
}
