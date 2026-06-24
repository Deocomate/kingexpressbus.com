<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ShiftTimestampsToGmt7 extends Command
{
    protected $signature = 'app:shift-timestamps-gmt7
                            {--dry-run : Preview changes without writing}
                            {--confirm : Apply the timestamp shift}';

    protected $description = 'Shift existing UTC timestamps forward by 7 hours for GMT+7 display consistency';

    /**
     * @var array<string, list<string>>
     */
    private array $tables = [
        'bookings' => ['created_at', 'updated_at', 'confirmed_at'],
        'trips' => ['created_at', 'updated_at'],
        'routes' => ['created_at', 'updated_at'],
        'buses' => ['created_at', 'updated_at'],
        'provinces' => ['created_at', 'updated_at'],
        'districts' => ['created_at', 'updated_at'],
        'district_types' => ['created_at', 'updated_at'],
        'stops' => ['created_at', 'updated_at'],
        'menus' => ['created_at', 'updated_at'],
        'holiday_surcharges' => ['created_at', 'updated_at'],
        'trip_blocks' => ['created_at', 'updated_at'],
        'web_profiles' => ['created_at', 'updated_at'],
        'bus_services' => ['created_at', 'updated_at'],
        'users' => ['created_at', 'updated_at'],
    ];

    public function handle(): int
    {
        if (! $this->option('dry-run') && ! $this->option('confirm')) {
            $this->error('Use --dry-run to preview or --confirm to apply changes.');

            return self::FAILURE;
        }

        $this->info('MySQL session timezone: '.DB::selectOne('SELECT @@session.time_zone AS tz')->tz);
        $this->newLine();

        $totalRows = 0;

        foreach ($this->tables as $table => $columns) {
            if (! Schema::hasTable($table)) {
                $this->warn("Skipping missing table: {$table}");

                continue;
            }

            $existingColumns = array_filter(
                $columns,
                fn (string $column): bool => Schema::hasColumn($table, $column),
            );

            if ($existingColumns === []) {
                continue;
            }

            $count = (int) DB::table($table)
                ->whereNotNull($existingColumns[0])
                ->count();

            if ($count === 0) {
                continue;
            }

            $this->line("<fg=cyan>{$table}</> — {$count} row(s)");

            $sample = DB::table($table)
                ->whereNotNull($existingColumns[0])
                ->orderByDesc('id')
                ->limit(1)
                ->first();

            if ($sample) {
                foreach ($existingColumns as $column) {
                    $this->line("  {$column}: {$sample->{$column}}");
                }
            }

            if ($this->option('dry-run')) {
                $totalRows += $count;

                continue;
            }

            $setClauses = collect($existingColumns)
                ->map(fn (string $column): string => "`{$column}` = DATE_ADD(`{$column}`, INTERVAL 7 HOUR)")
                ->implode(', ');

            DB::statement("UPDATE `{$table}` SET {$setClauses} WHERE `{$existingColumns[0]}` IS NOT NULL");
            $totalRows += $count;
        }

        $this->newLine();

        if ($this->option('dry-run')) {
            $this->info("Dry run complete. {$totalRows} row(s) would be shifted +7 hours.");
            $this->comment('Run with --confirm to apply.');

            return self::SUCCESS;
        }

        $this->info("Shifted timestamps for {$totalRows} row(s) by +7 hours.");

        return self::SUCCESS;
    }
}
