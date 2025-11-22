<?php

namespace App\AppIntegrationBundle\Infrastructure\Symfony\Service\AutoComplete\Clients\Sirene;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Fournit la clé API INSEE pour les appels à l’API Sirene publique (3.11).
 *
 * Mode : “Public”
 * Header attendu : X-INSEE-Api-Key-Integration: <clé fournie sur api.insee.fr>
 */
class InseeApiKeyProvider
{
    public function __construct(
        #[Autowire('%env(INSEE_API_KEY)%')]
        private readonly string $apiKey
    ) {}

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Retourne les headers prêts à l’emploi pour l’appel API.
     */
    public function getDefaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'X-INSEE-Api-Key-Integration' => $this->apiKey,
        ];
    }
}
