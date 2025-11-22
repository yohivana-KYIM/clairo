<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Application\Service\Logging;

use App\AdminBundle\SubBundles\ExportingSubBundle\Infrastructure\Symfony\Entity\ExportLog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class ExportLogger
{
    private EntityManagerInterface $entityManager;
    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function logExport(string $entity, string $format): void
    {
        $user = $this->security->getUser();
        $exportLog = new ExportLog($entity, $format, $user ? $user->getUserIdentifier() : 'Anonymous');

        $this->entityManager->persist($exportLog);
        $this->entityManager->flush();
    }
}
