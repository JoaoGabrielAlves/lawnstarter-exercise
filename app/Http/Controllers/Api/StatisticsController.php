<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Contracts\StatisticsServiceContract;
use Exception;
use Illuminate\Http\JsonResponse;

final class StatisticsController extends Controller
{
    public function __construct(
        private readonly StatisticsServiceContract $statisticsService
    ) {}

    /**
     * Get all computed statistics
     */
    public function index(): JsonResponse
    {
        try {
            $statistics = $this->statisticsService->getAllStatistics();

            return response()->json([
                'success' => true,
                'data' => [
                    'statistics' => $statistics,
                    'available_types' => $this->statisticsService->getAvailableStatisticTypes(),
                    'last_computed' => $this->statisticsService->getLastComputedTime(),
                ],
                'meta' => [
                    'computed_statistics_count' => count($statistics),
                    'next_computation' => 'Every 5 minutes',
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve statistics',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Get statistics by type
     */
    public function show(string $type): JsonResponse
    {
        try {
            $statistics = $this->statisticsService->getStatisticsByType($type);

            if (! $statistics) {
                return response()->json([
                    'success' => false,
                    'error' => 'Statistics type not found',
                    'available_types' => $this->statisticsService->getAvailableStatisticTypes(),
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $statistics,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve statistics',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Get summary of statistics
     */
    public function summary(): JsonResponse
    {
        try {
            $summary = $this->statisticsService->getStatisticsSummary();

            return response()->json([
                'success' => true,
                'data' => $summary,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve statistics summary',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }
}
