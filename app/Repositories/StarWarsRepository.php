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
                    'User-Agent' => 'StarWarsApp/1.0',
                ])
                ->get($url, $params);

            if (! $response->successful()) {
                throw new StarWarsApiException(
                    'Failed to fetch data from Star Wars API',
                    $response->status()
                );
            }

            $data = $response->json();
            return $this->transformDataWithIds($data);
        } catch (RequestException $e) {
            Log::error('Star Wars API request failed', [
                'endpoint' => $endpoint,
                'params' => $params,
                'error' => $e->getMessage(),
            ]);

            throw new StarWarsApiException(
                'Failed to connect to Star Wars API: '.$e->getMessage(),
                $e->getCode()
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
        $data = $this->get("/{$resource}/{$id}/");
        
        if ($resource === 'people' && isset($data['films'])) {
            $data['films'] = $this->resolveFilmTitles($data['films']);
        }
        
        if ($resource === 'films' && isset($data['characters'])) {
            $data['characters'] = $this->resolveCharacterNames($data['characters']);
        }
        
        return $data;
    }

    public function search(string $resource, string $query, int $page = 1): array
    {
        return $this->get("/{$resource}/", [
            'search' => $query,
            'page' => $page,
        ]);
    }

    /**
     * Extract ID from SWAPI URL
     */
    private function extractIdFromUrl(string $url): int
    {
        if (preg_match('/\/(\d+)\/$/', $url, $matches)) {
            return (int) $matches[1];
        }

        return 0;
    }

    /**
     * Transform data to include IDs extracted from URLs
     */
    private function transformDataWithIds(array $data): array
    {
        if (isset($data['url'])) {
            $data['id'] = $this->extractIdFromUrl($data['url']);
        }

        if (isset($data['results']) && is_array($data['results'])) {
            $data['results'] = array_map(function ($item) {
                if (isset($item['url'])) {
                    $item['id'] = $this->extractIdFromUrl($item['url']);
                }
                return $item;
            }, $data['results']);
        }

        return $data;
    }

    /**
     * Resolve film URLs to actual movie titles with IDs
     */
    private function resolveFilmTitles(array $filmUrls): array
    {
        $films = [];
        
        foreach ($filmUrls as $filmUrl) {
            try {
                $filmId = $this->extractIdFromUrl($filmUrl);
                
                if ($filmId > 0) {
                    $response = Http::timeout(self::TIMEOUT)
                        ->withHeaders([
                            'Accept' => 'application/json',
                            'User-Agent' => 'StarWarsApp/1.0',
                        ])
                        ->get($filmUrl);
                    
                    if ($response->successful()) {
                        $filmData = $response->json();
                        if (isset($filmData['title'])) {
                            $films[] = [
                                'id' => $filmId,
                                'title' => $filmData['title']
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to resolve film title', [
                    'url' => $filmUrl,
                    'error' => $e->getMessage()
                ]);
                
                continue;
            }
        }
        
        return $films;
    }

    /**
     * Resolve character URLs to actual character names with IDs
     */
    private function resolveCharacterNames(array $characterUrls): array
    {
        $characters = [];
        
        foreach ($characterUrls as $characterUrl) {
            try {
                $characterId = $this->extractIdFromUrl($characterUrl);
                
                if ($characterId > 0) {
                    $response = Http::timeout(self::TIMEOUT)
                        ->withHeaders([
                            'Accept' => 'application/json',
                            'User-Agent' => 'StarWarsApp/1.0',
                        ])
                        ->get($characterUrl);
                    
                    if ($response->successful()) {
                        $characterData = $response->json();
                        if (isset($characterData['name'])) {
                            $characters[] = [
                                'id' => $characterId,
                                'name' => $characterData['name']
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to resolve character name', [
                    'url' => $characterUrl,
                    'error' => $e->getMessage()
                ]);
                
                continue;
            }
        }
        
        return $characters;
    }
}
