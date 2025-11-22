<?php

namespace App\MultiStepBundle\Persistence;

use App\MultiStepBundle\Default\PersistenceStrategyInterface;
use App\MultiStepBundle\Entity\StepData;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PersonMicroCesamePersistanceStrategy extends PersistanceStrategy
{
    private HttpClientInterface $http;
    private string             $apiUrl;
    private array              $headers;

    public function __construct(
        RequestStack $requestStack,
        HttpClientInterface $httpClient,
        private readonly Security $security,
        private readonly EntityManagerInterface $entityManager,
        #[Autowire('%env(MICROCESAME_API_URL)%')]
        string $apiUrl,
        #[Autowire('%env(MICROCESAME_API_KEY)%')]
        string $apiKey,
        #[Autowire('%env(MICROCESAME_LOGIN)%')]
        string $login,
        #[Autowire('%env(MICROCESAME_PWD)%')]
        string $pwd,
        #[Autowire('%env(MICROCESAME_PROFILE)%')]
        string $profile,       // usually '1'
        #[Autowire('%env(BASE_URL)%')]
        public readonly string $baseUrl
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

        // use a dedicated session namespace
        $this->setCurrentStepSessionKey('microcesame.current_step');
        $this->setDataSessionKey       ('microcesame.data');
    }

    /**
     * Decode base64‐encoded content inside a <span class="encoded-content"> tag,
     * or return the original string if no such tag exists.
     *
     * @param string $html The input HTML or text
     * @return string The decoded content or original input
     */
    function decodeEncodedContent(string $html): string
    {
        // look for <span class="encoded-content">…</span>
        if (preg_match(
            '/<span\s+class=["\']encoded-content["\']\s*>(.*?)<\/span>/is',
            $html,
            $matches
        )) {
            // $matches[1] is the inner text of the span
            $decoded = base64_decode($matches[1], true);
            // if decoding fails (invalid base64), fall back to original inner text
            return $decoded !== false ? $decoded : $matches[1];
        }

        // no matching tag — return the input literally
        return $html;
    }

    /**
     * Save one step, then immediately re-assemble+POST everything.
     *
     * @param string $stepId
     * @param array $data
     * @return array $data
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws \DateMalformedStringException
     */
    public function saveData(string $stepId, array $data): array
    {
        $entity = $this->entityManager->getRepository(StepData::class)->findOneBy(['stepNumber' => $stepId]);
        if ($entity && $entity->getMicrocesameId()) return $data;
        $user = $this->security->getUser();
        // 1) write this step back to session
        $this->session->set("{$this->getDataSessionKey()}.{$stepId}", $data);

        // 2) gather all step payloads
        $allSteps = [];
        foreach ($this->session->all() as $key => $val) {
            if (str_starts_with($key, "{$this->getDataSessionKey()}.") && is_array($val)) {
                $allSteps[] = $val;
            }
        }

        // 3) flatten into one map
        $flat = array_merge(...array_values($allSteps[0]));

        // 4) helper to format french date
        $fmtDate = function($d) {
            if ($d instanceof \DateTime) {
                return $d->format('d/m/Y');
            }
            if (is_string($d) && preg_match('/\d{4}-\d{2}-\d{2}/', $d)) {
                return \DateTime::createFromFormat('Y-m-d', $d)->format('d/m/Y');
            }
            return '';
        };

        $path = $flat['id_card'] ?? $flat['passport'] ?? '';
        $noIdCard = implode('/', array_slice(explode('/', $path), -1));
        $access_durations = [
            'permanent' => 'k_permanent',
            'temporaire' => 'k_temporary',
            'Accord'    => 'k_permanent',
        ];
        // 5) build MicroCesame payload (keys per your table)
        $payload = [
            'LIB_01'            => $flat['matricule']               ?? 'xxxxx',
            'LIB_02'            => $flat['social_security_number'] ?? 'xxxxx',
            'LIB_03'            => in_array('hq', (array)($flat['access_locations'] ?? [])) ? 'dt' : 'NON',
            'LIB_04'            => in_array('lavera', (array)($flat['access_locations'] ?? [])) ? 'OUI' : 'NON',
            'LIB_05'            => in_array('fos', (array)($flat['access_locations'] ?? [])) ? 'OUI' : 'NON',
            'LIB_06'            => in_array('hq', (array)($flat['access_locations'] ?? [])) ? 'OUI' : 'NON',
            'LIB_07'            => $flat['address']                ?? 'xxxxx',
            'LIB_08'            => trim(($flat['postal_code'] ?? 'xxxxx') . ' ' . ($flat['city'] ?? 'xxxxx')),
            'LIB_09'            => trim(($fmtDate($flat['employment_date'] ?? null) ?? 'xxxxx') . ' ' . ($flat['contract_type'] ?? 'xxxxx')),
            'LIB_10'            => $flat['employee_birthdate']     ?? 'xxxxx',
            'LIB_11'            => trim(($flat['father_name'] ?? 'xxxxx') . ' ' . ($flat['father_first_name'] ?? 'xxxxx')),
            'LIB_12'            => trim(($flat['mother_maiden_name'] ?? '') . ' ' . ($flat['mother_first_name'] ?? 'xxxxx')),
            'LIB_13'            => $flat['numero_cni']             ?? 'xxxxx',
            'LIB_14'            => sprintf('délivrée le %s/ par %s', (new \DateTime())->format('d/m/Y'), strtok($user->getUserIdentifier(), '@')),
            'LIB_15'            => $flat['security_officer_email'] ?? 'xxxxx',
            'LIB_16'            => $flat['security_officer_phone'] ?? 'xxxxx',

            'antiPassback'      => !empty($flat['employee_refugee']),
            'birthCity'         => $flat['employee_birthplace']    ?? 'xxxxx',
            'birthCountry'      => $flat['country']                ?? 'xxxxx',
            'birthDate'         => $flat['employee_birthdate']     ?? 'xxxxx',
            'blackList'         => false,
            'class'             => 0,
            'comment'           => ($this->decodeEncodedContent($flat['access_purpose'] ?? '') ?? '') . ' ATT TC TEMP',
            'escortStatus'      => 'k_noStatus',
            'firstName'         => $flat['employee_first_name']    ?? 'xxxxx',
            'identityCard'      => implode('/', array_slice(explode('/', $flat['id_card'] ?? $flat['passport'] ?? 'xxxxx'), -1)),
            'identityCardType'  => isset($flat['id_card']) ? 'Carte d\'identité' : (isset($flat['passport']) ? 'Passeport' : 'xxxxx'),
            'lastName'          => $flat['employee_last_name']     ?? 'xxxxx',
            'mail'              => $flat['employee_email']         ?? 'xxxxx',
            'matricule'         => $flat['matricule']              ?? 'xxxxx',
            'nationality'       => $flat['nationality']            ?? 'xxxxx',
            'phoneDesk'         => $flat['employee_phone']         ?? 'xxxxx',
            'phoneMobile'       => $flat['employee_phone']         ?? 'xxxxx',
            'type'              => $access_durations[$flat['access_duration']]        ?? 'k_permanent',
            'valid'             => true,
            'validityStartDate' => $flat['employment_date']        ?? (new DateTime())->format('Y-m-d'),
            'validityEndDate'   => (isset($flat['employment_date'], $flat['contract_end_date']) && $flat['employment_date'] < $flat['contract_end_date'])
                ? $flat['contract_end_date']
                : (isset($flat['employment_date']) ? (new DateTime($flat['employment_date']))->modify('+5 years')->format('Y-m-d') : (new DateTime())->modify('+5 years')->format('Y-m-d')),
            'visitable'         => true,
            'workplace'         => implode(',', (array)($flat['access_locations'] ?? [])),
        ];

        // 6) POST to MicroCesame
        $response = $this->http->request('POST', $this->apiUrl . '/api/users', [
            'headers' => $this->headers,
            'json'    => $payload,
            'verify_peer'  => false,
            'verify_host'  => false,
        ]);

        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            throw new \RuntimeException(
                "MicroCesame API error {$response->getStatusCode()} — "
                . $response->getContent(false)
            );
        }

        if ($entity) {
            $entity->setMicrocesameId(json_decode($response->getContent(false), true)['id']);
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        }

        return $data;
    }

    /**
     * Load data from MicroCesame, supports optional query params e.g. ['filter' => 'company.name=TIL']
     *
     * @param string $stepId
     * @param array $queryParams
     * @return array
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function loadData(string $stepId, array $queryParams = []): array
    {
        $url = $this->apiUrl;
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        $response = $this->http->request('GET', $url, [
            'headers' => $this->headers,
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException(
                "MicroCesame API error {$response->getStatusCode()} — "
                . $response->getContent(false)
            );
        }

        $data = json_decode($response->getContent(), true);
        return is_array($data) ? $data : [];
    }

    public function clearAllData(): void
    {
        parent::clearAllData();
        $prefix = "{$this->getDataSessionKey()}.";
        foreach ($this->session->all() as $k => $v) {
            if (0 === strpos($k, $prefix)) {
                $this->session->remove($k);
            }
        }
    }
}
