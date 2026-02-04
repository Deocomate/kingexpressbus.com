<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Stop extends Model
{
    use HasFactory;

    protected $fillable = [
        'district_id',
        'name',
        'address',
        'priority',
    ];

    protected $casts = [
        'priority' => 'integer',
    ];

    /**
     * Get the district.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Get the province through district.
     */
    public function getProvinceAttribute()
    {
        return $this->district?->province;
    }

    /**
     * Get routes that use this stop.
     */
    public function routes(): BelongsToMany
    {
        return $this->belongsToMany(Route::class, 'route_stops')
            ->withPivot(['stop_type', 'priority']);
    }

    /**
     * Scope to order by priority.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('priority', 'desc');
    }
}
