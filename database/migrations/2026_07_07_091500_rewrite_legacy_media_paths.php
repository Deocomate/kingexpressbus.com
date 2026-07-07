<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @var array<string, list<string>>
     */
    private array $stringColumns = [
        'routes' => ['thumbnail_url'],
        'buses' => ['thumbnail_url'],
        'provinces' => ['thumbnail_url'],
        'districts' => ['thumbnail_url'],
        'web_profiles' => ['logo_url', 'favicon_url', 'share_image_url'],
    ];

    /**
     * @var array<string, list<string>>
     */
    private array $jsonColumns = [
        'routes' => ['image_list_url'],
        'buses' => ['image_list_url'],
        'provinces' => ['image_list_url'],
        'districts' => ['image_list_url'],
    ];

    public function up(): void
    {
        foreach ($this->stringColumns as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column) {
                if (! Schema::hasColumn($table, $column)) {
                    continue;
                }

                $this->rewriteStringColumn($table, $column);
            }
        }

        foreach ($this->jsonColumns as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column) {
                if (! Schema::hasColumn($table, $column)) {
                    continue;
                }

                $this->rewriteJsonColumn($table, $column);
            }
        }
    }

    private function rewriteStringColumn(string $table, string $column): void
    {
        DB::table($table)
            ->whereNotNull($column)
            ->orderBy('id')
            ->chunkById(100, function ($rows) use ($table, $column): void {
                foreach ($rows as $row) {
                    $rewritten = $this->rewritePath($row->{$column});

                    if ($rewritten !== $row->{$column}) {
                        DB::table($table)->where('id', $row->id)->update([$column => $rewritten]);
                    }
                }
            });
    }

    private function rewriteJsonColumn(string $table, string $column): void
    {
        DB::table($table)
            ->whereNotNull($column)
            ->orderBy('id')
            ->chunkById(100, function ($rows) use ($table, $column): void {
                foreach ($rows as $row) {
                    $decoded = json_decode((string) $row->{$column}, true);

                    if (! is_array($decoded)) {
                        continue;
                    }

                    $rewritten = array_map(fn ($value) => is_string($value) ? $this->rewritePath($value) : $value, $decoded);
                    $encoded = json_encode($rewritten, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    if ($encoded !== (string) $row->{$column}) {
                        DB::table($table)->where('id', $row->id)->update([$column => $encoded]);
                    }
                }
            });
    }

    private function rewritePath(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return $path;
        }

        $path = str_replace('web information', 'web-information', $path);

        $clientPrefix = '/'.config('assets.client_static_prefix').'/';
        $legacyClientPrefix = '/'.config('assets.legacy_client_prefix').'/';
        $uploadsDirectory = config('assets.uploads_directory');

        if (str_starts_with($path, $legacyClientPrefix)) {
            return $clientPrefix.substr($path, strlen($legacyClientPrefix));
        }

        foreach (['/userfiles/', 'userfiles/'] as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return $uploadsDirectory.'/'.ltrim(substr($path, strlen($prefix)), '/');
            }
        }

        return $path;
    }

    public function down(): void
    {
        // One-way data migration; restore from backup if rollback is required.
    }
};
