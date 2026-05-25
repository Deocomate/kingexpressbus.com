<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'user_id',
        'trip_id',
        'booking_date',
        'customer_name',
        'customer_email',
        'customer_phone',
        'pickup_stop_id',
        'dropoff_stop_id',
        'quantity',
        'total_price',
        'base_unit_price',
        'global_surcharge_unit',
        'route_surcharge_unit',
        'final_unit_price',
        'total_surcharge_amount',
        'surcharge_reason_snapshot',
        'status',
        'payment_method',
        'payment_status',
        'payment_transaction_id',
        'payment_log',
        'notes',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'quantity' => 'integer',
        'total_price' => 'integer',
        'base_unit_price' => 'integer',
        'global_surcharge_unit' => 'integer',
        'route_surcharge_unit' => 'integer',
        'final_unit_price' => 'integer',
        'total_surcharge_amount' => 'integer',
        'payment_log' => 'array',
    ];

    /**
     * Get the user who made the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the trip for this booking.
     */
    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * Get the pickup stop.
     */
    public function pickupStop(): BelongsTo
    {
        return $this->belongsTo(Stop::class, 'pickup_stop_id');
    }

    /**
     * Get the dropoff stop.
     */
    public function dropoffStop(): BelongsTo
    {
        return $this->belongsTo(Stop::class, 'dropoff_stop_id');
    }

    /**
     * Scope for active bookings (not cancelled).
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed', 'completed']);
    }

    /**
     * Scope for pending bookings.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for confirmed bookings.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }
}
