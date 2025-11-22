<?php

namespace App\AdminBundle\Application\UseCase;

use App\AdminBundle\Application\Port\EntityServiceInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DeleteEntityUseCase
{
    private EntityServiceInterface $entityService;

    public function __construct(EntityServiceInterface $entityService)
    {
        $this->entityService = $entityService;
    }

    /**
     * Handles deletion of an entity by its ID.
     *
     * @param mixed $id
     * @throws \RuntimeException|AccessDeniedException
     */
    public function execute(mixed $id): void
    {
        $entity = $this->entityService->findOne($id);

        if (!$entity) {
            throw new \RuntimeException(sprintf(
                'Entity of class %s with ID "%s" not found.',
                $this->entityService->getEntityClass(),
                (string) $id
            ));
        }

        if (!$this->entityService->isActionAllowed('delete', $entity)) {
            throw new AccessDeniedException('You are not allowed to delete this entity.');
        }

        $this->entityService->deleteEntity($id);
    }
}
