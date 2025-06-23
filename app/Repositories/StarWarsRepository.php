<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Exceptions\StarWarsApiException;
use App\Repositories\Contracts\StarWarsRepositoryContract;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

final class StarWarsRepository implements StarWarsRepositoryContract
{
    private const BASE_URL = 'https://swapi.py4e.com/api';

    private const TIMEOUT = 30;

    private const CACHE_TTL = 86400;

    private const CACHE_PREFIX = 'swapi';

    private const CACHE_KEY_BASIC = 'basic';

    private const CACHE_KEY_DETAILED = 'detailed';

    private const CACHE_KEY_FILM = 'film';

    private const CACHE_KEY_CHARACTER = 'character';

    public function get(string $endpoint, array $params = []): array
    {
        $cacheKey = $this->buildCacheKey(self::CACHE_KEY_BASIC, $endpoint, $params);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($endpoint, $params) {
            return $this->fetchFromApi($endpoint, $params);
        });
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
        $cacheKey = $this->buildCacheKey(self::CACHE_KEY_DETAILED, $resource, ['id' => $id]);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($resource, $id) {
            $data = $this->fetchFromApi("/{$resource}/{$id}/");

            if ($resource === 'people' && isset($data['films'])) {
                $data['films'] = $this->resolveFilmTitles($data['films']);
            }

            if ($resource === 'films' && isset($data['characters'])) {
                $data['characters'] = $this->resolveCharacterNames($data['characters']);
            }

            return $data;
        });
    }

    public function getBasicById(string $resource, int $id): array
    {
        $cacheKey = $this->buildCacheKey(self::CACHE_KEY_BASIC, $resource, ['id' => $id]);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($resource, $id) {
            return $this->fetchFromApi("/{$resource}/{$id}/");
        });
    }

    public function resolveFilmsForResource(array $filmUrls): array
    {
        if (empty($filmUrls)) {
            return [];
        }

        return $this->resolveFilmTitlesInParallel($filmUrls);
    }

    public function resolveCharactersForResource(array $characterUrls): array
    {
        if (empty($characterUrls)) {
            return [];
        }

        return $this->resolveCharacterNamesInParallel($characterUrls);
    }

    public function search(string $resource, string $query, int $page = 1): array
    {
        return $this->get("/{$resource}/", [
            'search' => $query,
            'page' => $page,
        ]);
    }

    /**
     * Build a unique cache key for the given parameters
     */
    private function buildCacheKey(string $type, string $identifier, array $params = []): string
    {
        $keyParts = [
            self::CACHE_PREFIX,
            $type,
            md5($identifier),
        ];

        if (! empty($params)) {
            ksort($params); // Sort params for consistent key generation
            $keyParts[] = md5(serialize($params));
        }

        return implode(':', $keyParts);
    }

    /**
     * Fetch data from the Star Wars API without caching
     *
     * @throws StarWarsApiException|ConnectionException
     */
    private function fetchFromApi(string $endpoint, array $params = []): array
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
        } catch (Throwable $e) {
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
     * Resolve film URLs to actual movie titles with IDs (sequential - legacy)
     */
    private function resolveFilmTitles(array $filmUrls): array
    {
        $films = [];

        foreach ($filmUrls as $filmUrl) {
            $filmId = $this->extractIdFromUrl($filmUrl);

            if ($filmId > 0) {
                $cacheKey = $this->buildCacheKey(self::CACHE_KEY_FILM, 'title', ['id' => $filmId]);

                $filmData = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($filmUrl) {
                    return $this->fetchFilmData($filmUrl);
                });

                if ($filmData) {
                    $films[] = $filmData;
                }
            }
        }

        return $films;
    }

    /**
     * Resolve character URLs to actual character names with IDs (sequential - legacy)
     */
    private function resolveCharacterNames(array $characterUrls): array
    {
        $characters = [];

        foreach ($characterUrls as $characterUrl) {
            $characterId = $this->extractIdFromUrl($characterUrl);

            if ($characterId > 0) {
                $cacheKey = $this->buildCacheKey(self::CACHE_KEY_CHARACTER, 'name', ['id' => $characterId]);

                $characterData = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($characterUrl) {
                    return $this->fetchCharacterData($characterUrl);
                });

                if ($characterData) {
                    $characters[] = $characterData;
                }
            }
        }

        return $characters;
    }

    /**
     * Resolve film URLs in parallel for better performance
     */
    private function resolveFilmTitlesInParallel(array $filmUrls): array
    {
        $films = [];
        $requests = [];
        $filmIds = [];

        foreach ($filmUrls as $filmUrl) {
            $filmId = $this->extractIdFromUrl($filmUrl);

            if ($filmId > 0) {
                $cacheKey = $this->buildCacheKey(self::CACHE_KEY_FILM, 'title', ['id' => $filmId]);
                $cachedData = Cache::get($cacheKey);

                if ($cachedData) {
                    $films[] = $cachedData;
                } else {
                    $requests[] = $filmUrl;
                    $filmIds[] = $filmId;
                }
            }
        }

        if (! empty($requests)) {
            $responses = Http::pool(static function ($pool) use ($requests) {
                foreach ($requests as $url) {
                    $pool->timeout(self::TIMEOUT)
                        ->withHeaders([
                            'Accept' => 'application/json',
                            'User-Agent' => 'StarWarsApp/1.0',
                        ])
                        ->get($url);
                }
            });

            foreach ($responses as $index => $response) {
                $filmId = $filmIds[$index];
                $filmUrl = $requests[$index];

                try {
                    if ($response->successful()) {
                        $filmData = $response->json();
                        if (isset($filmData['title'])) {
                            $result = [
                                'id' => $filmId,
                                'title' => $filmData['title'],
                            ];

                            $films[] = $result;

                            $cacheKey = $this->buildCacheKey(self::CACHE_KEY_FILM, 'title', ['id' => $filmId]);
                            Cache::put($cacheKey, $result, self::CACHE_TTL);
                        }
                    }
                } catch (Exception $e) {
                    Log::warning('Failed to resolve film title in parallel', [
                        'url' => $filmUrl,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return $films;
    }

    /**
     * Resolve character URLs in parallel for better performance
     */
    private function resolveCharacterNamesInParallel(array $characterUrls): array
    {
        $characters = [];
        $requests = [];
        $characterIds = [];

        foreach ($characterUrls as $characterUrl) {
            $characterId = $this->extractIdFromUrl($characterUrl);

            if ($characterId > 0) {
                $cacheKey = $this->buildCacheKey(self::CACHE_KEY_CHARACTER, 'name', ['id' => $characterId]);
                $cachedData = Cache::get($cacheKey);

                if ($cachedData) {
                    $characters[] = $cachedData;
                } else {
                    $requests[] = $characterUrl;
                    $characterIds[] = $characterId;
                }
            }
        }

        if (! empty($requests)) {
            $responses = Http::pool(static function ($pool) use ($requests) {
                foreach ($requests as $url) {
                    $pool->timeout(self::TIMEOUT)
                        ->withHeaders([
                            'Accept' => 'application/json',
                            'User-Agent' => 'StarWarsApp/1.0',
                        ])
                        ->get($url);
                }
            });

            foreach ($responses as $index => $response) {
                $characterId = $characterIds[$index];
                $characterUrl = $requests[$index];

                try {
                    if ($response->successful()) {
                        $characterData = $response->json();
                        if (isset($characterData['name'])) {
                            $result = [
                                'id' => $characterId,
                                'name' => $characterData['name'],
                            ];

                            $characters[] = $result;

                            $cacheKey = $this->buildCacheKey(self::CACHE_KEY_CHARACTER, 'name', ['id' => $characterId]);
                            Cache::put($cacheKey, $result, self::CACHE_TTL);
                        }
                    }
                } catch (Exception $e) {
                    Log::warning('Failed to resolve character name in parallel', [
                        'url' => $characterUrl,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return $characters;
    }

    /**
     * Fetch film data from API
     */
    private function fetchFilmData(string $filmUrl): ?array
    {
        try {
            $response = Http::timeout(self::TIMEOUT)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'User-Agent' => 'StarWarsApp/1.0',
                ])
                ->get($filmUrl);

            if ($response->successful()) {
                $filmData = $response->json();
                if (isset($filmData['title'])) {
                    return [
                        'id' => $this->extractIdFromUrl($filmUrl),
                        'title' => $filmData['title'],
                    ];
                }
            }
        } catch (Exception $e) {
            Log::warning('Failed to resolve film title', [
                'url' => $filmUrl,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Fetch character data from API
     */
    private function fetchCharacterData(string $characterUrl): ?array
    {
        try {
            $response = Http::timeout(self::TIMEOUT)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'User-Agent' => 'StarWarsApp/1.0',
                ])
                ->get($characterUrl);

            if ($response->successful()) {
                $characterData = $response->json();
                if (isset($characterData['name'])) {
                    return [
                        'id' => $this->extractIdFromUrl($characterUrl),
                        'name' => $characterData['name'],
                    ];
                }
            }
        } catch (Exception $e) {
            Log::warning('Failed to resolve character name', [
                'url' => $characterUrl,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }
}
