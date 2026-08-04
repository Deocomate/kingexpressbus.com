<?php

namespace App\Support\Admin;

class TableConfig
{
    /** @var TableColumn[] */
    private array $columns = [];

    /** @var TableTab[] */
    private array $tabs = [];

    /** Allowed sort column keys (with optional Closure for raw expression). */
    private array $sortWhitelist = [];

    /** Allowed per_page values. */
    private array $perPageOptions = [15, 30, 50, 100];

    /** Default per_page. */
    private int $defaultPerPage = 15;

    /** Searchable column names (DB columns or raw expressions). */
    private array $searchColumns = [];

    public static function make(): self
    {
        return new self();
    }

    /** @param TableColumn[] $columns */
    public function columns(array $columns): self
    {
        $this->columns = $columns;
        return $this;
    }

    /** @param TableTab[] $tabs */
    public function tabs(array $tabs): self
    {
        $this->tabs = $tabs;
        return $this;
    }

    /**
     * Whitelist a sort key with optional custom Closure.
     * @param string|\Closure $column DB column name or Closure(Builder, string $direction): Builder
     */
    public function allowSort(string $key, string|\Closure $column = null): self
    {
        $this->sortWhitelist[$key] = $column ?? $key;
        return $this;
    }

    public function searchColumns(array $columns): self
    {
        $this->searchColumns = $columns;
        return $this;
    }

    public function perPageOptions(array $options, int $default = 15): self
    {
        $this->perPageOptions = $options;
        $this->defaultPerPage = $default;
        return $this;
    }

    /** @return TableColumn[] */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /** @return TableTab[] */
    public function getTabs(): array
    {
        return $this->tabs;
    }

    public function getSortWhitelist(): array
    {
        return $this->sortWhitelist;
    }

    public function getSearchColumns(): array
    {
        return $this->searchColumns;
    }

    public function getPerPageOptions(): array
    {
        return $this->perPageOptions;
    }

    public function getDefaultPerPage(): int
    {
        return $this->defaultPerPage;
    }
}
