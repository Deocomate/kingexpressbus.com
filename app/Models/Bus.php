<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'model_name',
        'seat_count',
        'thumbnail_url',
        'image_list_url',
        'content',
        'priority',
    ];

    protected $casts = [
        'image_list_url' => 'array',
        'seat_count' => 'integer',
        'priority' => 'integer',
    ];

    /**
     * Get all trips for this bus.
     */
    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(BusService::class, 'bus_bus_service');
    }

    /**
     * Scope to order by priority.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('priority', 'desc');
    }
}
