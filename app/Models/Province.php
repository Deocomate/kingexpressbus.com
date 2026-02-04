<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'title',
        'description',
        'thumbnail_url',
        'image_list_url',
        'content',
        'priority',
    ];

    protected $casts = [
        'image_list_url' => 'array',
        'priority' => 'integer',
    ];

    /**
     * Get districts in this province.
     */
    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    /**
     * Get routes starting from this province.
     */
    public function routesFrom(): HasMany
    {
        return $this->hasMany(Route::class, 'province_start_id');
    }

    /**
     * Get routes ending at this province.
     */
    public function routesTo(): HasMany
    {
        return $this->hasMany(Route::class, 'province_end_id');
    }

    /**
     * Scope to order by priority.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('priority', 'desc');
    }
}
