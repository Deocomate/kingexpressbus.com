<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HolidaySurchargeRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'holiday_surcharge_id',
        'route_id',
        'route_surcharge_amount',
    ];

    protected $casts = [
        'holiday_surcharge_id' => 'integer',
        'route_id' => 'integer',
        'route_surcharge_amount' => 'integer',
    ];

    /**
     * Get parent holiday surcharge rule.
     */
    public function holidaySurcharge(): BelongsTo
    {
        return $this->belongsTo(HolidaySurcharge::class);
    }

    /**
     * Get related route.
     */
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }
}
