<?php

namespace App\Models;

use App\Models\Concerns\HasTreeOrder;
use Database\Factories\MenuFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    /** @use HasFactory<MenuFactory> */
    use HasFactory, HasTreeOrder;

    public const ROOT_PARENT_ID = -1;

    protected $fillable = [
        'name',
        'url',
        'parent_id',
        'priority',
        'type',
        'related_id',
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'priority' => 'integer',
        'related_id' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->with('children');
    }

    public function determineOrderColumnName(): string
    {
        return 'priority';
    }

    public function determineParentColumnName(): string
    {
        return 'parent_id';
    }

    public function determineTitleColumnName(): string
    {
        return 'name';
    }

    public static function defaultParentKey(): int
    {
        return self::ROOT_PARENT_ID;
    }

    /**
     * Higher priority values should appear first across admin and client.
     */
    public function scopeOrdered(Builder $query, string $direction = 'desc'): Builder
    {
        return $query
            ->orderBy($this->determineParentColumnName())
            ->orderBy($this->determineOrderColumnName(), $direction);
    }
}
