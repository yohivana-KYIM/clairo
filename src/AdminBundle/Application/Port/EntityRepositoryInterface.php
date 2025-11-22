<?php

namespace App\AdminBundle\Application\Port;

use App\AdminBundle\Domain\Model\Entity;

interface EntityRepositoryInterface
{
    public function save(Entity $entity): void;
    public function findById(int $id): ?Entity;
    public function findByEntityClassId(string $entityClass, int $id): ?Entity;
    public function findAll(): array;
    public function delete(Entity $entity): void;
    public function findByTenant(string $entityClass, int $tenantId, int $page, int $limit): array;
}
