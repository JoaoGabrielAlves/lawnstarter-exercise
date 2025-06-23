<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface StarWarsServiceContract
{
    /**
     * Get all available resources
     */
    public function getResources(): array;

    /**
     * Get all people with optional search
     */
    public function getPeople(?string $search = null, int $page = 1): array;

    /**
     * Get a specific person by ID
     */
    public function getPerson(int $id): array;

    /**
     * Get basic person data without resolved relationships
     */
    public function getPersonBasic(int $id): array;

    /**
     * Get resolved films for a person
     */
    public function getPersonFilms(int $id): array;

    /**
     * Get all films with optional search
     */
    public function getFilms(?string $search = null, int $page = 1): array;

    /**
     * Get a specific film by ID
     */
    public function getFilm(int $id): array;

    /**
     * Get basic film data without resolved relationships
     */
    public function getFilmBasic(int $id): array;

    /**
     * Get resolved characters for a film
     */
    public function getFilmCharacters(int $id): array;

    /**
     * Get all starships with optional search
     */
    public function getStarships(?string $search = null, int $page = 1): array;

    /**
     * Get a specific starship by ID
     */
    public function getStarship(int $id): array;

    /**
     * Get all vehicles with optional search
     */
    public function getVehicles(?string $search = null, int $page = 1): array;

    /**
     * Get a specific vehicle by ID
     */
    public function getVehicle(int $id): array;

    /**
     * Get all species with optional search
     */
    public function getSpecies(?string $search = null, int $page = 1): array;

    /**
     * Get a specific species by ID
     */
    public function getSpeciesById(int $id): array;

    /**
     * Get all planets with optional search
     */
    public function getPlanets(?string $search = null, int $page = 1): array;

    /**
     * Get a specific planet by ID
     */
    public function getPlanet(int $id): array;

    /**
     * Search within a specific resource type
     */
    public function search(string $resource, string $query, int $page = 1): array;
}
