<?php

namespace App\AppIntegrationBundle\Infrastructure\Symfony\Service\Adapters;

use App\AppIntegrationBundle\Infrastructure\Symfony\Service\AutoComplete\Clients\Sirene\InseeApiKeyProvider;
use App\Entity\Entreprise;
use App\Repository\EntrepriseRepository;
use DateTime;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsTaggedItem(index: 'sirene')]
#[AutoconfigureTag('app.autocomplete.adapter')]
class SireneAutoCompleteAdapter implements AutoCompleteAdapterInterface
{
    private const API_URL = 'https://api.insee.fr/api-sirene/3.11/siret';
    private const CACHE_KEY = 'fr_communes_cache_v1';
    private const CACHE_TTL = 86400 * 7; // 7 jours

    private array $communesCache = [];

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        private readonly HttpClientInterface     $httpClient,
        private readonly InseeApiKeyProvider     $apiKeyProvider,
        private readonly EntrepriseRepository    $entrepriseRepository,
        private readonly ?CacheItemPoolInterface $cache = null,
        private readonly ?string                 $communesCsvPath = '/srv/app/private/datas/fr_communes.csv'
    )
    {
        $this->initializeCommunesCache();
    }

    /**
     * Initialise le cache des communes au démarrage.
     * @throws InvalidArgumentException
     */
    private function initializeCommunesCache(): void
    {
        $cachePool = $this->cache ?? new FilesystemAdapter(namespace: 'sirene_communes');

        $item = $cachePool->getItem(self::CACHE_KEY);
        if ($item->isHit()) {
            $this->communesCache = $item->get();
            return;
        }

        // Pas en cache → on charge depuis le CSV
        if ($this->communesCsvPath && file_exists($this->communesCsvPath)) {
            $this->communesCache = $this->loadCommunesFromCsv($this->communesCsvPath);
        }

        // Stockage dans le cache
        $item->set($this->communesCache);
        $item->expiresAfter(self::CACHE_TTL);
        $cachePool->save($item);
    }

    /**
     * Retourne les suggestions d’entreprises.
     * @param string $query
     * @return array
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function getSuggestions(string $query): array
    {
        $nombre = 200;

        // 🔹 Recherche locale
        if ($query === 'all') {
            $enterprises = $this->entrepriseRepository->findAll();
            $nombre = 100;
        } else {
            $enterprises = $this->entrepriseRepository->findByName($query);
        }

        if ($enterprises) {
            return $this->transformDatabaseEntreprisesToApiFormat($enterprises);
        }

        // 🔹 Recherche API Sirene
        $headers = $this->apiKeyProvider->getDefaultHeaders();
        $luceneQuery = $this->buildLuceneQuery($query);

        $response = $this->httpClient->request('GET', self::API_URL, [
            'headers' => $headers,
            'query' => [
                'q' => $luceneQuery,
                'debut' => 0,
                'nombre' => $nombre,
                'masquerValeursNulles' => false,
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            return [];
        }

        $data = $response->toArray(false);
        $etabs = $data['etablissements'] ?? [];

        foreach ($etabs as &$etab) {
            $adr = $etab['adresseEtablissement'] ?? [];
            $etab['emailReferentEntreprise'] = '';
            $etab['telephoneReferentEntreprise'] = '';
            $etab['value'] = sprintf(
                '%s (%s %s %s, %s %s) - %s',
                $etab['uniteLegale']['denominationUniteLegale'] ?? '',
                $adr['numeroVoieEtablissement'] ?? '',
                $adr['typeVoieEtablissement'] ?? '',
                $adr['libelleVoieEtablissement'] ?? '',
                $adr['codePostalEtablissement'] ?? '',
                $adr['libelleCommuneEtablissement'] ?? '',
                $etab['siret'] ?? ''
            );
        }

        return $etabs;
    }

    /**
     * Construit une requête Lucene robuste avec correspondance de ville.
     */
    private function buildLuceneQuery(string $query): string
    {
        $query = trim($query);
        if ($query === '') {
            return '*';
        }

        $upper = strtoupper($this->normalize($query));

        $siren = preg_match('/\b\d{9}\b/', $upper, $matches) ? reset($matches) : null;
        $siret = preg_match('/\b\d{14}\b/', $upper, $matches) ? reset($matches) : null;
        $cp = preg_match('/\b\d{5}\b/', $upper, $m) ? $m[0] : null;

        $mots = preg_split('/\s+/', $upper);
        $ville = null;
        $nom = null;

        foreach ($mots as $mot) {
            if (!$ville && $this->findCommuneCode($mot)) {
                $ville = $this->findCommuneCode($mot);
                continue;
            }
            if (preg_match('/^[A-ZÉÈÎÏÂÀÇ\-]+$/', $mot) && strlen($mot) > 2) {
                $nom = trim(($nom ? "$nom " : '') . $mot);
            }
        }

        $filters = array_filter([
            $siren ? sprintf('(siren:%s OR siret:%s*)', $siren, $siren) : null,
            $siret ? sprintf('siret:%s', $siret) : null,
            $cp ? sprintf('(codePostalEtablissement:%s*)', $cp) : null,
            $ville ? sprintf('(codeCommuneEtablissement:%s OR libelleCommuneEtablissement:"%s")', $ville, $this->getCommuneName($ville)) : null,
            $nom ? sprintf('(denominationUniteLegale:(%1$s*) OR denominationUsuelle1UniteLegale:(%1$s*) OR sigleUniteLegale:(%1$s*))', $nom) : null,
        ]);

        return $filters ? implode(' AND ', $filters) : sprintf('(denominationUniteLegale:(%s*) OR sigleUniteLegale:(%s*))', $upper, $upper);
    }

    /**
     * Normalise un texte : supprime accents, ponctuation et met en majuscule.
     */
    private function normalize(string $text): string
    {
        $slugger = new AsciiSlugger('fr');
        $normalized = $slugger->slug($text)->lower()->replace('-', ' ');
        return strtoupper((string)$normalized);
    }

    private function loadCommunesFromCsv(string $path): array
    {
        $communes = [];
        $handle = fopen($path, 'r');
        if (!$handle) return $communes;

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (count($row) !== 3) continue;
            [$code, $nom, $nomAffiche] = $row;
            $communes[$code] = [
                'nom' => $this->normalize($nom),
                'nomAffiche' => $nomAffiche,
            ];
        }
        fclose($handle);

        return $communes;
    }

    private function findCommuneCode(string $mot): ?string
    {
        $mot = $this->normalize($mot);

        foreach ($this->communesCache as $code => $ville) {
            if ($ville['nom'] === $mot || levenshtein($mot, $ville['nom']) <= 1) {
                return $code;
            }
        }

        return null;
    }

    public function getAdapterName(): string
    {
        return 'sirene';
    }

    /**
     * Transforme les entités Entreprise locales au format API Sirene 3.11.
     *
     * @param Entreprise[] $databaseEntreprises
     * @return array
     */
    public function transformDatabaseEntreprisesToApiFormat(array $databaseEntreprises): array
    {
        $apiEntreprises = [];

        foreach ($databaseEntreprises as $entreprise) {
            $adresse = $entreprise->getAdresse();
            $siret = $entreprise->getSiret() ?? '';
            $siren = substr($siret, 0, 9);
            $nic = substr($siret, 9);

            // --- Adresse établissement ---
            $adresseEtablissement = [
                'numeroVoieEtablissement' => $adresse?->getNumVoie(),
                'typeVoieEtablissement' => $adresse?->getTourEtc(),
                'libelleVoieEtablissement' => $adresse?->getDistribution(),
                'codePostalEtablissement' => $adresse?->getCp(),
                'libelleCommuneEtablissement' => $adresse?->getVille(),
                'codeCommuneEtablissement' => $adresse?->getCp(),
                'identifiantAdresseEtablissement' => $adresse?->getCp() . '_B',
                'coordonneeLambertAbscisseEtablissement' => null,
                'coordonneeLambertOrdonneeEtablissement' => null,
            ];

            // --- Informations sur l’unité légale ---
            $uniteLegale = [
                'etatAdministratifUniteLegale' => 'A', // Active
                'statutDiffusionUniteLegale' => 'O',   // Diffusable
                'dateCreationUniteLegale' => $entreprise->getCreatedAt()?->format('Y-m-d'),
                'categorieJuridiqueUniteLegale' => '6540', // code fictif
                'denominationUniteLegale' => $entreprise->getNom(),
                'activitePrincipaleUniteLegale' => $entreprise->getNaf(),
                'nomenclatureActivitePrincipaleUniteLegale' => 'NAFRev2',
                'economieSocialeSolidaireUniteLegale' => 'N',
                'nicSiegeUniteLegale' => $nic,
                'dateDernierTraitementUniteLegale' => (new DateTime())->format('Y-m-d\TH:i:s.000'),
                'categorieEntreprise' => null,
                'anneeCategorieEntreprise' => null,
            ];

            // --- Libellé lisible pour l’autocomplete ---
            $value = sprintf(
                '%s (%s %s, %s %s) - %s',
                $entreprise->getNom(),
                $adresse?->getNumVoie() ?? '',
                $adresse?->getDistribution() ?? '',
                $adresse?->getCp() ?? '',
                $adresse?->getVille() ?? '',
                $siret
            );

            // --- Structure complète conforme à l’API ---
            $apiEntreprises[] = [
                'siren' => $siren,
                'nic' => $nic,
                'siret' => $siret,
                'statutDiffusionEtablissement' => 'O',
                'dateCreationEtablissement' => $entreprise->getCreatedAt()?->format('Y-m-d'),
                'dateDernierTraitementEtablissement' => (new DateTime())->format('Y-m-d\TH:i:s.000'),
                'etablissementSiege' => true,
                'nombrePeriodesEtablissement' => 1,
                'uniteLegale' => $uniteLegale,
                'adresseEtablissement' => $adresseEtablissement,
                'value' => $value,
                'emailReferentEntreprise' => $entreprise->getEmailReferent()
                    ?? $entreprise->getSuppleant1()
                        ?? $entreprise->getSuppleant2(),
                'telephoneReferentEntreprise' => $entreprise->getTelephoneReferent()
                    ?? $entreprise->getTelephoneSuppleant1()
                        ?? $entreprise->getTelephoneSuppleant2(),
            ];
        }

        return $apiEntreprises;
    }

    private function getCommuneName(string $code): string
    {
        return $this->communesCache[$code]['nomAffiche'] ?? '';
    }
}
