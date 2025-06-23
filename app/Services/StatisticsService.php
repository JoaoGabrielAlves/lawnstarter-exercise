<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\StatisticsComputed;
use App\Repositories\Contracts\StatisticsRepositoryContract;
use App\Services\Contracts\StatisticsServiceContract;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

final class StatisticsService implements StatisticsServiceContract
{
    public function __construct(
        private readonly StatisticsRepositoryContract $repository
    ) {}

    /**
     * Get all computed statistics formatted for API response
     */
    public function getAllStatistics(): array
    {
        $statistics = $this->repository->getAllStatistics();

        return $statistics->mapWithKeys(function ($stat) {
            return [$stat->statistic_type => [
                'data' => $stat->data,
                'average_response_time' => $stat->average_response_time,
                'total_queries' => $stat->total_queries,
                'computed_at' => $stat->computed_at,
                'updated_at' => $stat->updated_at,
            ]];
        })->toArray();
    }

    /**
     * Get statistics by type
     */
    public function getStatisticsByType(string $type): ?array
    {
        $statistics = $this->repository->getStatisticsByType($type);

        if (! $statistics) {
            return null;
        }

        return [
            'type' => $statistics->statistic_type,
            'data' => $statistics->data,
            'average_response_time' => $statistics->average_response_time,
            'total_queries' => $statistics->total_queries,
            'computed_at' => $statistics->computed_at,
            'updated_at' => $statistics->updated_at,
        ];
    }

    /**
     * Get statistics summary
     */
    public function getStatisticsSummary(): array
    {
        $allStats = $this->repository->getAllStatistics();

        $summary = [
            'total_statistic_types' => $allStats->count(),
            'last_computed' => $allStats->max('computed_at'),
            'available_types' => $this->getAvailableStatisticTypes(),
            'quick_stats' => [],
        ];

        foreach ($allStats as $stat) {
            $summary['quick_stats'][$stat->statistic_type] = [
                'total_queries' => $stat->total_queries,
                'average_response_time' => $stat->average_response_time,
                'computed_at' => $stat->computed_at,
            ];
        }

        return $summary;
    }

    /**
     * Compute all statistics for a given time range
     *
     * @throws Exception
     */
    public function computeAllStatistics(?Carbon $from = null, ?Carbon $to = null): void
    {
        try {
            Log::info('Starting statistics computation via service');

            $from = $from ?? now()->subDay();
            $to = $to ?? now();

            $this->computePopularEndpoints($from, $to);
            $this->computePerformanceMetrics($from, $to);
            $this->computeErrorRateStats($from, $to);
            $this->computeThroughputStats($from, $to);
            $this->computeTopConsumers($from, $to);
            $this->computeEndpointPerformance($from, $to);
            $this->computePopularHours($from, $to);

            event(new StatisticsComputed);

            Log::info('Statistics computation completed successfully via service');
        } catch (Exception $e) {
            Log::error('Failed to compute statistics via service', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Compute popular endpoints statistics
     */
    public function computePopularEndpoints(Carbon $from, Carbon $to): array
    {
        $popularEndpointsData = $this->repository->getMostPopularEndpoints($from, $to);

        $this->repository->updateOrCreateStatistics(
            'popular_endpoints',
            $popularEndpointsData['endpoints'],
            null,
            $popularEndpointsData['total']
        );

        return $popularEndpointsData['endpoints'];
    }

    /**
     * Compute performance metrics statistics
     */
    public function computePerformanceMetrics(Carbon $from, Carbon $to): array
    {
        $performanceData = $this->repository->getPerformanceMetrics($from, $to);

        $this->repository->updateOrCreateStatistics(
            'performance_metrics',
            $performanceData,
            $performanceData['average_ms'],
            $performanceData['total_requests']
        );

        return $performanceData;
    }

    /**
     * Compute error rate statistics
     */
    public function computeErrorRateStats(Carbon $from, Carbon $to): array
    {
        $errorRateData = $this->repository->getErrorRateStats($from, $to);

        $this->repository->updateOrCreateStatistics(
            'error_rate_stats',
            $errorRateData,
            null,
            $errorRateData['total_requests']
        );

        return $errorRateData;
    }

    /**
     * Compute throughput statistics
     */
    public function computeThroughputStats(Carbon $from, Carbon $to): array
    {
        $throughputData = $this->repository->getThroughputStats($from, $to);

        $this->repository->updateOrCreateStatistics(
            'throughput_stats',
            $throughputData,
            $throughputData['average_requests_per_hour'],
            $throughputData['total_requests']
        );

        return $throughputData;
    }

    /**
     * Compute top consumers statistics
     */
    public function computeTopConsumers(Carbon $from, Carbon $to): array
    {
        $topConsumersData = $this->repository->getTopConsumers($from, $to);

        $this->repository->updateOrCreateStatistics(
            'top_consumers',
            $topConsumersData['top_consumers'],
            null,
            count($topConsumersData['top_consumers'])
        );

        return $topConsumersData['top_consumers'];
    }

    /**
     * Compute endpoint performance statistics
     */
    public function computeEndpointPerformance(Carbon $from, Carbon $to): array
    {
        $endpointPerformanceData = $this->repository->getEndpointPerformanceComparison($from, $to);

        $this->repository->updateOrCreateStatistics(
            'endpoint_performance',
            $endpointPerformanceData['endpoint_performance'],
            null,
            count($endpointPerformanceData['endpoint_performance'])
        );

        return $endpointPerformanceData['endpoint_performance'];
    }

    /**
     * Compute popular hours statistics
     */
    public function computePopularHours(Carbon $from, Carbon $to): array
    {
        $popularHoursData = $this->repository->getHourlyQueryStats($from, $to);

        $this->repository->updateOrCreateStatistics(
            'popular_hours',
            $popularHoursData['hours'],
            null,
            $popularHoursData['total']
        );

        return $popularHoursData['hours'];
    }

    /**
     * Compute resource type statistics
     */
    public function computeResourceTypeStats(Carbon $from, Carbon $to): array
    {
        $resourceTypeData = $this->repository->getResourceTypeStats($from, $to);

        $this->repository->updateOrCreateStatistics(
            'resource_type_stats',
            $resourceTypeData['resources'],
            null,
            $resourceTypeData['total']
        );

        return $resourceTypeData['resources'];
    }

    /**
     * Get available statistic types with descriptions
     */
    public function getAvailableStatisticTypes(): array
    {
        return [
            'popular_endpoints' => 'Most popular API endpoints with hit counts and response times',
            'performance_metrics' => 'Detailed performance metrics including percentiles',
            'error_rate_stats' => 'Error rates and breakdown by status codes and endpoints',
            'throughput_stats' => 'Request throughput and hourly breakdown',
            'top_consumers' => 'Top IP addresses consuming the API',
            'endpoint_performance' => 'Performance comparison across all endpoints',
            'popular_hours' => 'Most popular hours of the day for API usage',
            'resource_type_stats' => 'Statistics by resource type (people, films, etc.)',
        ];
    }

    /**
     * Get last computed time across all statistics
     */
    public function getLastComputedTime(): ?string
    {
        return $this->repository->getLastComputedTime()?->toISOString();
    }

    /**
     * Log an API request (used by middleware)
     */
    public function logApiRequest(array $requestData): void
    {
        try {
            $this->repository->logRequest($requestData);
        } catch (Exception $e) {
            Log::error('Failed to log API request via service', [
                'error' => $e->getMessage(),
                'request_data' => $requestData,
            ]);
        }
    }
}
