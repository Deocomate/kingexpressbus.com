<?php

namespace App\Http\Controllers\KingExpressBus\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    // Helper function to build the menu tree recursively
    private function buildMenuTree(array $elements, $parentId = null): array
    {
        $branch = [];
        foreach ($elements as $element) {
            // Use type-safe comparison (convert parent_id to integer if necessary)
            $currentParentId = is_null($element->parent_id) ? null : (int)$element->parent_id;
            $targetParentId = is_null($parentId) ? null : (int)$parentId;

            if ($currentParentId === $targetParentId) {
                $children = $this->buildMenuTree($elements, $element->id);
                if ($children) {
                    $element->children = $children;
                } else {
                    $element->children = []; // Ensure children property always exists
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all menus ordered by priority, then name
        $allMenus = DB::table('menus')
            ->orderBy('priority', 'asc')
            ->orderBy('name', 'asc')
            ->get()
            ->toArray(); // Convert to array for easier manipulation in buildMenuTree

        // Build the hierarchical tree structure
        $menuTree = $this->buildMenuTree($allMenus);

        // Pass the tree to the view
        // The view (admin.modules.menus.index) will need logic
        // (e.g., a recursive Blade component or JS library) to render the tree.
        return view('kingexpressbus.admin.modules.menus.index', compact('menuTree'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Fetch potential parent menus (all menus)
        $parentMenus = DB::table('menus')
            ->orderBy('priority', 'asc')
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'parent_id']); // Fetch necessary fields

        // Build a flat list suitable for a dropdown, maybe indicating hierarchy
        // Or structure it for a tree-like select if needed in the view
        $formattedParentMenus = $this->formatMenusForSelect($parentMenus->toArray());


        $menu = null; // No menu data when creating
        return view('kingexpressbus.admin.modules.menus.createOrEdit', compact('menu', 'formattedParentMenus'));
    }

    // Helper function to format menus for select dropdown (optional: adds indentation)
    private function formatMenusForSelect(array $menus, $parentId = null, $prefix = ''): array
    {
        $result = [];
        foreach ($menus as $menu) {
            $currentParentId = is_null($menu->parent_id) ? null : (int)$menu->parent_id;
            $targetParentId = is_null($parentId) ? null : (int)$parentId;

            if ($currentParentId === $targetParentId) {
                // Create a simple object or array for the dropdown
                $menuOption = new \stdClass(); // Or use an array
                $menuOption->id = $menu->id;
                $menuOption->name = $prefix . $menu->name;
                $result[] = $menuOption;

                // Recursively add children with indentation
                $children = $this->formatMenusForSelect($menus, $menu->id, $prefix . '-- ');
                $result = array_merge($result, $children);
            }
        }
        return $result;
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'nullable|string|url|max:255',
            'priority' => 'required|integer',
            // Ensure parent_id exists in the menus table if it's not null
            'parent_id' => 'nullable|integer',
        ]);

        // Set timestamps
        $validated['created_at'] = now();
        $validated['updated_at'] = now();

        // Handle empty parent_id (convert empty string or 0 to null)
        $validated['parent_id'] = $validated['parent_id'] != -1 ? $validated['parent_id'] : null;


        DB::table('menus')->insert($validated);

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu item created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Typically not needed for admin CRUD
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $menu = DB::table('menus')->find($id);
        if (!$menu) {
            abort(404);
        }

        // Fetch potential parent menus, excluding the current menu and its descendants
        $allMenus = DB::table('menus')
            ->orderBy('priority', 'asc')
            ->orderBy('name', 'asc')
            ->get()
            ->toArray();

        $excludeIds = $this->getDescendantIds($allMenus, $id);
        $excludeIds[] = (int)$id; // Exclude self

        $potentialParentMenus = array_filter($allMenus, function ($item) use ($excludeIds) {
            return !in_array($item->id, $excludeIds);
        });

        $formattedParentMenus = $this->formatMenusForSelect($potentialParentMenus);


        return view('kingexpressbus.admin.modules.menus.createOrEdit', compact('menu', 'formattedParentMenus'));
    }

    // Helper function to get IDs of all descendants of a menu item
    private function getDescendantIds(array $elements, $parentId): array
    {
        $ids = [];
        foreach ($elements as $element) {
            if ((int)$element->parent_id === (int)$parentId) {
                $ids[] = (int)$element->id;
                $ids = array_merge($ids, $this->getDescendantIds($elements, $element->id));
            }
        }
        return $ids;
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $menu = DB::table('menus')->find($id);
        if (!$menu) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'nullable|string|max:255',
            'priority' => 'required|integer',
            // Ensure parent_id exists if not null, and prevent setting parent to self or descendant
            'parent_id' => [
                'nullable',
                'integer',
                'exists:menus,id',
                Rule::notIn([$id]), // Cannot be self
                function ($attribute, $value, $fail) use ($id) {
                    if ($value) {
                        $allMenus = DB::table('menus')->get()->toArray();
                        $descendantIds = $this->getDescendantIds($allMenus, $id);
                        if (in_array((int)$value, $descendantIds)) {
                            $fail('Cannot set parent to a descendant item.');
                        }
                    }
                },
            ],
        ]);

        // Set timestamp
        $validated['updated_at'] = now();

        // Handle empty parent_id
        $validated['parent_id'] = !empty($validated['parent_id']) ? $validated['parent_id'] : null;


        DB::table('menus')->where('id', $id)->update($validated);

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu item updated successfully!');
    }

    public function updateOrder(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $request->validate([
            'menuData' => 'required|string',
        ]);

        try {
            // Parse JSON string thành mảng
            $menuData = json_decode($request->menuData, true);

            if (!is_array($menuData)) {
                return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
            }

            // Cập nhật thứ tự và quan hệ cha-con
            $this->updateMenuItems($menuData);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Đệ quy cập nhật thứ tự menu và quan hệ cha con
     */
    private function updateMenuItems($items, $parentId = null, $priority = 0)
    {
        foreach ($items as $item) {
            // Cập nhật mục menu
            DB::table('menus')->where('id', $item['id'])->update([
                'parent_id' => $parentId,
                'priority' => $priority,
                'updated_at' => now()
            ]);

            // Nếu có menu con, cập nhật đệ quy
            if (isset($item['children']) && is_array($item['children']) && count($item['children']) > 0) {
                $this->updateMenuItems($item['children'], $item['id'], 0);
            }

            $priority++;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $menu = DB::table('menus')->find($id);
        if (!$menu) {
            return redirect()->route('admin.menus.index')
                ->with('error', 'Menu item not found.');
        }

        // Due to 'on delete cascade' constraint, deleting a parent will delete children.
        // You might want to add checks here if needed (e.g., prevent deleting top-level items).
        DB::table('menus')->where('id', $id)->delete();

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu item deleted successfully!');
    }
}
