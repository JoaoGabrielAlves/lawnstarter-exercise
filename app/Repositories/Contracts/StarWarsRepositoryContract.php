<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface StarWarsRepositoryContract
{
    /**
     * Get data from SWAPI endpoint
     */
    public function get(string $endpoint, array $params = []): array;

    /**
     * Get all resources from the root endpoint
     */
    public function getRoot(): array;

    /**
     * Get paginated results from an endpoint
     */
    public function getPaginated(string $endpoint, int $page = 1, array $params = []): array;

    /**
     * Get a specific resource by ID
     */
    public function getById(string $resource, int $id): array;

    /**
     * Search within a resource type
     */
    public function search(string $resource, string $query, int $page = 1): array;

    /**
     * Get basic resource data without resolving relationships
     */
    public function getBasicById(string $resource, int $id): array;

    /**
     * Resolve film relationships for a resource
     */
    public function resolveFilmsForResource(array $filmUrls): array;

    /**
     * Resolve character relationships for a resource
     */
    public function resolveCharactersForResource(array $characterUrls): array;
}
