<?php

namespace App\AdminBundle\Application\UseCase;

use App\AdminBundle\Application\Port\EntityServiceInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UpdateEntityUseCase
{
    private EntityServiceInterface $entityService;

    public function __construct(EntityServiceInterface $entityService)
    {
        $this->entityService = $entityService;
    }

    /**
     * Update an entity based on input data.
     *
     * @param mixed $id Identifier of the entity to update
     * @param array $data The new data to apply
     * @return object The updated entity
     * @throws \RuntimeException|AccessDeniedException
     */
    public function execute(mixed $id, array $data): object
    {
        $entity = $this->entityService->findOne($id);

        if (!$entity) {
            throw new \RuntimeException(sprintf(
                'Entity of class %s with ID "%s" not found.',
                $this->entityService->getEntityClass(),
                (string) $id
            ));
        }

        if (!$this->entityService->isActionAllowed('edit', $entity)) {
            throw new AccessDeniedException("You are not allowed to edit this entity.");
        }

        // Inject the ID into the data array if needed
        $data['id'] = $id;

        return $this->entityService->saveEntity($data);
    }
}
