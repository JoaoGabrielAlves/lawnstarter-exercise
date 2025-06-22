<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Exceptions\StarWarsApiException;
use App\Repositories\Contracts\StarWarsRepositoryContract;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class StarWarsRepository implements StarWarsRepositoryContract
{
    private const BASE_URL = 'https://swapi.py4e.com/api';

    private const TIMEOUT = 30;

    public function get(string $endpoint, array $params = []): array
    {
        try {
            $url = self::BASE_URL.'/'.ltrim($endpoint, '/');

            $response = Http::timeout(self::TIMEOUT)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'User-Agent' => 'Laravel-StarWars-App/1.0',
                ])
                ->get($url, $params);

            if ($response->failed()) {
                Log::warning('SWAPI request failed', [
                    'url' => $url,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new StarWarsApiException(
                    'Failed to fetch data from Star Wars API',
                    $response->status()
                );
            }

            return $response->json() ?? [];

        } catch (RequestException $e) {
            Log::error('SWAPI request exception', [
                'endpoint' => $endpoint,
                'params' => $params,
                'error' => $e->getMessage(),
            ]);

            throw new StarWarsApiException(
                'Connection error: '.$e->getMessage(),
                503
            );
        }
    }

    public function getRoot(): array
    {
        return $this->get('/');
    }

    public function getPaginated(string $endpoint, int $page = 1, array $params = []): array
    {
        $params['page'] = $page;

        return $this->get($endpoint, $params);
    }

    public function getById(string $resource, int $id): array
    {
        return $this->get("/{$resource}/{$id}/");
    }

    public function search(string $resource, string $query, int $page = 1): array
    {
        return $this->getPaginated("/{$resource}/", $page, ['search' => $query]);
    }
}
