<?php
namespace App\AppIntegrationBundle\Infrastructure\Symfony\Service\AutoComplete;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class AutoCompleteService
{
    private iterable $adapters;
    private CacheItemPoolInterface $cache;

    public function __construct(#[AutowireIterator('app.autocomplete.adapter')] iterable $adapters, CacheItemPoolInterface $cache)
    {
        $this->adapters = $adapters;
        $this->cache = $cache;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getSuggestions(string $query, string $source): array
    {
        foreach ($this->adapters as $adapter) {
            if ($adapter->getAdapterName() === $source) {
                return $adapter->getSuggestions($query);
            }
        }

        return [];
    }
}
