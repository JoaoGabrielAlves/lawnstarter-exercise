<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use Carbon\Carbon;

interface StatisticsServiceContract
{
    /**
     * Get all computed statistics formatted for API response
     */
    public function getAllStatistics(): array;

    /**
     * Get statistics by type
     */
    public function getStatisticsByType(string $type): ?array;

    /**
     * Get statistics summary
     */
    public function getStatisticsSummary(): array;

    /**
     * Compute all statistics for a given time range
     */
    public function computeAllStatistics(?Carbon $from = null, ?Carbon $to = null): void;

    /**
     * Compute popular endpoints statistics
     */
    public function computePopularEndpoints(Carbon $from, Carbon $to): array;

    /**
     * Compute performance metrics statistics
     */
    public function computePerformanceMetrics(Carbon $from, Carbon $to): array;

    /**
     * Compute error rate statistics
     */
    public function computeErrorRateStats(Carbon $from, Carbon $to): array;

    /**
     * Compute throughput statistics
     */
    public function computeThroughputStats(Carbon $from, Carbon $to): array;

    /**
     * Compute top consumers statistics
     */
    public function computeTopConsumers(Carbon $from, Carbon $to): array;

    /**
     * Compute endpoint performance statistics
     */
    public function computeEndpointPerformance(Carbon $from, Carbon $to): array;

    /**
     * Compute popular hours statistics
     */
    public function computePopularHours(Carbon $from, Carbon $to): array;

    /**
     * Compute resource type statistics
     */
    public function computeResourceTypeStats(Carbon $from, Carbon $to): array;

    /**
     * Get available statistic types with descriptions
     */
    public function getAvailableStatisticTypes(): array;

    /**
     * Get last computed time across all statistics
     */
    public function getLastComputedTime(): ?string;

    /**
     * Log an API request (used by middleware)
     */
    public function logApiRequest(array $requestData): void;
}
