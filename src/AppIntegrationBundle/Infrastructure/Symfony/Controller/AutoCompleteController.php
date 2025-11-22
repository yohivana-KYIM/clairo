<?php
namespace App\AppIntegrationBundle\Infrastructure\Symfony\Controller;

use App\AppIntegrationBundle\Infrastructure\Symfony\Service\AutoComplete\AutoCompleteService;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AutoCompleteController extends AbstractController
{
    private AutoCompleteService $autoCompleteService;

    public function __construct(AutoCompleteService $autoCompleteService)
    {
        $this->autoCompleteService = $autoCompleteService;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getSuggestions(Request $request): JsonResponse
    {
        $query = $request->query->get('query');
        $source = $request->query->get('source');

        if (!$query || !$source) {
            return new JsonResponse(['error' => 'Invalid request'], 400);
        }

        $suggestions = $this->autoCompleteService->getSuggestions($query, $source);
        return new JsonResponse($suggestions);
    }
}
