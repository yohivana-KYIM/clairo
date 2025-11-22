<?php

namespace App\AdminBundle\Application\UseCase;

use App\AdminBundle\Application\Port\EntityServiceInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class GetEntityUseCase
{
    private EntityServiceInterface $entityService;

    public function __construct(EntityServiceInterface $entityService)
    {
        $this->entityService = $entityService;
    }

    /**
     * Retrieve a single entity by ID, with optional access check.
     *
     * @param mixed $id
     * @param bool $checkPermission
     * @param string $permission
     * @return object
     * @throws \RuntimeException|AccessDeniedException
     */
    public function execute(mixed $id, bool $checkPermission = true, string $permission = 'view'): object
    {
        $entity = $this->entityService->findOne($id);

        if (!$entity) {
            throw new \RuntimeException(sprintf(
                'Entity of class %s with ID "%s" not found.',
                $this->entityService->getEntityClass(),
                (string) $id
            ));
        }

        if ($checkPermission && !$this->entityService->isActionAllowed($permission, $entity)) {
            throw new AccessDeniedException("Access denied for action '{$permission}' on this entity.");
        }

        return $entity;
    }
}
