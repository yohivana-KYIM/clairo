<?php

namespace App\AdminBundle\Application\Port;

interface EntityServiceInterface
{
    /**
     * Returns the fully-qualified class name of the supported entity.
     */
    public function getEntityClass(): string;

    /**
     * Returns a list of entities filtered by optional parameters.
     * Supports sorting, searching, filtering, pagination.
     *
     * @param array $criteria   Key-value filters
     * @param array $sort       Key => direction ('asc'|'desc')
     * @param int   $page       Pagination: current page
     * @param int   $limit      Pagination: items per page
     *
     * @return iterable<object>
     */
    public function findEntities(array $criteria = [], array $sort = [], int $page = 1, int $limit = 50): iterable;

    /**
     * Returns a single entity by its ID or unique identifier.
     *
     * @param mixed $id
     * @return object|null
     */
    public function findOne(mixed $id): ?object;

    /**
     * Creates or updates an entity from request or array data.
     *
     * @param array $data
     * @return object
     */
    public function saveEntity(array $data): object;

    /**
     * Deletes an entity by its identifier.
     *
     * @param mixed $id
     */
    public function deleteEntity(mixed $id): void;

    /**
     * Returns the number of total items matching given criteria (for pagination).
     *
     * @param array $criteria
     * @return int
     */
    public function count(array $criteria = []): int;

    /**
     * Returns searchable fields for this entity (for filters or search UI).
     *
     * @return array<string>
     */
    public function getSearchableFields(): array;

    /**
     * Returns available sort fields and directions for this entity.
     *
     * @return array<string, string> Field => Direction
     */
    public function getSortableFields(): array;

    /**
     * Returns exportable fields and optionally a model mapping.
     *
     * @return array<string, mixed>
     */
    public function getExportableFields(): array;

    /**
     * Optional: Returns a formatted export-ready dataset.
     *
     * @param array $criteria
     * @param array $sort
     * @return iterable<object|array>
     */
    public function getExportData(array $criteria = [], array $sort = []): iterable;

    /**
     * Checks if current user is allowed to perform an action on the entity.
     *
     * @param string $action
     * @param object|null $entity
     * @return bool
     */
    public function isActionAllowed(string $action, ?object $entity = null): bool;
}
