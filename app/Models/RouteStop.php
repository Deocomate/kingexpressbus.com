<?php

namespace App\Models;

use Database\Factories\RouteStopFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RouteStop extends Model
{
    /** @use HasFactory<RouteStopFactory> */
    use HasFactory;

    protected $fillable = [
        'route_id',
        'stop_id',
        'stop_type',
        'priority',
    ];

    protected $casts = [
        'route_id' => 'integer',
        'stop_id' => 'integer',
        'priority' => 'integer',
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function stop(): BelongsTo
    {
        return $this->belongsTo(Stop::class);
    }

    /**
     * Scope to order by priority (higher values first).
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('priority');
    }
}
