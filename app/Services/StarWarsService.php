<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\StarWarsRepositoryContract;
use App\Services\Contracts\StarWarsServiceContract;

final class StarWarsService implements StarWarsServiceContract
{
    private const VALID_RESOURCES = [
        'people', 'films', 'starships', 'vehicles', 'species', 'planets',
    ];

    public function __construct(
        private readonly StarWarsRepositoryContract $repository
    ) {}

    public function getResources(): array
    {
        return $this->repository->getRoot();
    }

    public function getPeople(?string $search = null, int $page = 1): array
    {
        return $this->getResourceData('people', $search, $page);
    }

    public function getPerson(int $id): array
    {
        return $this->repository->getById('people', $id);
    }

    public function getPersonBasic(int $id): array
    {
        return $this->repository->getBasicById('people', $id);
    }

    public function getPersonFilms(int $id): array
    {
        $person = $this->repository->getBasicById('people', $id);

        if (empty($person['films'])) {
            return [];
        }

        return $this->repository->resolveFilmsForResource($person['films']);
    }

    public function getFilms(?string $search = null, int $page = 1): array
    {
        return $this->getResourceData('films', $search, $page);
    }

    public function getFilm(int $id): array
    {
        return $this->repository->getById('films', $id);
    }

    public function getFilmBasic(int $id): array
    {
        return $this->repository->getBasicById('films', $id);
    }

    public function getFilmCharacters(int $id): array
    {
        $film = $this->repository->getBasicById('films', $id);

        if (empty($film['characters'])) {
            return [];
        }

        return $this->repository->resolveCharactersForResource($film['characters']);
    }

    public function getStarships(?string $search = null, int $page = 1): array
    {
        return $this->getResourceData('starships', $search, $page);
    }

    public function getStarship(int $id): array
    {
        return $this->repository->getById('starships', $id);
    }

    public function getVehicles(?string $search = null, int $page = 1): array
    {
        return $this->getResourceData('vehicles', $search, $page);
    }

    public function getVehicle(int $id): array
    {
        return $this->repository->getById('vehicles', $id);
    }

    public function getSpecies(?string $search = null, int $page = 1): array
    {
        return $this->getResourceData('species', $search, $page);
    }

    public function getSpeciesById(int $id): array
    {
        return $this->repository->getById('species', $id);
    }

    public function getPlanets(?string $search = null, int $page = 1): array
    {
        return $this->getResourceData('planets', $search, $page);
    }

    public function getPlanet(int $id): array
    {
        return $this->repository->getById('planets', $id);
    }

    public function search(string $resource, string $query, int $page = 1): array
    {
        $this->validateResource($resource);

        return $this->repository->search($resource, $query, $page);
    }

    private function getResourceData(string $resource, ?string $search, int $page): array
    {
        return $search
            ? $this->repository->search($resource, $search, $page)
            : $this->repository->getPaginated("/{$resource}/", $page);
    }

    private function validateResource(string $resource): void
    {
        if (! in_array($resource, self::VALID_RESOURCES)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid resource "%s". Valid resources: %s',
                    $resource,
                    implode(', ', self::VALID_RESOURCES)
                )
            );
        }
    }
}
