<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Contracts\StatisticsServiceContract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

final class LogApiRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 5;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly array $requestData
    ) {}

    /**
     * Execute the job.
     */
    public function handle(StatisticsServiceContract $statisticsService): void
    {
        try {
            $statisticsService->logApiRequest($this->requestData);
        } catch (\Exception $e) {
            Log::error('Failed to log API request in job', [
                'error' => $e->getMessage(),
                'request_data' => $this->requestData,
            ]);

            // Re-throw to mark job as failed
            throw $e;
        }
    }
}
