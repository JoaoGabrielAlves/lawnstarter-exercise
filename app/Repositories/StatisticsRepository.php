<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\QueryLog;
use App\Models\QueryStatistics;
use App\Repositories\Contracts\StatisticsRepositoryContract;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class StatisticsRepository implements StatisticsRepositoryContract
{
    /**
     * Log an API request
     */
    public function logRequest(array $data): QueryLog
    {
        return QueryLog::query()->create($data);
    }

    /**
     * Get query logs within date range
     */
    public function getQueryLogsInDateRange(Carbon $from, Carbon $to): Collection
    {
        return QueryLog::query()->inDateRange($from, $to)->get();
    }

    /**
     * Get successful query logs within date range
     */
    public function getSuccessfulQueryLogsInDateRange(Carbon $from, Carbon $to): Collection
    {
        return QueryLog::query()->inDateRange($from, $to)->successful()->get();
    }

    /**
     * Get most popular endpoints
     */
    public function getMostPopularEndpoints(Carbon $from, Carbon $to, int $limit = 10): array
    {
        $totalRequests = QueryLog::query()->inDateRange($from, $to)->count();

        if ($totalRequests === 0) {
            return [
                'endpoints' => [],
                'total' => 0,
            ];
        }

        $popularEndpoints = QueryLog::query()->inDateRange($from, $to)
            ->select(
                'endpoint',
                DB::raw('COUNT(*) as hit_count'),
                DB::raw('AVG(response_time_ms) as avg_response_time'),
                DB::raw('(COUNT(*) * 100.0 / ?) as percentage')
            )
            ->addBinding($totalRequests, 'select')
            ->groupBy('endpoint')
            ->orderByDesc('hit_count')
            ->limit($limit)
            ->get()
            ->map(function ($endpoint) {
                return [
                    'endpoint' => $endpoint->endpoint,
                    'hit_count' => (int) $endpoint->hit_count,
                    'avg_response_time_ms' => round((float) $endpoint->avg_response_time, 2),
                    'percentage' => round((float) $endpoint->percentage, 2),
                ];
            })
            ->toArray();

        return [
            'endpoints' => $popularEndpoints,
            'total' => $totalRequests,
        ];
    }

    /**
     * Get error rate statistics
     */
    public function getErrorRateStats(Carbon $from, Carbon $to): array
    {
        $totalRequests = QueryLog::query()->inDateRange($from, $to)->count();
        $errorRequests = QueryLog::query()->inDateRange($from, $to)->where('response_status_code', '>=', 400)->count();

        $errorRate = $totalRequests > 0 ? round(($errorRequests / $totalRequests) * 100, 2) : 0;

        // Get error breakdown by status code
        $errorBreakdown = QueryLog::query()->inDateRange($from, $to)
            ->where('response_status_code', '>=', 400)
            ->select('response_status_code', DB::raw('COUNT(*) as count'))
            ->groupBy('response_status_code')
            ->orderByDesc('count')
            ->get()
            ->map(function ($error) {
                return [
                    'status_code' => (int) $error->response_status_code,
                    'count' => (int) $error->count,
                ];
            })
            ->toArray();

        // Get error breakdown by endpoint
        $errorsByEndpoint = QueryLog::query()->inDateRange($from, $to)
            ->where('response_status_code', '>=', 400)
            ->select('endpoint', 'response_status_code', DB::raw('COUNT(*) as count'))
            ->groupBy('endpoint', 'response_status_code')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(function ($error) {
                return [
                    'endpoint' => $error->endpoint,
                    'status_code' => (int) $error->response_status_code,
                    'count' => (int) $error->count,
                ];
            })
            ->toArray();

        return [
            'overall_error_rate' => $errorRate,
            'total_requests' => $totalRequests,
            'total_errors' => $errorRequests,
            'error_breakdown' => $errorBreakdown,
            'errors_by_endpoint' => $errorsByEndpoint,
        ];
    }

    /**
     * Get performance metrics with percentiles
     */
    public function getPerformanceMetrics(Carbon $from, Carbon $to): array
    {
        $stats = QueryLog::query()->inDateRange($from, $to)
            ->successful()
            ->selectRaw('
                AVG(response_time_ms) as avg_response_time,
                MIN(response_time_ms) as min_response_time,
                MAX(response_time_ms) as max_response_time,
                COUNT(*) as total_requests
            ')
            ->first();

        // Get percentiles (approximation using ORDER BY and LIMIT/OFFSET)
        $totalCount = (int) ($stats->total_requests ?? 0);
        $percentiles = [];

        if ($totalCount > 0) {
            $percentileQueries = [
                'p50' => intval($totalCount * 0.5),
                'p90' => intval($totalCount * 0.9),
                'p95' => intval($totalCount * 0.95),
                'p99' => intval($totalCount * 0.99),
            ];

            foreach ($percentileQueries as $percentile => $offset) {
                $result = QueryLog::query()->inDateRange($from, $to)
                    ->successful()
                    ->orderBy('response_time_ms')
                    ->offset($offset)
                    ->limit(1)
                    ->value('response_time_ms');

                $percentiles[$percentile] = $result ? round((float) $result, 2) : 0;
            }
        }

        return [
            'average_ms' => $stats->avg_response_time ? round((float) $stats->avg_response_time, 2) : 0,
            'minimum_ms' => $stats->min_response_time ? round((float) $stats->min_response_time, 2) : 0,
            'maximum_ms' => $stats->max_response_time ? round((float) $stats->max_response_time, 2) : 0,
            'total_requests' => $totalCount,
            'percentiles' => $percentiles,
        ];
    }

    /**
     * Get throughput statistics (requests per hour)
     */
    public function getThroughputStats(Carbon $from, Carbon $to): array
    {
        $hourlyStats = QueryLog::query()->inDateRange($from, $to)
            ->select(
                DB::raw('DATE_TRUNC(\'hour\', created_at) as hour'),
                DB::raw('COUNT(*) as requests_count')
            )
            ->groupBy(DB::raw('DATE_TRUNC(\'hour\', created_at)'))
            ->orderBy('hour')
            ->get()
            ->map(function ($stat) {
                return [
                    'hour' => $stat->hour,
                    'requests_count' => (int) $stat->requests_count,
                ];
            })
            ->toArray();

        $totalRequests = array_sum(array_column($hourlyStats, 'requests_count'));
        $totalHours = count($hourlyStats);
        $avgRequestsPerHour = $totalHours > 0 ? round($totalRequests / $totalHours, 2) : 0;

        return [
            'hourly_breakdown' => $hourlyStats,
            'total_requests' => $totalRequests,
            'total_hours' => $totalHours,
            'average_requests_per_hour' => $avgRequestsPerHour,
        ];
    }

    /**
     * Get top IP addresses consuming the API
     */
    public function getTopConsumers(Carbon $from, Carbon $to, int $limit = 10): array
    {
        $topConsumers = QueryLog::query()->inDateRange($from, $to)
            ->select(
                'ip_address',
                DB::raw('COUNT(*) as request_count'),
                DB::raw('AVG(response_time_ms) as avg_response_time')
            )
            ->groupBy('ip_address')
            ->orderByDesc('request_count')
            ->limit($limit)
            ->get()
            ->map(function ($consumer) {
                return [
                    'ip_address' => $consumer->ip_address,
                    'request_count' => (int) $consumer->request_count,
                    'avg_response_time_ms' => round((float) $consumer->avg_response_time, 2),
                ];
            })
            ->toArray();

        return [
            'top_consumers' => $topConsumers,
        ];
    }

    /**
     * Get endpoint performance comparison
     */
    public function getEndpointPerformanceComparison(Carbon $from, Carbon $to): array
    {
        $endpointStats = QueryLog::query()->inDateRange($from, $to)
            ->select(
                'endpoint',
                DB::raw('COUNT(*) as total_requests'),
                DB::raw('AVG(response_time_ms) as avg_response_time'),
                DB::raw('MIN(response_time_ms) as min_response_time'),
                DB::raw('MAX(response_time_ms) as max_response_time'),
                DB::raw('SUM(CASE WHEN response_status_code >= 400 THEN 1 ELSE 0 END) as error_count')
            )
            ->groupBy('endpoint')
            ->orderByDesc('total_requests')
            ->get()
            ->map(function ($stat) {
                $errorRate = $stat->total_requests > 0 ?
                    round(($stat->error_count / $stat->total_requests) * 100, 2) : 0;

                return [
                    'endpoint' => $stat->endpoint,
                    'total_requests' => (int) $stat->total_requests,
                    'avg_response_time_ms' => round((float) $stat->avg_response_time, 2),
                    'min_response_time_ms' => round((float) $stat->min_response_time, 2),
                    'max_response_time_ms' => round((float) $stat->max_response_time, 2),
                    'error_count' => (int) $stat->error_count,
                    'error_rate_percent' => $errorRate,
                ];
            })
            ->toArray();

        return [
            'endpoint_performance' => $endpointStats,
        ];
    }

    /**
     * Get response time statistics
     */
    public function getResponseTimeStats(Carbon $from, Carbon $to): array
    {
        $stats = QueryLog::query()->inDateRange($from, $to)
            ->successful()
            ->selectRaw('
                AVG(response_time_ms) as avg_response_time,
                MIN(response_time_ms) as min_response_time,
                MAX(response_time_ms) as max_response_time,
                COUNT(*) as total_requests
            ')
            ->first();

        return [
            'average_ms' => $stats->avg_response_time ? round((float) $stats->avg_response_time, 2) : 0,
            'minimum_ms' => $stats->min_response_time ? round((float) $stats->min_response_time, 2) : 0,
            'maximum_ms' => $stats->max_response_time ? round((float) $stats->max_response_time, 2) : 0,
            'total_requests' => (int) ($stats->total_requests ?? 0),
        ];
    }

    /**
     * Get hourly query statistics
     */
    public function getHourlyQueryStats(Carbon $from, Carbon $to): array
    {
        $hourlyStats = QueryLog::query()->inDateRange($from, $to)
            ->successful()
            ->select(
                DB::raw('CAST(EXTRACT(HOUR FROM created_at) AS INTEGER) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy(DB::raw('EXTRACT(HOUR FROM created_at)'))
            ->orderByDesc('count')
            ->get()
            ->map(function ($stat) {
                return [
                    'hour' => (int) $stat->hour,
                    'hour_formatted' => sprintf('%02d:00', (int) $stat->hour),
                    'count' => (int) $stat->count,
                ];
            })
            ->toArray();

        $totalRequests = array_sum(array_column($hourlyStats, 'count'));

        return [
            'hours' => array_map(static function ($stat) use ($totalRequests) {
                $stat['percentage'] = $totalRequests > 0 ? round(($stat['count'] / $totalRequests) * 100, 2) : 0;

                return $stat;
            }, $hourlyStats),
            'total' => $totalRequests,
        ];
    }

    /**
     * Get resource type statistics
     */
    public function getResourceTypeStats(Carbon $from, Carbon $to): array
    {
        $resourceStats = QueryLog::query()->inDateRange($from, $to)
            ->successful()
            ->select(
                'resource_type',
                DB::raw('COUNT(*) as count'),
                DB::raw('AVG(response_time_ms) as avg_response_time')
            )
            ->groupBy('resource_type')
            ->orderByDesc('count')
            ->get()
            ->map(function ($stat) {
                return [
                    'resource_type' => $stat->resource_type,
                    'count' => (int) $stat->count,
                    'average_response_time_ms' => round((float) $stat->avg_response_time, 2),
                ];
            })
            ->toArray();

        $totalRequests = array_sum(array_column($resourceStats, 'count'));

        return [
            'resources' => array_map(static function ($stat) use ($totalRequests) {
                $stat['percentage'] = $totalRequests > 0 ? round(($stat['count'] / $totalRequests) * 100, 2) : 0;

                return $stat;
            }, $resourceStats),
            'total' => $totalRequests,
        ];
    }

    /**
     * Get all computed statistics
     */
    public function getAllStatistics(): Collection
    {
        return QueryStatistics::all();
    }

    /**
     * Get statistics by type
     */
    public function getStatisticsByType(string $type): ?QueryStatistics
    {
        return QueryStatistics::query()->where('statistic_type', '=', $type)->first();
    }

    /**
     * Update or create statistics for a given type
     */
    public function updateOrCreateStatistics(
        string $type,
        array $data,
        ?float $averageResponseTime = null,
        ?int $totalQueries = null
    ): QueryStatistics {
        return QueryStatistics::query()->updateOrCreate(
            ['statistic_type' => $type],
            [
                'data' => $data,
                'average_response_time' => $averageResponseTime,
                'total_queries' => $totalQueries,
                'computed_at' => now(),
            ]
        );
    }

    /**
     * Get last computed time across all statistics
     */
    public function getLastComputedTime(): ?Carbon
    {
        $lastComputed = QueryStatistics::max('computed_at');

        return $lastComputed ? Carbon::parse($lastComputed) : null;
    }
}
