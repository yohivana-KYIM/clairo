<?php
// src/MultiStepBundle/Persistence/VehicleMicroCesamePersistanceStrategy.php

namespace App\MultiStepBundle\Persistence;

use App\MultiStepBundle\Entity\StepData;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

class VehicleMicroCesamePersistanceStrategy extends PersistanceStrategy
{
    private HttpClientInterface $http;
    private string $apiUrl;
    private array  $headers;

    public function __construct(
        RequestStack                           $requestStack,
        HttpClientInterface                    $httpClient,
        private readonly Security              $security,
        public readonly EntityManagerInterface $entityManager,
        #[Autowire('%env(MICROCESAME_API_URL)%')]
        string                                 $apiUrl,
        #[Autowire('%env(MICROCESAME_API_KEY)%')]
        string                                 $apiKey,
        #[Autowire('%env(MICROCESAME_LOGIN)%')]
        string                                 $login,
        #[Autowire('%env(MICROCESAME_PWD)%')]
        string                                 $pwd,
        #[Autowire('%env(MICROCESAME_PROFILE)%')]
        string                                 $profile
    ) {
        parent::__construct($requestStack);

        $this->http   = $httpClient;
        $this->apiUrl = rtrim($apiUrl, '/');

        $this->headers = [
            'X-API-KEY'    => $apiKey,
            'X-LOGIN'      => $login,
            'X-PWD'        => $pwd,
            'X-PROFILE'    => $profile,
            'Content-Type' => 'application/json; charset=utf-8',
        ];

        // session keys
        $this->setCurrentStepSessionKey('vehicle.current_step');
        $this->setDataSessionKey('vehicle.data');
    }

    /**
     * Save one step, then flatten & POST the whole payload.
     *
     * @param string $stepId
     * @param array  $data
     * @return array
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function saveData(string $stepId, array $data): array
    {
        // 1) write this step back to session
        $this->session->set("{$this->getDataSessionKey()}.{$stepId}", $data);

        // 2) gather all steps from session
        $flatData = [];
        foreach ($this->session->all() as $key => $val) {
            if (str_starts_with($key, "{$this->getDataSessionKey()}.") && is_array($val)) {
                $flatData = array_merge($flatData, $val);
            }
        }

        // 3) date‐formatter helper
        $fmt = fn(?string $d, string $inFmt = 'Y-m-d', string $outFmt = 'd/m/Y'): string =>
        $d ? DateTime::createFromFormat($inFmt, $d)?->format($outFmt) ?? '' : '';

        // 4) build payload per your equivalence table:
        $payload = [
            'LIB_01' => '',
            'LIB_02' => '',
            'LIB_03' => '',

            // IP Lavera  = vehicle_step_three.lavera_port_access
            'LIB_04' => !empty($flatData['lavera_port_access']) ? 'OUI' : 'NON',

            // IP Fos     = vehicle_step_three.fos_port_access
            'LIB_05' => !empty($flatData['fos_port_access'])    ? 'OUI' : 'NON',

            'LIB_06' => '',
            'LIB_07' => $flatData['address']         ?? '',
            'LIB_08' => trim(($flatData['postal_code'] ?? '') . ' ' . ($flatData['city'] ?? '')),
            'LIB_09' => '',   // employment date + contract type
            'LIB_10' => '',   // birth date + cp+ville+pays
            'LIB_11' => '',   // père
            'LIB_12' => '',   // mère
            'LIB_13' => '',   // cni/passport #
            'LIB_14' => '',   // délivré le/par
            'LIB_15' => '',   // email employeur
            'LIB_16' => '',   // téléphone employeur

            // --- remaining API fields ---
            'antiPassback'     => (bool)($flatData['fos_port_access'] ?? false),
            'birthCity'        => '',
            'birthCountry'     => '',
            'birthDate'        => $flatData['first_registration_date'] ?? null,
            'blackList'        => false,
            'class'            => 0,
            'comment'          => base64_decode($flatData['fos_access_reason'] ?? '') ?: '',
            'createDateTime'   => (new DateTime())->format(DateTime::ATOM),
            'createOperator'   => $this->security->getUser()?->getUserIdentifier() ?? '',

            'escortStatus'     => 'k_noStatus',
            'firstName'        => '',
            'id'               => null,
            'idExt'            => '',
            'identityCard'     => '',
            'identityCardType' => '',
            'lastName'         => '',
            'mail'             => $flatData['email'] ?? '',
            'matricule'        => '',
            'nationality'      => '',
            'phoneDesk'        => '',
            'phoneHome'        => '',
            'phoneMobile'      => $flatData['security_officer_phone'] ?? '',
            'pincode'          => '',
            'restControl'      => false,
            'source'           => '',
            'title'            => '',
            'type'             => 'k_permanent',
            'updateDateTime'   => null,
            'updateOperator'   => null,
            'uri'              => null,
            'valid'            => true,
            'validityStartDate'=> $flatData['first_registration_date'] ?? null,
            'validityEndDate'  => $flatData['gies_expiry_date']         ?? null,
            'visitable'        => true,
            'workplace'        => implode(', ', array_filter([
                $flatData['fos_port_access']    ? 'FOS'    : null,
                $flatData['lavera_port_access'] ? 'LAVERA' : null,
            ])),
        ];

        // 5) POST to Microcésame
        $response = $this->http->request('POST', "{$this->apiUrl}/api/users", [
            'headers'    => $this->headers,
            'json'       => $payload,
            'verify_peer'=> false,
            'verify_host'=> false,
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException(
                "MicroCesame API error {$response->getStatusCode()} — {$response->getContent(false)}"
            );
        }

        return $data;
    }

    /**
     * Optionally load existing vehicle data by query params.
     */
    public function loadData(string $stepId, array $queryParams = []): array
    {
        $url = $this->apiUrl . '/api/users';
        if ($queryParams) {
            $url .= '?' . http_build_query($queryParams);
        }

        $response = $this->http->request('GET', $url, [
            'headers' => $this->headers,
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException(
                "MicroCesame GET error {$response->getStatusCode()} — {$response->getContent(false)}"
            );
        }

        $data = json_decode($response->getContent(), true);
        return is_array($data) ? $data : [];
    }

    /**
     * Clear all vehicle-step data from session.
     */
    public function clearAllData(): void
    {
        parent::clearAllData();
        $prefix = "{$this->getDataSessionKey()}.";
        foreach ($this->session->all() as $k => $_) {
            if (str_starts_with($k, $prefix)) {
                $this->session->remove($k);
            }
        }
    }
}
