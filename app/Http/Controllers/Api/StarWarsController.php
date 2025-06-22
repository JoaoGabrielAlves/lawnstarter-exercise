<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StarWarsSearchRequest;
use App\Http\Resources\StarWarsCollection;
use App\Http\Resources\StarWarsResource;
use App\Services\Contracts\StarWarsServiceContract;
use Illuminate\Http\Request;

final class StarWarsController extends Controller
{
    public function __construct(
        private readonly StarWarsServiceContract $starWarsService
    ) {}

    /**
     * Get overview of all available resources
     */
    public function index(): StarWarsResource
    {
        return new StarWarsResource($this->starWarsService->getResources());
    }

    /**
     * Get all people with optional search
     */
    public function getPeople(Request $request): StarWarsCollection
    {
        return $this->getCollectionResponse(
            fn ($search, $page) => $this->starWarsService->getPeople($search, $page),
            $request
        );
    }

    /**
     * Get a specific person by ID
     */
    public function getPerson(int $id): StarWarsResource
    {
        return new StarWarsResource($this->starWarsService->getPerson($id));
    }

    /**
     * Get all films with optional search
     */
    public function getFilms(Request $request): StarWarsCollection
    {
        return $this->getCollectionResponse(
            fn ($search, $page) => $this->starWarsService->getFilms($search, $page),
            $request
        );
    }

    /**
     * Get a specific film by ID
     */
    public function getFilm(int $id): StarWarsResource
    {
        return new StarWarsResource($this->starWarsService->getFilm($id));
    }

    /**
     * Get all starships with optional search
     */
    public function getStarships(Request $request): StarWarsCollection
    {
        return $this->getCollectionResponse(
            fn ($search, $page) => $this->starWarsService->getStarships($search, $page),
            $request
        );
    }

    /**
     * Get a specific starship by ID
     */
    public function getStarship(int $id): StarWarsResource
    {
        return new StarWarsResource($this->starWarsService->getStarship($id));
    }

    /**
     * Get all vehicles with optional search
     */
    public function getVehicles(Request $request): StarWarsCollection
    {
        return $this->getCollectionResponse(
            fn ($search, $page) => $this->starWarsService->getVehicles($search, $page),
            $request
        );
    }

    /**
     * Get a specific vehicle by ID
     */
    public function getVehicle(int $id): StarWarsResource
    {
        return new StarWarsResource($this->starWarsService->getVehicle($id));
    }

    /**
     * Get all species with optional search
     */
    public function getSpecies(Request $request): StarWarsCollection
    {
        return $this->getCollectionResponse(
            fn ($search, $page) => $this->starWarsService->getSpecies($search, $page),
            $request
        );
    }

    /**
     * Get a specific species by ID
     */
    public function getSpeciesById(int $id): StarWarsResource
    {
        return new StarWarsResource($this->starWarsService->getSpeciesById($id));
    }

    /**
     * Get all planets with optional search
     */
    public function getPlanets(Request $request): StarWarsCollection
    {
        return $this->getCollectionResponse(
            fn ($search, $page) => $this->starWarsService->getPlanets($search, $page),
            $request
        );
    }

    /**
     * Get a specific planet by ID
     */
    public function getPlanet(int $id): StarWarsResource
    {
        return new StarWarsResource($this->starWarsService->getPlanet($id));
    }

    /**
     * Search within a specific resource type
     */
    public function search(string $resource, StarWarsSearchRequest $request): StarWarsCollection
    {
        $validated = $request->validated();

        $data = $this->starWarsService->search(
            $resource,
            $validated['q'],
            (int) ($validated['page'] ?? 1)
        );

        return new StarWarsCollection($data);
    }

    private function getCollectionResponse(callable $serviceMethod, Request $request): StarWarsCollection
    {
        $search = $request->query('search');
        $page = (int) $request->query('page', 1);

        return new StarWarsCollection($serviceMethod($search, $page));
    }
}
