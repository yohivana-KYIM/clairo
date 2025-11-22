<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SystemUserFixture extends Fixture implements FixtureGroupInterface
{
    public function __construct(private readonly UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        // Consistent identity you can target in code/tests
        $first = 'System';
        $last  = 'Account';
        $email = 'cleo@fluxel.fr';

        $user = new User();
        $user->setEmail($email);

        $user->setRoles(['ROLE_SYSTEM']);

        $plain = $_ENV['APP_SYSTEM_USER_PASSWORD'] ?? 'System#' . bin2hex(random_bytes(8));
        $user->setPassword($this->hasher->hashPassword($user, $plain));

        if (method_exists($user, 'setFirstName')) $user->setFirstName($first);
        if (method_exists($user, 'setFirstname')) $user->setFirstname($first);
        if (method_exists($user, 'setLastName'))  $user->setLastName($last);
        if (method_exists($user, 'setLastname'))  $user->setLastname($last);
        if (!method_exists($user, 'setFirstName') && !method_exists($user, 'setFirstname') && method_exists($user, 'setName')) {
            $user->setName($first.' '.$last);
        }

        // Harden the account if your entity supports these flags
        if (method_exists($user, 'setIsVerified'))          $user->setIsVerified(true);
        if (method_exists($user, 'setIsReferentVerified'))  $user->setIsReferentVerified(true);
        if (method_exists($user, 'setCreatedAt'))           $user->setCreatedAt(new \DateTimeImmutable('now'));
        if (method_exists($user, 'setStatus'))              $user->setStatus('system');
        if (method_exists($user, 'setEnabled'))             $user->setEnabled(false);
        if (method_exists($user, 'setLocked'))              $user->setLocked(true);
        if (method_exists($user, 'setMfaStrategies'))       $user->setMfaStrategies([]);

        $manager->persist($user);
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['system-user', 'dev', 'prod', 'demo'];
    }
}
