<?php

namespace App\Support\Admin;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Collection;

/**
 * Builds a nested tree structure from a flat Menu collection.
 *
 * Input: flat collection ordered by parent_id ASC, priority DESC (treeRecords()).
 * Output: nested array suitable for recursive Blade rendering.
 */
class MenuTreeBuilder
{
    /**
     * Build nested tree from a flat collection.
     *
     * @param  Collection<int, Menu>  $records
     * @return array<int, array{node: Menu, children: array}>
     */
    public static function build(Collection $records): array
    {
        // Index by id for fast parent lookup
        $indexed = $records->keyBy('id');

        // Group by parent_id so we can attach children efficiently
        $childrenMap = $records->groupBy('parent_id');

        // Roots are those with parent_id = ROOT_PARENT_ID
        return self::buildLevel(Menu::ROOT_PARENT_ID, $childrenMap, $indexed);
    }

    /**
     * @param  \Illuminate\Support\Collection  $childrenMap
     * @param  \Illuminate\Support\Collection  $indexed
     * @return array<int, array{node: Menu, children: array}>
     */
    private static function buildLevel(int $parentId, $childrenMap, $indexed): array
    {
        $siblings = $childrenMap->get($parentId, collect());

        // Sort siblings by priority descending (highest first)
        $sorted = $siblings->sortByDesc('priority')->values();

        $result = [];
        foreach ($sorted as $menu) {
            $result[] = [
                'node'     => $menu,
                'children' => self::buildLevel($menu->id, $childrenMap, $indexed),
            ];
        }

        return $result;
    }

    /**
     * Compute current version token = max(updated_at) as a string.
     * Used for optimistic concurrency on reorder.
     */
    public static function versionToken(): string
    {
        return (string) Menu::max('updated_at');
    }
}
