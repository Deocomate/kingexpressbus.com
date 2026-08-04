<?php

namespace App\Support\Admin\OptionSources;

interface OptionSourceContract
{
    /**
     * Search for options matching $query string.
     * Must return at most 50 results as [['id' => ..., 'text' => ...], ...].
     */
    public function search(string $query): array;
}
