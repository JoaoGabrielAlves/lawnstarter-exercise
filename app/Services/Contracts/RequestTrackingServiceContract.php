<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

interface RequestTrackingServiceContract
{
    /**
     * Determine if the request should be tracked
     */
    public function shouldTrackRequest(Request $request): bool;

    /**
     * Extract request data for logging
     */
    public function extractRequestData(Request $request, Response $response, float $responseTime): array;

    /**
     * Queue request for asynchronous logging
     */
    public function queueRequestForLogging(array $requestData): void;
}
