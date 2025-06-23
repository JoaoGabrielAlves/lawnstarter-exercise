<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StarWarsSearchRequest;
use App\Services\Contracts\StarWarsServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class StarWarsController extends Controller
{
    public function __construct(
        private readonly StarWarsServiceContract $starWarsService
    ) {}

    /**
     * Get overview of all available resources
     */
    public function index(): JsonResponse
    {
        $data = $this->starWarsService->getResources();

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Get all people with optional search
     */
    public function getPeople(Request $request): JsonResponse
    {
        return $this->getCollectionResponse(
            fn ($search, $page) => $this->starWarsService->getPeople($search, $page),
            $request
        );
    }

    /**
     * Get a specific person by ID
     */
    public function getPerson(int $id): JsonResponse
    {
        $data = $this->starWarsService->getPerson($id);

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Get basic person data without resolved relationships (fast)
     */
    public function getPersonBasic(int $id): JsonResponse
    {
        $data = $this->starWarsService->getPersonBasic($id);

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Get resolved films for a person
     */
    public function getPersonFilms(int $id): JsonResponse
    {
        $data = $this->starWarsService->getPersonFilms($id);

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Get all films with optional search
     */
    public function getFilms(Request $request): JsonResponse
    {
        return $this->getCollectionResponse(
            fn ($search, $page) => $this->starWarsService->getFilms($search, $page),
            $request
        );
    }

    /**
     * Get a specific film by ID
     */
    public function getFilm(int $id): JsonResponse
    {
        $data = $this->starWarsService->getFilm($id);

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Get basic film data without resolved relationships (fast)
     */
    public function getFilmBasic(int $id): JsonResponse
    {
        $data = $this->starWarsService->getFilmBasic($id);

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Get resolved characters for a film
     */
    public function getFilmCharacters(int $id): JsonResponse
    {
        $data = $this->starWarsService->getFilmCharacters($id);

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Get all starships with optional search
     */
    public function getStarships(Request $request): JsonResponse
    {
        return $this->getCollectionResponse(
            fn ($search, $page) => $this->starWarsService->getStarships($search, $page),
            $request
        );
    }

    /**
     * Get a specific starship by ID
     */
    public function getStarship(int $id): JsonResponse
    {
        $data = $this->starWarsService->getStarship($id);

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Get all vehicles with optional search
     */
    public function getVehicles(Request $request): JsonResponse
    {
        return $this->getCollectionResponse(
            fn ($search, $page) => $this->starWarsService->getVehicles($search, $page),
            $request
        );
    }

    /**
     * Get a specific vehicle by ID
     */
    public function getVehicle(int $id): JsonResponse
    {
        $data = $this->starWarsService->getVehicle($id);

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Get all species with optional search
     */
    public function getSpecies(Request $request): JsonResponse
    {
        return $this->getCollectionResponse(
            fn ($search, $page) => $this->starWarsService->getSpecies($search, $page),
            $request
        );
    }

    /**
     * Get a specific species by ID
     */
    public function getSpeciesById(int $id): JsonResponse
    {
        $data = $this->starWarsService->getSpeciesById($id);

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Get all planets with optional search
     */
    public function getPlanets(Request $request): JsonResponse
    {
        return $this->getCollectionResponse(
            fn ($search, $page) => $this->starWarsService->getPlanets($search, $page),
            $request
        );
    }

    /**
     * Get a specific planet by ID
     */
    public function getPlanet(int $id): JsonResponse
    {
        $data = $this->starWarsService->getPlanet($id);

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Search within a specific resource type
     */
    public function search(string $resource, StarWarsSearchRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $data = $this->starWarsService->search(
            $resource,
            $validated['q'],
            (int) data_get($validated, 'page', 1)
        );

        return $this->formatCollectionResponse($data);
    }

    private function getCollectionResponse(callable $serviceMethod, Request $request): JsonResponse
    {
        $search = $request->query('search');
        $page = (int) $request->query('page', 1);

        $data = $serviceMethod($search, $page);

        return $this->formatCollectionResponse($data);
    }

    private function formatCollectionResponse(array $data): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => data_get($data, 'results', []),
            'meta' => [
                'count' => data_get($data, 'count', 0),
                'next' => data_get($data, 'next'),
                'previous' => data_get($data, 'previous'),
            ],
        ]);
    }
}
