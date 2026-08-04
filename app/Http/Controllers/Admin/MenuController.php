<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMenuRequest;
use App\Http\Requests\Admin\UpdateMenuRequest;
use App\Models\Menu;
use App\Support\ClientCache;
use Illuminate\Http\RedirectResponse;

class MenuController extends Controller
{
    public function store(StoreMenuRequest $request): RedirectResponse
    {
        Menu::create($request->validated());
        ClientCache::forgetMenus();

        return redirect()
            ->route('admin.website.index', ['section' => 'menus'])
            ->with('success', 'Đã thêm menu.');
    }

    public function update(UpdateMenuRequest $request, Menu $menu): RedirectResponse
    {
        $menu->update($request->validated());
        ClientCache::forgetMenus();

        return redirect()
            ->route('admin.website.index', ['section' => 'menus'])
            ->with('success', 'Đã cập nhật menu.');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        // HasTreeOrder boot hook cascades delete to children
        $menu->delete();
        ClientCache::forgetMenus();

        return redirect()
            ->route('admin.website.index', ['section' => 'menus'])
            ->with('success', 'Đã xóa menu.');
    }
}
