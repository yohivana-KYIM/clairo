<?php

namespace App\AdminBundle\SubBundles\SearchFilterBundle\Infrastructure\Symfony\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\AdminBundle\Application\Port\EntityServiceInterface;

/**
 * Endpoint générique pour la recherche + filtres sur n’importe quelle entité.
 */
#[Route('/admin/search')]
class SearchFilterController extends AbstractController
{
    public function __construct(
        private readonly EntityServiceInterface $entityService
    ) {}

    #[Route('/{entity}', name: 'admin_search_filter', methods: ['GET'])]
    public function search(Request $request, string $entity): JsonResponse
    {
        $entityClass = 'App\\Entity\\' . ucfirst($entity);

        // Inject dynamiquement la classe de l'entité
        $this->entityService->setEntityClass($entityClass);

        $filters = $request->query->all();
        $sort = $filters['sort'] ?? [];
        $page = (int)($filters['page'] ?? 1);
        $limit = (int)($filters['limit'] ?? 20);

        unset($filters['sort'], $filters['page'], $filters['limit']);

        $items = $this->entityService->findEntities($filters, $sort, $page, $limit);
        $total = $this->entityService->count($filters);

        return $this->json([
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
        ]);
    }
}
