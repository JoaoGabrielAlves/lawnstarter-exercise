<?php

declare(strict_types=1);

namespace App\Services;

use App\Jobs\LogApiRequestJob;
use App\Services\Contracts\RequestTrackingServiceContract;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

final class RequestTrackingService implements RequestTrackingServiceContract
{
    /**
     * Determine if the request should be tracked
     */
    public function shouldTrackRequest(Request $request): bool
    {
        return $request->is('api/v1/starwars*');
    }

    /**
     * Extract request data for logging
     */
    public function extractRequestData(Request $request, Response $response, float $responseTime): array
    {
        return [
            'resource_type' => $this->extractResourceType($request),
            'query_params' => $this->extractQueryParams($request),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'response_time_ms' => $responseTime,
            'response_status_code' => $response->getStatusCode(),
            'endpoint' => $request->getPathInfo(),
            'query_data' => [
                'method' => $request->method(),
                'query_string' => $request->getQueryString(),
                'all_params' => $request->all(),
                'route_params' => $request->route() ? $request->route()->parameters() : [],
            ],
        ];
    }

    /**
     * Queue request for asynchronous logging
     */
    public function queueRequestForLogging(array $requestData): void
    {
        try {
            LogApiRequestJob::dispatch($requestData);
        } catch (Exception $e) {
            // Log the error but don't break the request
            Log::error('Failed to queue API request for logging', [
                'error' => $e->getMessage(),
                'request_data' => $requestData,
            ]);
        }
    }

    /**
     * Extract resource type from the request path
     */
    private function extractResourceType(Request $request): string
    {
        $path = $request->getPathInfo();

        // Match patterns like /api/v1/starwars/people, /api/v1/starwars/search/films
        if (preg_match('/\/api\/v1\/starwars\/(?:search\/)?([^\/]+)/', $path, $matches)) {
            return $matches[1];
        }

        // If we can't determine the resource type, check route parameters
        if ($request->route() && $request->route()->hasParameter('resource')) {
            return $request->route()->parameter('resource');
        }

        return 'unknown';
    }

    /**
     * Extract query parameters for analysis
     */
    private function extractQueryParams(Request $request): ?string
    {
        $params = [];

        // Get search query
        if ($request->has('search')) {
            $params[] = 'search:'.$request->input('search');
        }

        if ($request->has('q')) {
            $params[] = 'q:'.$request->input('q');
        }

        // Get pagination
        if ($request->has('page')) {
            $params[] = 'page:'.$request->input('page');
        }

        return empty($params) ? null : implode('|', $params);
    }
}
