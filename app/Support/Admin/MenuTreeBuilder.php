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
                'node' => $menu,
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

    /**
     * Depth of a menu node (1 = root under ROOT_PARENT_ID).
     */
    public static function depthOf(int $menuId, ?Collection $indexed = null): int
    {
        $indexed ??= Menu::treeRecords()->keyBy('id');
        $depth = 0;
        $currentId = $menuId;
        $guard = 0;

        while ($currentId && $currentId !== Menu::ROOT_PARENT_ID) {
            $depth++;
            $node = $indexed->get($currentId);
            if (! $node) {
                break;
            }
            $currentId = (int) $node->parent_id;
            if (++$guard > 50) {
                break;
            }
        }

        return $depth;
    }

    /**
     * Height of subtree rooted at $menuId (1 = leaf).
     */
    public static function subtreeHeight(int $menuId, ?Collection $childrenMap = null): int
    {
        $childrenMap ??= Menu::treeRecords()->groupBy('parent_id');
        $children = $childrenMap->get($menuId, collect());

        if ($children->isEmpty()) {
            return 1;
        }

        $max = 0;
        foreach ($children as $child) {
            $max = max($max, self::subtreeHeight((int) $child->id, $childrenMap));
        }

        return 1 + $max;
    }

    /**
     * True if $ancestorId is an ancestor of $nodeId (or equal).
     */
    public static function isSelfOrAncestor(int $ancestorId, int $nodeId, ?Collection $indexed = null): bool
    {
        if ($ancestorId === $nodeId) {
            return true;
        }

        $indexed ??= Menu::treeRecords()->keyBy('id');
        $currentId = $nodeId;
        $guard = 0;

        while ($currentId && $currentId !== Menu::ROOT_PARENT_ID) {
            if ($currentId === $ancestorId) {
                return true;
            }
            $node = $indexed->get($currentId);
            if (! $node) {
                return false;
            }
            $currentId = (int) $node->parent_id;
            if (++$guard > 50) {
                return false;
            }
        }

        return false;
    }

    /**
     * Validate a prospective parent_id for create/update.
     * Returns null if OK, or a Vietnamese error message.
     *
     * @param  Menu|null  $moving  Menu being updated (null on create)
     */
    public static function parentValidationError(int $parentId, ?Menu $moving = null): ?string
    {
        $maxDepth = 4;

        if ($parentId === Menu::ROOT_PARENT_ID) {
            if ($moving === null) {
                return null;
            }
            // Moving to root: subtree height must fit under max depth
            if (self::subtreeHeight($moving->id) > $maxDepth) {
                return "Cây menu quá sâu. Tối đa {$maxDepth} cấp.";
            }

            return null;
        }

        $records = Menu::treeRecords();
        $indexed = $records->keyBy('id');
        $parent = $indexed->get($parentId);

        if (! $parent) {
            return 'Menu cha không tồn tại.';
        }

        if ($moving !== null) {
            if ($parentId === (int) $moving->id) {
                return 'Menu không thể làm cha của chính nó.';
            }
            // Reject placing under own descendant
            if (self::isSelfOrAncestor($moving->id, $parentId, $indexed)) {
                return 'Không thể chọn menu con làm menu cha.';
            }
        }

        $parentDepth = self::depthOf($parentId, $indexed);
        $subtree = $moving ? self::subtreeHeight($moving->id, $records->groupBy('parent_id')) : 1;

        if ($parentDepth + $subtree > $maxDepth) {
            return "Cây menu quá sâu. Tối đa {$maxDepth} cấp.";
        }

        return null;
    }

    /**
     * Parent options for the form: only nodes that can still accept a child (depth < 4).
     *
     * @return array<int, string>
     */
    public static function parentOptions(?int $excludeMenuId = null): array
    {
        $records = Menu::treeRecords();
        $indexed = $records->keyBy('id');
        $options = [];

        foreach ($records as $menu) {
            if ($excludeMenuId !== null && self::isSelfOrAncestor($excludeMenuId, (int) $menu->id, $indexed)) {
                continue;
            }
            $depth = self::depthOf((int) $menu->id, $indexed);
            if ($depth >= 4) {
                continue;
            }
            $prefix = $depth > 1 ? str_repeat('— ', $depth - 1) : '';
            $options[$menu->id] = $prefix.$menu->name;
        }

        return $options;
    }
}
