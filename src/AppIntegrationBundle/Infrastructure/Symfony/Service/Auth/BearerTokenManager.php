<?php
namespace App\AppIntegrationBundle\Infrastructure\Symfony\Service\Auth;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BearerTokenManager
{
    private const CACHE_KEY = 'bearer_token';
    private const GRANT_TYPE = 'client_credentials';
    private const TOKEN_EXPIRY_BUFFER = 60;
    private const REQUEST_HEADERS = [
        'Accept' => 'application/json',
    ];

    private HttpClientInterface $httpClient;
    private CacheItemPoolInterface $cache;
    private string $authUrl;
    private string $clientId;
    private string $clientSecret;

    public function __construct(
        HttpClientInterface $httpClient,
        CacheItemPoolInterface $cache,
        string $authUrl,
        string $clientId,
        string $clientSecret
    ) {
        $this->httpClient = $httpClient;
        $this->cache = $cache;
        $this->authUrl = $authUrl;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * @return string
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getToken(): string
    {
        $cacheItem = $this->cache->getItem(self::CACHE_KEY);

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        return $this->fetchNewToken();
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws InvalidArgumentException
     */
    private function fetchNewToken(): string
    {
        $response = $this->httpClient->request('POST', $this->authUrl, [
            'body' => [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => self::GRANT_TYPE,
            ],
            'headers' => self::REQUEST_HEADERS,
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Failed to fetch bearer token.');
        }

        $data = $response->toArray();

        $token = $data['access_token'];
        $expiresIn = $data['expires_in'] ?? 3600;

        $this->storeTokenInCache($token, $expiresIn);

        return $token;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function storeTokenInCache(string $token, int $expiresIn): void
    {
        $cacheItem = $this->cache->getItem(self::CACHE_KEY);
        $cacheItem->set($token);
        $cacheItem->expiresAfter($expiresIn - self::TOKEN_EXPIRY_BUFFER);
        $this->cache->save($cacheItem);
    }
}