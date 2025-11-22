<?php

namespace App\AppIntegrationBundle\Infrastructure\Symfony\Service\Adapters;

use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsTaggedItem(index: 'google')]
#[AutoconfigureTag('app.autocomplete.adapter')]
class GoogleMapsAutoCompleteAdapter implements AutoCompleteAdapterInterface
{
    private const GOOGLE_PLACES_AUTOCOMPLETE_URL = 'https://maps.googleapis.com/maps/api/place/autocomplete/json';
    const GOOGLE_PLACE_DETAILS_URL = 'https://maps.googleapis.com/maps/api/place/details/json';

    private HttpClientInterface $httpClient;
    private string $apiKey;
    private string $adapterName = 'google';

    public function __construct(HttpClientInterface $httpClient, ParameterBagInterface $params)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $params->get('gmap_api_key');
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getSuggestions(string $query): array
    {
        // 1) Autocomplete lookup
        $response = $this->httpClient->request('GET', self::GOOGLE_PLACES_AUTOCOMPLETE_URL, [
            'query' => [
                'input' => $query,
                'key'   => $this->apiKey,
                'types' => 'address',
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            return [];
        }

        $data = $response->toArray();
        $results = [];

        foreach ($data['predictions'] as $prediction) {
            $placeId = $prediction['place_id'];

            // 2) Fetch details for postal_code
            $detailsResp = $this->httpClient->request('GET', self::GOOGLE_PLACE_DETAILS_URL, [
                'query' => [
                    'place_id' => $placeId,
                    'fields'   => 'address_component',
                    'key'      => $this->apiKey,
                ],
            ]);

            $postalCode = '';
            if ($detailsResp->getStatusCode() === 200) {
                $details = $detailsResp->toArray();
                foreach ($details['result']['address_components'] as $comp) {
                    if (in_array('postal_code', $comp['types'], true)) {
                        $postalCode = $comp['long_name'];
                        break;
                    }
                }
            }

            // 3) Build final suggestion
            $terms = $prediction['terms'];
            $results[] = [
                'value' => $prediction['description'],
                'description' => $prediction['description'],
                'numero'      => $terms[0]['value'] ?? '',
                'voie'        => $terms[1]['value'] ?? '',
                'ville'       => $terms[2]['value'] ?? '',
                'Pays'        => $terms[3]['value'] ?? '',
                'codePostale' => $postalCode,
                'metadata'    => [
                    'place_id' => $placeId,
                    'types'    => $prediction['types'],
                ],
            ];
        }

        return $results;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function getAdapterName(): string
    {
        return $this->adapterName;
    }

    public function setAdapterName(string $adapterName): void
    {
        $this->adapterName = $adapterName;
    }
}