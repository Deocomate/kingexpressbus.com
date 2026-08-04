<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Standalone port of SolutionForest\FilamentTree\Concern\ModelTree boot hooks and public surface.
 *
 * Does not depend on SolutionForest Utils or SupportTranslation so it survives Filament removal.
 * Models that use this trait should override determine*() and defaultParentKey() as needed.
 * Filament TreePage compatibility is preserved via children(), scopeOrdered(), isRoot(),
 * scopeIsRoot(), determineParentColumnName(), determineOrderColumnName(), determineTitleColumnName(),
 * and defaultParentKey().
 */
trait HasTreeOrder
{
    /**
     * Boot hook: normalise parent key and auto-assign order on save; cascade delete children.
     */
    public static function bootHasTreeOrder(): void
    {
        static::saving(function (Model $model) {
            $parentCol = $model->determineParentColumnName();
            $orderCol  = $model->determineOrderColumnName();

            // Normalise missing/sentinel parent value to the model's canonical root key.
            if (empty($model->{$parentCol}) || $model->{$parentCol} === -1) {
                $model->{$parentCol} = static::defaultParentKey();
            }

            // Auto-assign next order number when none given or when it is zero.
            if (empty($model->{$orderCol}) || $model->{$orderCol} === 0) {
                $model->setHighestOrderNumber();
            }
        });

        static::deleting(function (Model $model) {
            // Cascade-delete children recursively via Eloquent so each child's deleting hook fires.
            static::buildSortQuery()
                ->where($model->determineParentColumnName(), $model->getKey())
                ->get()
                ->each(fn ($child) => $child->delete());
        });
    }

    // -------------------------------------------------------------------------
    // Order helpers
    // -------------------------------------------------------------------------

    public function setHighestOrderNumber(): void
    {
        $this->{$this->determineOrderColumnName()} = $this->getHighestOrderNumber() + 1;
    }

    public function getHighestOrderNumber(): int
    {
        return (int) $this->buildSortQueryForParent()->max($this->determineOrderColumnName());
    }

    // -------------------------------------------------------------------------
    // Root checks
    // -------------------------------------------------------------------------

    public function isRoot(): bool
    {
        return $this->getAttributeValue($this->determineParentColumnName()) === static::defaultParentKey();
    }

    public function scopeIsRoot(Builder $query): Builder
    {
        return $query->where($this->determineParentColumnName(), static::defaultParentKey());
    }

    // -------------------------------------------------------------------------
    // Scopes & query helpers
    // -------------------------------------------------------------------------

    public function scopeOrdered(Builder $query, string $direction = 'asc'): Builder
    {
        return $query
            ->orderBy($this->determineParentColumnName(), 'asc')
            ->orderBy($this->determineOrderColumnName(), $direction);
    }

    public static function buildSortQuery(): Builder
    {
        return static::query()->ordered();
    }

    public static function allNodes(): \Illuminate\Database\Eloquent\Collection
    {
        return static::buildSortQuery()->get();
    }

    /**
     * Flat ordered collection of all records — useful for seeding and tests.
     */
    public static function treeRecords(): \Illuminate\Database\Eloquent\Collection
    {
        return static::allNodes();
    }

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function children(): HasMany
    {
        return $this->hasMany(static::class, $this->determineParentColumnName())
            ->with('children')
            ->orderBy($this->determineOrderColumnName());
    }

    // -------------------------------------------------------------------------
    // Column name resolution — override per model as needed
    // -------------------------------------------------------------------------

    public function determineOrderColumnName(): string
    {
        return 'order';
    }

    public function determineParentColumnName(): string
    {
        return 'parent_id';
    }

    public function determineTitleColumnName(): string
    {
        return 'title';
    }

    public static function defaultParentKey(): int
    {
        return -1;
    }

    // -------------------------------------------------------------------------
    // Internal helpers
    // -------------------------------------------------------------------------

    /**
     * Scope restricted to siblings of the current instance (same parent).
     * Handles NULL parent values correctly.
     */
    private function buildSortQueryForParent(): Builder
    {
        $parentVal = $this->{$this->determineParentColumnName()};

        return $parentVal === null
            ? static::buildSortQuery()->whereNull($this->determineParentColumnName())
            : static::buildSortQuery()->where($this->determineParentColumnName(), $parentVal);
    }
}
