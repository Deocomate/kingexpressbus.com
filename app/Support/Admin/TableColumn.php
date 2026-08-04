<?php

namespace App\Support\Admin;

class TableColumn
{
    public function __construct(
        public readonly string $key,
        public readonly string $label,
        public readonly bool $sortable = false,
        public readonly bool $hideable = true,
        public readonly bool $defaultHidden = false,
    ) {}

    public static function make(string $key, string $label): self
    {
        return new self($key, $label);
    }

    public function sortable(bool $value = true): self
    {
        return new self($this->key, $this->label, $value, $this->hideable, $this->defaultHidden);
    }

    public function hideable(bool $value = true): self
    {
        return new self($this->key, $this->label, $this->sortable, $value, $this->defaultHidden);
    }

    public function defaultHidden(bool $value = true): self
    {
        return new self($this->key, $this->label, $this->sortable, $this->hideable, $value);
    }
}
