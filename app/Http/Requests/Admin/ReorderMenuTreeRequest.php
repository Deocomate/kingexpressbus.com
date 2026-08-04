<?php

namespace App\Http\Requests\Admin;

use App\Models\Menu;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ReorderMenuTreeRequest extends FormRequest
{
    public const MAX_DEPTH = 4;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tree' => ['required', 'array'],
            'version' => ['required', 'string'],
        ];
    }

    /**
     * Run extra business-rule validation after basic rules pass.
     *
     * @throws ValidationException
     */
    public function validateTree(): array
    {
        $tree = $this->input('tree', []);
        $version = $this->input('version');

        // 1. Version token check (optimistic concurrency)
        $maxUpdatedAt = (string) Menu::max('updated_at');
        if ($maxUpdatedAt !== '' && $version !== $maxUpdatedAt) {
            abort(409, 'Cây menu đã thay đổi. Tải lại trang và thử lại.');
        }

        // 2. Flatten + collect ids, depths, parent mappings
        $allIds = Menu::pluck('id')->sort()->values()->toArray();
        $seenIds = [];
        $flatMap = [];
        $errors = [];

        $this->flattenTree($tree, Menu::ROOT_PARENT_ID, 1, $seenIds, $flatMap, $errors);

        // 3. Missing/extra nodes
        sort($seenIds);
        if ($seenIds !== $allIds) {
            $missing = array_diff($allIds, $seenIds);
            $extra = array_diff($seenIds, $allIds);
            if ($missing) {
                $errors[] = 'Payload thiếu node(s): '.implode(', ', $missing);
            }
            if ($extra) {
                $errors[] = 'Payload có node không tồn tại: '.implode(', ', $extra);
            }
        }

        if ($errors) {
            throw ValidationException::withMessages(['tree' => $errors]);
        }

        return $flatMap;
    }

    private function flattenTree(
        array $nodes,
        int $parentId,
        int $depth,
        array &$seenIds,
        array &$flatMap,
        array &$errors
    ): void {
        if ($depth > self::MAX_DEPTH) {
            $errors[] = 'Cây quá sâu. Tối đa '.self::MAX_DEPTH.' cấp.';

            return;
        }

        foreach ($nodes as $index => $node) {
            $id = isset($node['id']) ? (int) $node['id'] : null;

            if ($id === null || $id <= 0) {
                $errors[] = "Node tại vị trí {$index} có id không hợp lệ.";

                continue;
            }

            if (in_array($id, $seenIds, true)) {
                $errors[] = "Node id={$id} bị trùng.";

                continue;
            }

            // Cannot be its own parent
            if ($id === $parentId) {
                $errors[] = "Node id={$id} không thể là cha của chính nó.";

                continue;
            }

            $seenIds[] = $id;
            $flatMap[$id] = [
                'parent_id' => $parentId,
                'order' => $index + 1,
            ];

            $children = $node['children'] ?? [];
            if (is_array($children) && count($children) > 0) {
                $this->flattenTree($children, $id, $depth + 1, $seenIds, $flatMap, $errors);
            }
        }
    }
}
