<?php
namespace App\AppIntegrationBundle\Infrastructure\Symfony\Service\Adapters;

interface AutoCompleteAdapterInterface
{
    public function getSuggestions(string $query): array;
    public function getAdapterName(): string;
}
