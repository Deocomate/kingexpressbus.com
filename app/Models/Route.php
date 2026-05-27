<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'province_start_id',
        'province_end_id',
        'name',
        'slug',
        'title',
        'description',
        'duration',
        'distance_km',
        'price_default',
        'thumbnail_url',
        'image_list_url',
        'content',
        'available_hotel_pickup',
        'priority',
    ];

    protected $casts = [
        'image_list_url' => 'array',
        'distance_km' => 'integer',
        'price_default' => 'integer',
        'available_hotel_pickup' => 'boolean',
        'priority' => 'integer',
    ];

    /**
     * Get the start province.
     */
    public function startProvince(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_start_id');
    }

    /**
     * Get the end province.
     */
    public function endProvince(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_end_id');
    }

    /**
     * Get all trips for this route.
     */
    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function routeStops(): HasMany
    {
        return $this->hasMany(RouteStop::class)->orderBy('priority');
    }

    /**
     * Get all stops for this route.
     */
    public function stops(): BelongsToMany
    {
        return $this->belongsToMany(Stop::class, 'route_stops')
            ->withPivot(['stop_type', 'priority'])
            ->orderBy('route_stops.priority');
    }

    /**
     * Get pickup stops.
     */
    public function pickupStops(): BelongsToMany
    {
        return $this->stops()->wherePivotIn('stop_type', ['pickup', 'both']);
    }

    /**
     * Get dropoff stops.
     */
    public function dropoffStops(): BelongsToMany
    {
        return $this->stops()->wherePivotIn('stop_type', ['dropoff', 'both']);
    }

    /**
     * Scope to order by priority.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('priority', 'desc');
    }
}
