<?php

namespace App\AdminBundle\Domain\Service;

use App\AdminBundle\Application\Port\EntityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EntityService implements EntityServiceInterface
{
    protected string $entityClass;

    public function __construct(
        protected EntityManagerInterface $em,
        protected AuthorizationCheckerInterface $auth,
        protected PropertyAccessorInterface $accessor,
        protected SerializerInterface $serializer,
    ) {}

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public function findEntities(array $criteria = [], array $sort = [], int $page = 1, int $limit = 50): iterable
    {
        $repo = $this->getRepository();

        return $repo->findBy(
            $criteria,
            $sort,
            $limit,
            ($page - 1) * $limit
        );
    }

    public function findOne(mixed $id): ?object
    {
        return $this->getRepository()->find($id);
    }

    public function saveEntity(array $data): object
    {
        $id = $data['id'] ?? null;
        $entity = $id ? $this->findOne($id) : new ($this->entityClass)();

        if (!$entity) {
            throw new NotFoundHttpException("Entity not found");
        }

        foreach ($data as $field => $value) {
            if ($this->accessor->isWritable($entity, $field)) {
                $this->accessor->setValue($entity, $field, $value);
            }
        }

        $this->em->persist($entity);
        $this->em->flush();

        return $entity;
    }

    public function deleteEntity(mixed $id): void
    {
        $entity = $this->findOne($id);
        if (!$entity) {
            throw new NotFoundHttpException("Entity not found");
        }

        $this->em->remove($entity);
        $this->em->flush();
    }

    public function count(array $criteria = []): int
    {
        return $this->getRepository()->count($criteria);
    }

    public function getSearchableFields(): array
    {
        // Can be extracted from metadata or configured elsewhere
        return ['name', 'email', 'title'];
    }

    public function getSortableFields(): array
    {
        // Static for now, can be dynamic via metadata
        return ['id' => 'asc', 'createdAt' => 'desc'];
    }

    public function getExportableFields(): array
    {
        return ['id', 'name', 'email', 'createdAt'];
    }

    public function getExportData(array $criteria = [], array $sort = []): iterable
    {
        return $this->findEntities($criteria, $sort, 1, 9999); // Full export without pagination
    }

    public function isActionAllowed(string $action, ?object $entity = null): bool
    {
        $subject = $entity ?? $this->entityClass;
        return $this->auth->isGranted($action, $subject);
    }

    protected function getRepository(): ObjectRepository
    {
        return $this->em->getRepository($this->entityClass);
    }

    /**
     * Allows late injection of entity class name.
     */
    public function setEntityClass(string $class): void
    {
        $this->entityClass = $class;
    }
}
