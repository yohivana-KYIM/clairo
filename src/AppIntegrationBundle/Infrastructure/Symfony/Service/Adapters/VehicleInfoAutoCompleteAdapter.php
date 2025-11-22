<?php

namespace App\AppIntegrationBundle\Infrastructure\Symfony\Service\Adapters;

use DateTime;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsTaggedItem(index: 'vehicle_info_autocomplete')]
#[AutoconfigureTag('app.autocomplete.adapter')]
class VehicleInfoAutoCompleteAdapter implements AutoCompleteAdapterInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        #[Autowire('%env(VEHICLE_API_KEY)%')]
        private readonly string $rapidApiKey,
        #[Autowire('%env(string:VEHICLE_API_HOST_NAME)%')]
        private readonly string $hostName = 'https://apiplaqueimmatriculation.com'
    ) {}

    /**
     * Retourne le statut GIES d’un véhicule selon ses émissions de CO₂ et son type d’énergie.
     *
     * @param array $vehicleData       Tableau associatif contenant au minimum 'co2' et/ou 'energieNGC'
     * @param int   $co2Threshold      Seuil CO₂ en g/km au-delà duquel on considère le véhicule comme GIES
     * @param array $zeroEmissionTypes Liste des énergies considérées comme zéro-émission
     * @return string                  "gies" ou "non_gies"
     */
    function getVehicleGIESStatus(array $vehicleData, int $co2Threshold = 50, array $zeroEmissionTypes = ['Electric', 'Hydrogen', 'Fuel Cell']): string
    {
        // 1. Tentative d’extraction de la valeur CO₂ (g/km)
        if (!empty($vehicleData['co2']) && preg_match('/\d+/', $vehicleData['co2'], $matches)) {
            $co2 = (int) $matches[0];
            if ($co2 > $co2Threshold) {
                return 'gies';
            } else {
                return 'non_gies';
            }
        }

        // 2. Si pas de CO₂ fiable, on vérifie le type d'énergie
        if (!empty($vehicleData['energieNGC'])) {
            foreach ($zeroEmissionTypes as $zeroType) {
                if (stripos($vehicleData['energieNGC'], $zeroType) !== false) {
                    return 'non_gies';
                }
            }
        }

        // 3. Par défaut, on considère que tout autre cas est GIES
        return 'gies';
    }

    /**
     * Calcule la date d'expiration du statut GIES d'un véhicule.
     *
     * @param array    $vehicleData  Tableau associatif contenant au moins 'date1erCir_us' ou 'date1erCir_fr'
     * @param int      $yearsValid   Durée (en années) de validité du statut GIES (par défaut 10 ans)
     * @param string   $outputFormat Format de sortie de la date (par défaut 'Y-m-d')
     * @return string|null           Date d’expiration au format demandé, ou null si pas de date de 1ʳᵉ circu.
     */
    function getGiesExpiryDate(array $vehicleData, int $yearsValid = 10, string $outputFormat = 'Y-m-d'): ?string
    {
        // 1. Récupérer la date de première mise en circulation
        $rawDate = $vehicleData['date1erCir_us'] ?? $vehicleData['date1erCir_fr'] ?? null;
        if (!$rawDate) {
            return null; // pas de date disponible
        }

        // 2. Normaliser en format US (Y-m-d)
        if (isset($vehicleData['date1erCir_fr']) && !isset($vehicleData['date1erCir_us'])) {
            // le format français est 'd-m-Y' ou 'd/m/Y'
            $rawDate = str_replace('/', '-', $rawDate);
            $d = DateTime::createFromFormat('d-m-Y', $rawDate);
        } else {
            // on suppose que date1erCir_us est déjà 'Y-m-d'
            $d = DateTime::createFromFormat('Y-m-d', $rawDate);
        }

        if (!$d) {
            return null; // échec de parsing
        }

        // 3. Calculer l'expiration
        $d->modify("+{$yearsValid} years");

        // 4. Retourner la date formatée
        return $d->format($outputFormat);
    }

    public function getSuggestions(string $query): array
    {
        try {
            $response = $this->httpClient->request('GET', 'https://api-plaque-immatriculation-siv.p.rapidapi.com/get-vehicule-info', [
                'query' => [
                    'token'           => 'TokenDemoRapidapi',
                    'host_name'       => urlencode($this->hostName),
                    'immatriculation' => $query,
                ],
                'headers' => [
                    'x-rapidapi-host' => 'api-plaque-immatriculation-siv.p.rapidapi.com',
                    'x-rapidapi-key'  => $this->rapidApiKey,
                ],
            ]);

            $body = $response->toArray();
            $data = $body['data'] ?? [];
        } catch (\Throwable $e) {
            return [];
        }

        if (empty($data['immat'])) {
            return [];
        }

        // Format date de première mise en circulation
        $dateIso = DateTime::createFromFormat('d-m-Y', $data['date1erCir_fr'])
            ->format('Y-m-d');


        return [[
            'brand' => $data['marque'] ?? 'Marque inconnue',
            'modele' => $data['modele'] ?? 'Modèle inconnu',
            'immatriculation' => strtoupper($data['immat']),
            'value' => strtoupper($data['immat']),
            'meta'  => [
                'firstRegistrationDate' => $dateIso,
                'companyVehicle'        => isset($data['genreVCGNGC']) && $data['genreVCGNGC'] !== 'VP'
                    ? 'company'
                    : 'personal',
                'typeCertificationGIES' => $this->getVehicleGIESStatus($data),
                'giesExpiry'            => $this->getGiesExpiryDate($data),
            ],
        ]];
    }

    public function getAdapterName(): string
    {
        return 'vehicle_info_autocomplete';
    }
}
