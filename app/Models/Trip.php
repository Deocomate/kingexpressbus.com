<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_id',
        'route_id',
        'start_time',
        'end_time',
        'price',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'price' => 'integer',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    /**
     * Get the bus for this trip.
     */
    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    /**
     * Get the route for this trip.
     */
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    /**
     * Get all bookings for this trip.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the blocks for this trip.
     */
    public function blocks(): HasMany
    {
        return $this->hasMany(TripBlock::class);
    }

    /**
     * Scope for active trips.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by priority.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('priority', 'desc')->orderBy('start_time');
    }
}
