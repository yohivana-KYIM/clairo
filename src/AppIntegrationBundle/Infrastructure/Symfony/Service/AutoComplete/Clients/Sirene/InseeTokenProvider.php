<?php

namespace App\AppIntegrationBundle\Infrastructure\Symfony\Service\AutoComplete\Clients\Sirene;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

class InseeTokenProvider
{

    /** @var int Cache time-to-live in seconds (7 days) */
    private int $cacheTtl = 604800;

    public function __construct(
        #[Autowire('%env(INSEE_CONSUMER_KEY)%')]
        private readonly string $consumerKey,

        #[Autowire('%env(INSEE_CONSUMER_SECRET)%')]
        private readonly string $consumerSecret,

        #[Autowire('%kernel.cache_dir%/insee_token.json')]
        private string          $cacheFile = ''
    )
    {
        $this->cacheFile      = $cacheFile ?: __DIR__ . '/insee_token_cache.json';
    }

    /**
     * Get a valid access token, either from cache or by requesting a new one.
     *
     * @return string Bearer access token
     * @throws \RuntimeException on HTTP or JSON errors
     */
    public function getToken(): string
    {
        // 1. Try to load from cache
        if (file_exists($this->cacheFile)) {
            $raw = @file_get_contents($this->cacheFile);
            if ($raw !== false) {
                $data = json_decode($raw, true);
                if (
                    isset($data['access_token'], $data['expires_at'])
                    && time() < $data['expires_at']
                ) {
                    // cached token is still valid
                    return $data['access_token'];
                }
            }
        }

        // 2. No valid cache – fetch a fresh token
        $url = 'https://api.insee.fr/token';
        $auth = base64_encode($this->consumerKey . ':' . $this->consumerSecret);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => 'grant_type=client_credentials',
            CURLOPT_HTTPHEADER     => [
                'Authorization: Basic ' . $auth,
                'Content-Type: application/x-www-form-urlencoded',
            ],
            CURLOPT_SSL_VERIFYPEER => false, // as per `-k` in your example
        ]);

        $response = curl_exec($ch);
        if ($response === false) {
            throw new \RuntimeException('cURL error: ' . curl_error($ch));
        }
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status < 200 || $status >= 300) {
            throw new \RuntimeException("Token endpoint returned HTTP $status: $response");
        }

        $tokenData = json_decode($response, true);
        if (!isset($tokenData['access_token'], $tokenData['expires_in'])) {
            throw new \RuntimeException('Invalid token response: ' . $response);
        }

        // 3. Compute cache expiry (use the smaller of API’s expires_in vs our TTL)
        $apiExpiresIn = (int)$tokenData['expires_in'];
        $ttl = min($apiExpiresIn, $this->cacheTtl);
        $expiresAt = time() + $ttl;

        // 4. Write to cache
        $payload = json_encode([
            'access_token' => $tokenData['access_token'],
            'expires_at'   => $expiresAt,
        ], JSON_PRETTY_PRINT);

        if (file_put_contents($this->cacheFile, $payload, LOCK_EX) === false) {
            // if cache write fails, we still return the token
            error_log("Warning: could not write token cache to {$this->cacheFile}");
        }

        return $tokenData['access_token'];
    }
}
