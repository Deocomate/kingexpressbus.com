<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class PriorityOrder
{
    /**
     * Invert ascending sibling order values so the first item gets the highest number.
     *
     * @param  array<int|string, array{parent_id: mixed, order: int}>  $items
     * @return array<int|string, array{parent_id: mixed, order: int}>
     */
    public static function invertSiblingOrders(array $items): array
    {
        $grouped = [];

        foreach ($items as $id => $item) {
            $grouped[$item['parent_id']][] = [
                'id' => $id,
                'order' => $item['order'],
            ];
        }

        foreach ($grouped as $siblings) {
            $count = count($siblings);

            foreach ($siblings as $sibling) {
                $items[$sibling['id']]['order'] = $count - $sibling['order'] + 1;
            }
        }

        return $items;
    }

    /**
     * Invert stored priority values within each group while preserving visual order under DESC sorts.
     *
     * Wrapped in a transaction; handles NULL group values correctly; uses a single CASE
     * update per group instead of one UPDATE per row.
     */
    public static function invertStoredPriorities(string $table, string $groupColumn): void
    {
        DB::transaction(function () use ($table, $groupColumn) {
            $groupIds = DB::table($table)
                ->select($groupColumn)
                ->distinct()
                ->pluck($groupColumn);

            foreach ($groupIds as $groupId) {
                $rows = $groupId === null
                    ? DB::table($table)->whereNull($groupColumn)->get(['id', 'priority'])
                    : DB::table($table)->where($groupColumn, $groupId)->get(['id', 'priority']);

                if ($rows->isEmpty()) {
                    continue;
                }

                $maxPriority = (int) $rows->max('priority');

                // Build a single CASE update to avoid N individual queries.
                $cases = $rows->map(fn ($row) => 'WHEN ' . (int) $row->id . ' THEN ' . ($maxPriority - (int) $row->priority))->implode(' ');

                $whereQuery = $groupId === null
                    ? DB::table($table)->whereNull($groupColumn)
                    : DB::table($table)->where($groupColumn, $groupId);

                $whereQuery->update(['priority' => DB::raw("CASE id {$cases} END")]);
            }
        });
    }
}
