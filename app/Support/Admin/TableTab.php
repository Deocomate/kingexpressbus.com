<?php

namespace App\Support\Admin;

use Closure;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class TableTab
{
    /** @var Closure(Builder): Builder */
    private ?Closure $queryModifier = null;

    /** @var Closure(): int|null */
    private ?Closure $badgeCallback = null;

    public function __construct(
        public readonly string $key,
        public readonly string $label,
    ) {}

    public static function make(string $key, string $label): self
    {
        return new self($key, $label);
    }

    /** Modify the base query for this tab. */
    public function query(Closure $callback): self
    {
        $clone = clone $this;
        $clone->queryModifier = $callback;
        return $clone;
    }

    /** Badge count closure — evaluated lazily per request. */
    public function badge(Closure $callback): self
    {
        $clone = clone $this;
        $clone->badgeCallback = $callback;
        return $clone;
    }

    public function applyTo(EloquentBuilder|QueryBuilder $query): EloquentBuilder|QueryBuilder
    {
        if ($this->queryModifier) {
            return ($this->queryModifier)($query);
        }
        return $query;
    }

    public function getBadgeCount(): ?int
    {
        if ($this->badgeCallback) {
            return (int) ($this->badgeCallback)();
        }
        return null;
    }
}
