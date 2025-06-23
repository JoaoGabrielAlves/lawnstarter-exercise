<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class QueryLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'resource_type',
        'query_params',
        'ip_address',
        'user_agent',
        'response_time_ms',
        'response_status_code',
        'endpoint',
        'query_data',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'query_data' => 'array',
        'response_time_ms' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope to filter by date range
     */
    public function scopeInDateRange($query, Carbon $from, Carbon $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Scope to filter by resource type
     */
    public function scopeByResourceType($query, string $resourceType)
    {
        return $query->where('resource_type', $resourceType);
    }

    /**
     * Scope to filter successful responses
     */
    public function scopeSuccessful($query)
    {
        return $query->whereBetween('response_status_code', [200, 299]);
    }

    /**
     * Get queries from the last hour
     */
    public function scopeLastHour($query)
    {
        return $query->where('created_at', '>=', now()->subHour());
    }

    /**
     * Get queries from the last 24 hours
     */
    public function scopeLast24Hours($query)
    {
        return $query->where('created_at', '>=', now()->subDay());
    }
}
