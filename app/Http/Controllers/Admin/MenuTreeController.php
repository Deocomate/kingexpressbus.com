<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReorderMenuTreeRequest;
use App\Support\Admin\MenuTreeBuilder;
use App\Support\ClientCache;
use App\Support\PriorityOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class MenuTreeController extends Controller
{
    /**
     * Reorder the menu tree atomically.
     *
     * Algorithm:
     *   1. Validate tree (server-side: depth ≤ 4, all ids present, no duplicates, version token)
     *   2. Invert sibling orders with PriorityOrder::invertSiblingOrders()
     *   3. Write all parent_id + priority in one CASE UPDATE inside a DB transaction
     *   4. Flush menu cache exactly once
     */
    public function reorder(ReorderMenuTreeRequest $request): JsonResponse
    {
        // Throws ValidationException (422) or abort(409) on version mismatch
        $flatMap = $request->validateTree();

        if (empty($flatMap)) {
            return response()->json([
                'reload' => false,
                'version' => MenuTreeBuilder::versionToken(),
            ]);
        }

        $inverted = PriorityOrder::invertSiblingOrders($flatMap);
        $now = now()->format('Y-m-d H:i:s');

        DB::transaction(function () use ($inverted, $now) {
            // Build single CASE UPDATE per batch to avoid N queries
            $priorityCases = '';
            $parentIdCases = '';
            $ids = [];

            foreach ($inverted as $id => $data) {
                $id = (int) $id;
                $priority = (int) $data['order'];
                $parentId = (int) $data['parent_id'];
                $priorityCases .= " WHEN {$id} THEN {$priority}";
                $parentIdCases .= " WHEN {$id} THEN {$parentId}";
                $ids[] = $id;
            }

            $idList = implode(',', $ids);

            // Bind timestamp — SQLite has no NOW(); MySQL accepts the literal datetime.
            DB::update(
                "UPDATE menus SET
                    priority  = CASE id {$priorityCases} END,
                    parent_id = CASE id {$parentIdCases} END,
                    updated_at = ?
                 WHERE id IN ({$idList})",
                [$now]
            );
        });

        // Flush exactly once after the transaction commits
        ClientCache::forgetMenus();

        return response()->json([
            'reload' => true,
            'version' => MenuTreeBuilder::versionToken(),
        ]);
    }
}
