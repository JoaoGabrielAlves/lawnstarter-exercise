<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\QueryLog;
use App\Models\QueryStatistics;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

interface StatisticsRepositoryContract
{
    /**
     * Log an API request
     */
    public function logRequest(array $data): QueryLog;

    /**
     * Get query logs within date range
     */
    public function getQueryLogsInDateRange(Carbon $from, Carbon $to): Collection;

    /**
     * Get successful query logs within date range
     */
    public function getSuccessfulQueryLogsInDateRange(Carbon $from, Carbon $to): Collection;

    /**
     * Get most popular endpoints
     */
    public function getMostPopularEndpoints(Carbon $from, Carbon $to, int $limit = 10): array;

    /**
     * Get error rate statistics
     */
    public function getErrorRateStats(Carbon $from, Carbon $to): array;

    /**
     * Get performance metrics with percentiles
     */
    public function getPerformanceMetrics(Carbon $from, Carbon $to): array;

    /**
     * Get throughput statistics (requests per hour)
     */
    public function getThroughputStats(Carbon $from, Carbon $to): array;

    /**
     * Get top IP addresses consuming the API
     */
    public function getTopConsumers(Carbon $from, Carbon $to, int $limit = 10): array;

    /**
     * Get endpoint performance comparison
     */
    public function getEndpointPerformanceComparison(Carbon $from, Carbon $to): array;

    /**
     * Get response time statistics
     */
    public function getResponseTimeStats(Carbon $from, Carbon $to): array;

    /**
     * Get hourly query statistics
     */
    public function getHourlyQueryStats(Carbon $from, Carbon $to): array;

    /**
     * Get resource type statistics
     */
    public function getResourceTypeStats(Carbon $from, Carbon $to): array;

    /**
     * Get all computed statistics
     */
    public function getAllStatistics(): Collection;

    /**
     * Get statistics by type
     */
    public function getStatisticsByType(string $type): ?QueryStatistics;

    /**
     * Update or create statistics for a given type
     */
    public function updateOrCreateStatistics(
        string $type,
        array $data,
        ?float $averageResponseTime = null,
        ?int $totalQueries = null
    ): QueryStatistics;

    /**
     * Get last computed time across all statistics
     */
    public function getLastComputedTime(): ?Carbon;
}
