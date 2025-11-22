<?php

namespace App\MultiStepBundle\Persistence\Helper;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Helper de persistance REST pour Micro-Sesame.
 * Offre filtrage, tri, pagination, sélection de champs, pretty-print et exécution de requêtes.
 *
 * Usage :
 *   $helper = (new MicroSesamePersistenceHelper('https://api.example.com'))
 *       ->setResource('users')
 *       ->addFilter('lastName', 'CO', 'startswith')
 *       ->addSort('firstName', 'asc')
 *       ->setLimit(25)
 *       ->setOffset(0)
 *       ->setFields(['id','firstName','lastName'])
 *       ->enablePrettyPrint(true)
 *       ->execute();
 */
class MicroSesamePersistenceHelper
{
    /** @var string Base URL de l’API (ex: https://server/api) */
    protected $baseUrl;

    /** @var string Ressource ciblée (ex: 'users') */
    protected $resource;

    /** @var array Liste des filtres à appliquer */
    protected $filters = [];

    /** @var array Liste des tris à appliquer */
    protected $sorts = [];

    /** @var int|null Nombre d’items par page */
    protected $limit;

    /** @var int|null Début de la page */
    protected $offset;

    /** @var string[]|null Champs à récupérer */
    protected $fields;

    /** @var bool Pretty-print dans la réponse JSON */
    protected $prettyPrint = false;

    /** @var array En-têtes HTTP personnalisés */
    protected $headers = [];

    /**
     * @param string $baseUrl URL de base de votre API (sans slash final)
     */
    public function __construct(
        #[Autowire('%env(BASE_URL)%')]
        string $baseUrl
    )
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    /**
     * Définit la ressource REST (plural) sur laquelle opérer.
     * @param string $resource
     * @return $this
     */
    public function setResource(string $resource): self
    {
        $this->resource = trim($resource, '/');
        return $this;
    }

    /**
     * Ajoute un filtre GET?filter=...
     *
     * @param string $field   Sélecteur de champ (ex: "lastName", "company.id")
     * @param mixed  $value   Valeur de comparaison
     * @param string $operator Opérateur: "eq","ne","gt","lt","ge","le","contains","startswith","endswith"
     * @return $this
     */
    public function addFilter(string $field, $value, string $operator = 'eq'): self
    {
        $this->filters[] = "{$field}:{$operator}:" . urlencode((string)$value);
        return $this;
    }

    /**
     * Ajoute un critère de tri GET?sort=...
     *
     * @param string $field    Champ sur lequel trier
     * @param string $direction "asc" ou "desc"
     * @return $this
     */
    public function addSort(string $field, string $direction = 'asc'): self
    {
        $dir    = strtolower($direction) === 'desc' ? '-' : '';
        $this->sorts[] = $dir . $field;
        return $this;
    }

    /**
     * Définit le nombre d’éléments à récupérer (page size).
     * @param int $limit
     * @return $this
     */
    public function setLimit(int $limit): self
    {
        $this->limit = max(1, $limit);
        return $this;
    }

    /**
     * Définit l’offset de début (page offset).
     * @param int $offset
     * @return $this
     */
    public function setOffset(int $offset): self
    {
        $this->offset = max(0, $offset);
        return $this;
    }

    /**
     * Sélectionne uniquement certains champs GET?fields=...
     * @param string[] $fields
     * @return $this
     */
    public function setFields(array $fields): self
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * Active ou désactive le pretty-print JSON (?prettyPrint).
     * @param bool $enable
     * @return $this
     */
    public function enablePrettyPrint(bool $enable = true): self
    {
        $this->prettyPrint = $enable;
        return $this;
    }

    /**
     * Ajoute un header HTTP personnalisé à la requête.
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function addHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Requête GET construite et exécutée.
     *
     * @return array Décodage JSON en tableau associatif.
     * @throws \RuntimeException sur erreur HTTP ou JSON invalide.
     */
    public function execute(): array
    {
        if (empty($this->resource)) {
            throw new \RuntimeException("La ressource n'est pas définie.");
        }

        // Construction de l’URL et de la query string
        $url    = "{$this->baseUrl}/{$this->resource}";
        $params = [];

        if ($this->filters) {
            $params['filter'] = implode(',', $this->filters);
        }
        if ($this->sorts) {
            $params['sort'] = implode(',', $this->sorts);
        }
        if ($this->limit !== null) {
            $params['limit'] = $this->limit;
        }
        if ($this->offset !== null) {
            $params['offset'] = $this->offset;
        }
        if ($this->fields) {
            $params['fields'] = implode(',', $this->fields);
        }
        if ($this->prettyPrint) {
            $params['prettyPrint'] = 'true';
        }

        $query = $params ? ('?' . http_build_query($params)) : '';
        $full  = $url . $query;

        // Init cURL
        $ch = curl_init($full);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_map(
            function($v, $k){
		 return "$k: $v";
	},
            array_values($this->headers),
            array_keys($this->headers)
        ));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode < 200 || $httpCode >= 300) {
            throw new \RuntimeException("Requête HTTP échouée ($httpCode): $response");
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("JSON invalide reçu : " . json_last_error_msg());
        }

        return $data;
    }

    /**
     * Réinitialise tous les paramètres (filtre, tri, pagination, etc.) pour une nouvelle requête.
     * @return $this
     */
    public function reset(): self
    {
        $this->filters     = [];
        $this->sorts       = [];
        $this->limit       = null;
        $this->offset      = null;
        $this->fields      = null;
        $this->prettyPrint = false;
        $this->headers     = [];
        return $this;
    }


    /**
     * Execute a GET with a single call, based on arrays of conditions, sorts, pagination, etc.
     *
     * @param array $conditions  [
     *     // simple equality:
     *     'lastName' => 'Smith',
     *     // or with explicit operator:
     *     'age'      => ['value' => 30, 'operator' => 'gt'],
     *     'email'    => ['value' => 'example.com', 'operator' => 'contains'],
     * ]
     * @param array|null $sorts     ['firstName' => 'asc', 'createdAt' => 'desc']
     * @param int|null   $limit     page size
     * @param int|null   $offset    page offset
     * @param string[]|null $fields select only these fields
     * @param bool       $pretty    pretty-print the JSON
     *
     * @return array decoded JSON response
     * @throws \RuntimeException on HTTP or JSON error
     */
    public function findByConditions(
        array $conditions,
        ?array $sorts   = null,
        ?int   $limit   = null,
        ?int   $offset  = null,
        ?array $fields  = null,
        bool   $pretty  = false
    ): array {
        // reset previous filters/sorts/paging but keep baseUrl & resource
        $this->reset();

        // apply filters
        foreach ($conditions as $field => $spec) {
            if (is_array($spec)) {
                $value    = $spec['value'] ?? null;
                $operator = $spec['operator'] ?? 'eq';
            } else {
                $value    = $spec;
                $operator = 'eq';
            }
            $this->addFilter($field, $value, $operator);
        }

        // apply sorts
        if (! empty($sorts)) {
            foreach ($sorts as $field => $direction) {
                $this->addSort($field, $direction);
            }
        }

        // pagination
        if ($limit   !== null) { $this->setLimit($limit); }
        if ($offset  !== null) { $this->setOffset($offset); }

        // field selection
        if ($fields  !== null) { $this->setFields($fields); }

        // pretty printing
        if ($pretty)         { $this->enablePrettyPrint(true); }

        // finally execute
        return $this->execute();
    }



}
