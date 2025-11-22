<?php

namespace App\AdminBundle\Infrastructure\Controller;

use App\AdminBundle\Application\Port\EntityRepositoryInterface;
use App\AdminBundle\Application\Port\EntityServiceInterface;
use App\AdminBundle\SubBundles\RenderingBundle\Application\Service\RenderingManager;
use App\AdminBundle\SubBundles\SortingBundle\Application\Service\SortManager;
use App\AdminBundle\SubBundles\PaginationBundle\Application\Service\PaginationManager;
use App\AdminBundle\SubBundles\SearchFilterBundle\Application\Service\SearchFilterManager;
use App\AdminBundle\Application\UseCase\CreateEntityUseCase;
use App\AdminBundle\Application\DTO\EntityDTO;
use App\AdminBundle\Application\UseCase\ListEntitiesUseCase;
use App\AdminBundle\Infrastructure\Symfony\Form\EntityFormType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[Route('/admin')]
class AdminController extends AbstractController
{
    public function __construct(
        private readonly SortManager $sortManager,
        private RenderingManager $renderingManager,
        private PaginationManager $paginationManager,
        private SearchFilterManager $searchFilterManager
    ) {}
    #[Route('/create', name: 'admin_create', methods: ['POST'])]
    public function create(Request $request, CreateEntityUseCase $createEntityUseCase): Response
    {
        $data = json_decode($request->getContent(), true);
        $dto = new EntityDTO($data['name']);
        $entity = $createEntityUseCase->execute($dto);

        return $this->json(['id' => $entity->getId(), 'name' => $entity->getName()]);
    }

    /**
     * @throws Exception
     */
    #[Route('/list/{entity}/{page}', name: 'admin_list', methods: ['GET'])]
    public function listEntities(
        string $entity,
        int $page,
        Request $request,
        ListEntitiesUseCase $listEntitiesUseCase
    ): Response
    {
        // Example: ?sort[name]=ASC&sort[createdAt]=DESC
        $sortColumns = $request->query->all('sort');
        $clickedColumn = $request->query->get('sortColumn');
        $searchTerm = $request->query->get('q');
        $filters = $request->query->all('filters');

        if ($clickedColumn) {
            $sortColumns = $this->sortManager->processSorting($sortColumns, $clickedColumn);
        }

        $result = $listEntitiesUseCase->execute(
            'App\\Entity\\' . ucfirst($entity),
            $page,
            10,
            $sortColumns,
            $filters,
            $searchTerm
        );

        if ($request->isXmlHttpRequest()) {
            return $this->render('@Admin/crud/_list_partial.html.twig', [
                'items' => $result['items'],
            ]);
        }

        return $this->getRenderingManager()->render($request, '@Admin/crud/list.html.twig', [
            'items' => $result['items'],
            'totalItems' => $result['totalItems'],
            'pagesCount' => $result['pagesCount'],
            'currentPage' => $page,
            'sortColumns' => $sortColumns,
        ]);
    }

    #[Route('/edit/{entity}/{id}', name: 'admin_edit', methods: ['GET', 'POST'])]
    public function edit(
        string $entity,
        int $id,
        Request $request,
        EntityRepositoryInterface $repo,
        EntityServiceInterface $entityService
    ): Response {
        $entityClass = 'App\\Entity\\' . ucfirst($entity);
        $entityObject = $repo->findByEntityClassId($entityClass, $id);

        if (!$entityObject) {
            throw $this->createNotFoundException("Entity not found.");
        }

        if (!$this->isGranted('ENTITY_EDIT', $entityObject)) {
            throw $this->createAccessDeniedException();
        }

        // Set entity class dynamically for the form
        $entityService->setEntityClass($entityClass);

        $form = $this->createForm(EntityFormType::class, $entityObject, [
            'entity_service' => $entityService,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityService->saveEntity($request->request->all());

            $this->addFlash('success', ucfirst($entity) . ' updated successfully.');

            return $this->redirectToRoute('admin_crud_index', ['entity' => $entity]);
        }

        return $this->getRenderingManager()->render($request, 'admin/crud/edit.html.twig', [
            'form' => $form->createView(),
            'entity' => $entityObject,
        ]);
    }

    public function getRenderingManager(): RenderingManager
    {
        return $this->renderingManager;
    }

    public function setRenderingManager(RenderingManager $renderingManager): void
    {
        $this->renderingManager = $renderingManager;
    }

    public function getPaginationManager(): PaginationManager
    {
        return $this->paginationManager;
    }

    public function setPaginationManager(PaginationManager $paginationManager): void
    {
        $this->paginationManager = $paginationManager;
    }

    public function getSearchFilterManager(): SearchFilterManager
    {
        return $this->searchFilterManager;
    }

    public function setSearchFilterManager(SearchFilterManager $searchFilterManager): void
    {
        $this->searchFilterManager = $searchFilterManager;
    }
}
