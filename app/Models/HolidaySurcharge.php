<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HolidaySurcharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'reason',
        'start_date',
        'end_date',
        'global_surcharge_amount',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'global_surcharge_amount' => 'integer',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    /**
     * Get route-level surcharge adjustments for this holiday rule.
     */
    public function routeAdjustments(): HasMany
    {
        return $this->hasMany(HolidaySurchargeRoute::class);
    }

    /**
     * Scope for active surcharge rules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
