<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Application\Message;

use App\Entity\User;

/**
 * Message pour déclencher un export massif en file de fond (async).
 */
class BulkExportMessage
{
    public function __construct(
        private string $entityClass,
        private string $format = 'csv',
        private ?User $user = null,
        private ?array $data = [],
        private ?string $notificationChannel = null,
        private ?string $storageDestination = null,
        private ?array $customOptions = [],
        private array $criteria = [],
        private array $sort = [],
    ) {}

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public function getCriteria(): array
    {
        return $this->criteria;
    }

    public function getSort(): array
    {
        return $this->sort;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getNotificationChannel(): ?string
    {
        return $this->notificationChannel;
    }

    public function getStorageDestination(): ?string
    {
        return $this->storageDestination;
    }

    public function getCustomOptions(): array
    {
        return $this->customOptions ?? [];
    }

    public function getData(): ?array
    {
        return $this->data;
    }
}
