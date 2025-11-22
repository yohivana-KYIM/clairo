<?php

namespace App\AdminBundle\Application\UseCase;

use App\AdminBundle\Application\Port\EntityRepositoryInterface;
use App\AdminBundle\Infrastructure\Config\AdminConfig;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Exception;

class ListEntitiesUseCase
{
    public function __construct(
        private readonly EntityRepositoryInterface $repository,
        private readonly AdminConfig $config
    ) {}

    /**
     * Lists paginated entities with filters and sorting strategy applied.
     *
     * @param string $entityClass
     * @param int $page
     * @param int $limit
     * @param array $sortColumns       // e.g. ['name' => 'asc', 'createdAt' => 'desc']
     * @param array $filters           // e.g. ['status' => 'active']
     * @param string|null $search      // full-text search string
     *
     * @return array{
     *     data: iterable<object>,
     *     total: int,
     *     page: int,
     *     limit: int,
     *     pages: int
     * }
     *
     * @throws Exception
     */
    public function execute(
        string $entityClass,
        int $page = 1,
        int $limit = 10,
        array $sortColumns = [],
        array $filters = [],
        ?string $search = null
    ): array {
        // Handle sorting strategy
        if ($this->config->getSortingStrategy() === 'single' && count($sortColumns) > 1) {
            $lastColumn = array_key_last($sortColumns);
            $sortColumns = [$lastColumn => $sortColumns[$lastColumn]];
        }

        // Count total items before pagination
        $total = $this->repository->count($entityClass, $filters, $search);

        // Get paginated result
        $items = $this->repository->findPaginated(
            entityClass: $entityClass,
            page: $page,
            limit: $limit,
            sortColumns: $sortColumns,
            /*filters: $filters,
            search: $search*/
        );

        return [
            'data' => $items,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => (int) ceil($total / $limit),
        ];
    }
}
