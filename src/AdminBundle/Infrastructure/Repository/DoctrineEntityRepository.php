<?php

namespace App\AdminBundle\Infrastructure\Repository;

use App\AdminBundle\Application\Port\EntityRepositoryInterface;
use App\AdminBundle\Domain\Model\Entity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;

class DoctrineEntityRepository implements EntityRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function save(Entity $entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function findById(int $id): ?Entity
    {
        return $this->em->find(Entity::class, $id);
    }

    public function findByEntityClassId(string $entityClass, int $id): ?Entity
    {
        return $this->em->find($entityClass, $id);
    }

    public function findAll(): array
    {
        return $this->em->getRepository(Entity::class)->findAll();
    }

    public function delete(Entity $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /**
     * @throws Exception
     */
    public function findPaginated(
        string $entityClass,
        int $page,
        int $limit = 10,
        array $sortColumns = []
    ): array {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('e')
            ->from($entityClass, 'e')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        foreach ($sortColumns as $column => $direction) {
            if (in_array(strtoupper($direction), ['ASC', 'DESC'])) {
                $queryBuilder->addOrderBy("e.$column", $direction);
            }
        }

        $query = $queryBuilder->getQuery();
        $paginator = new Paginator($query);

        return [
            'items' => $paginator->getIterator(),
            'totalItems' => count($paginator),
            'pagesCount' => ceil(count($paginator) / $limit),
        ];
    }

    public function findByTenant(string $entityClass, int $tenantId, int $page, int $limit): array
    {
        $query = $this->em->createQueryBuilder()
            ->select('e')
            ->from($entityClass, 'e')
            ->where('e.tenantId = :tenantId')
            ->setParameter('tenantId', $tenantId)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery();

        $paginator = new Paginator($query);

        return [
            'items' => $paginator->getIterator(),
            'totalItems' => count($paginator),
            'pagesCount' => ceil(count($paginator) / $limit),
        ];
    }
}
