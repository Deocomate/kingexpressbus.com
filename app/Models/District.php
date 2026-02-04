<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    use HasFactory;

    protected $fillable = [
        'province_id',
        'district_type_id',
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
     * Get the province.
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * Get the district type.
     */
    public function districtType(): BelongsTo
    {
        return $this->belongsTo(DistrictType::class);
    }

    /**
     * Get stops in this district.
     */
    public function stops(): HasMany
    {
        return $this->hasMany(Stop::class);
    }

    /**
     * Scope to order by priority.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('priority', 'desc');
    }
}
