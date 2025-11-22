<?php

namespace App\AppIntegrationBundle\Infrastructure\Symfony\Service\AutoComplete\Clients\GoogleMaps;

use App\AppIntegrationBundle\Domain\Entity\Address;
use App\AppIntegrationBundle\Domain\Repository\GoogleMapsRepositoryInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GoogleMapsClient implements GoogleMapsRepositoryInterface
{
    private const API_URL = 'https://maps.googleapis.com/maps/api/geocode/json';

    private HttpClientInterface $httpClient;
    private string $apiKey;

    public function __construct(HttpClientInterface $httpClient, string $apiKey)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getGeolocation(string $address): ?Address
    {
        $response = $this->httpClient->request('GET', self::API_URL, [
            'query' => [
                'address' => $address,
                'key' => $this->apiKey,
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        $data = $response->toArray();
        if (empty($data['results'])) {
            return null;
        }

        $result = $data['results'][0];
        $location = $result['geometry']['location'];
        $components = $result['address_components'];

        return new Address(
            $result['formatted_address'],
            $this->getComponent($components, 'route') ?? '',
            $this->getComponent($components, 'street_number') ?? '',
            $this->getComponent($components, 'postal_code') ?? '',
            $this->getComponent($components, 'locality') ?? '',
            $this->getComponent($components, 'sublocality') ?? '',
            $location['lat'],
            $location['lng']
        );
    }

    private function getComponent(array $components, string $type): ?string
    {
        foreach ($components as $component) {
            if (in_array($type, $component['types'], true)) {
                return $component['long_name'];
            }
        }
        return null;
    }
}