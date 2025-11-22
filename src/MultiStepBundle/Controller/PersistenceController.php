<?php

namespace App\MultiStepBundle\Controller;

use App\MultiStepBundle\Persistence\Helper\MicroSesamePersistenceHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PersistenceController extends AbstractController
{
    #[Route('/items', name: 'items_index', methods: ['GET'])]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        // Just render the Twig shell — the JS will call /items/data
        return $this->render('@MultiStepBundle/items/index.html.twig');
    }

    #[Route('/items/data', name: 'items_data', methods: ['GET'])]
    public function data(Request $request, MicroSesamePersistenceHelper $helper): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        // --- 1) Read & normalize query params ---
        // page & limit
        $page  = max(1, (int)$request->query->get('page', 1));
        $limit = max(1, (int)$request->query->get('limit', 10));

        // compute offset for API
        $offset = ($page - 1) * $limit;

        // fields: comma-separated or array
        $fieldsParam = $request->query->all('fields');
        $fields = is_array($fieldsParam)
            ? $fieldsParam
            : (strlen((string)$fieldsParam) ? explode(',', (string)$fieldsParam) : []);

        // filters: expect filter[field]=value or filter[field][operator]=value
        $rawFilters = $request->query->all('filter');
        $filters = [];
        foreach ($rawFilters as $field => $spec) {
            if (is_array($spec) && isset($spec['value'])) {
                $filters[$field] = [
                    'value'    => $spec['value'],
                    'operator' => $spec['operator'] ?? 'eq',
                ];
            } elseif ($spec !== '') {
                $filters[$field] = $spec;
            }
        }

        // sorts: sort=firstName:asc,lastName:desc or sort[]=field:dir
        $rawSort = $request->query->get('sort', '');
        $sorts   = [];
        $sortItems = is_array($rawSort)
            ? $rawSort
            : explode(',', (string)$rawSort);
        foreach ($sortItems as $s) {
            if (trim($s) === '') {
                continue;
            }
            // “field:direction”
            [$field, $dir] = array_pad(explode(':', $s, 2), 2, 'asc');
            $sorts[ $field ] = $dir;
        }

        // prettyPrint?
        $pretty = filter_var($request->query->get('pretty'), FILTER_VALIDATE_BOOLEAN);

        // --- 2) Delegate to helper ---
        $helper
            ->setResource('items'); // your API resource

        $data = $helper->findByConditions(
            $filters,    // array of [field => value or [value, operator]]
            $sorts,      // array of [field => 'asc'|'desc']
            $limit,      // page size
            $offset,     // offset
            $fields ?: null,
            $pretty
        );

        // Optionally, if your API returns total count in headers or payload,
        // extract it here. For demo assume it’s part of the response:
        $total = $data['meta']['total'] ?? count($data['items'] ?? $data);

        // --- 3) Build & return JSON ---
        return new JsonResponse([
            'data' => $data['items'] ?? $data,
            'meta' => [
                'total' => $total,
                'page'  => $page,
                'limit' => $limit,
            ],
        ]);
    }
}
