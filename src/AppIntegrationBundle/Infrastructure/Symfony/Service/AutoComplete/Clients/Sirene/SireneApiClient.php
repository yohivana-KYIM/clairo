<?php
namespace App\AppIntegrationBundle\Infrastructure\Symfony\Service\AutoComplete\Clients\Sirene;

use App\AppIntegrationBundle\Domain\Entity\Company;
use App\AppIntegrationBundle\Domain\Repository\SireneRepositoryInterface;
use App\AppIntegrationBundle\Infrastructure\Symfony\Service\Auth\BearerTokenManager;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SireneApiClient implements SireneRepositoryInterface
{
    private HttpClientInterface $httpClient;
    private BearerTokenManager $tokenManager;
    private string $apiBaseUrl;

    public function __construct(
        HttpClientInterface $httpClient,
        BearerTokenManager $tokenManager,
        string $apiBaseUrl
    ) {
        $this->httpClient = $httpClient;
        $this->tokenManager = $tokenManager;
        $this->apiBaseUrl = $apiBaseUrl;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws InvalidArgumentException
     */
    public function findBySiren(string $siren): ?Company
    {
        $token = $this->tokenManager->getToken();
        $response = $this->httpClient->request('GET', "{$this->apiBaseUrl}/siren/{$siren}", [
            'headers' => [
                'Authorization' => "Bearer {$token}",
                'Accept' => 'application/json',
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        $data = $response->toArray();
        if (!isset($data['siren'])) {
            return null;
        }

        return new Company(
            $data['siren'],
            $data['nom_raison_sociale'] ?? 'Unknown',
            $data['adresse'] ?? null,
            $data['code_activite'] ?? null
        );
    }
}
