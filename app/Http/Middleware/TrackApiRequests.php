<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Contracts\RequestTrackingServiceContract;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class TrackApiRequests
{
    public function __construct(
        private RequestTrackingServiceContract $requestTrackingService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Record start time and store it in the request for later use
        $request->attributes->set('start_time', microtime(true));

        return $next($request);
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     */
    public function terminate(Request $request, Response $response): void
    {
        if (! $this->requestTrackingService->shouldTrackRequest($request) ||
            ! $request->attributes->has('start_time')) {
            return;
        }

        $startTime = $request->attributes->get('start_time');
        $responseTime = (microtime(true) - $startTime) * 1000;

        $requestData = $this->requestTrackingService->extractRequestData($request, $response, $responseTime);
        $this->requestTrackingService->queueRequestForLogging($requestData);
    }
}
