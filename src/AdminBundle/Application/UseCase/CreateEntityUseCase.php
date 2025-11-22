<?php

namespace App\AdminBundle\Application\UseCase;

use App\AdminBundle\Application\DTO\EntityDTO;
use App\AdminBundle\Application\Port\EntityRepositoryInterface;
use App\AdminBundle\Domain\Event\AdminEvent;
use App\AdminBundle\Domain\Model\Entity;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class CreateEntityUseCase
{
    public function __construct(private EntityRepositoryInterface $repository, private EventDispatcherInterface $eventDispatcher) {}

    public function execute(EntityDTO $dto): Entity
    {
        $entity = new Entity($dto->name);
        $this->repository->save($entity);

        $this->eventDispatcher->dispatch(new AdminEvent(get_class($entity), $entity->getId()));

        return $entity;
    }
}
