<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
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
        'payment_method',
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
        'confirmed_at' => 'datetime',
        'payment_log' => 'array',
        'status' => BookingStatus::class,
        'payment_method' => PaymentMethod::class,
        'payment_status' => PaymentStatus::class,
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
        return $query->whereIn('status', [
            BookingStatus::Pending,
            BookingStatus::Confirmed,
            BookingStatus::Completed,
        ]);
    }

    /**
     * Scope for pending bookings.
     */
    public function scopePending($query)
    {
        return $query->where('status', BookingStatus::Pending);
    }

    /**
     * Scope for confirmed bookings.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', BookingStatus::Confirmed);
    }
}
