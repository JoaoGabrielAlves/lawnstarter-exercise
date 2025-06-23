<?php

declare(strict_types=1);

namespace App\Events;

use App\Services\Contracts\StatisticsServiceContract;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class StatisticsComputed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $statistics;

    /**
     * Create a new event instance.
     */
    public function __construct()
    {
        // Get all computed statistics via service
        $statisticsService = app(StatisticsServiceContract::class);
        $this->statistics = $statisticsService->getAllStatistics();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }

    /**
     * Get the statistics data
     */
    public function getStatistics(): array
    {
        return $this->statistics;
    }
}
