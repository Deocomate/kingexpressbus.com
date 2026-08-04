@php
use App\Models\Menu;
use App\Models\Route as RouteModel;
use App\Support\Admin\MenuTreeBuilder;

$flatRecords  = Menu::treeRecords();
$tree         = MenuTreeBuilder::build($flatRecords);
$versionToken = MenuTreeBuilder::versionToken();
$reorderUrl   = route('admin.website.menus.reorder');
$parentOptions = MenuTreeBuilder::parentOptions();
$routeOptions = RouteModel::orderBy('name')->pluck('name', 'id')->toArray();
$updateUrlTpl = url('/quan-tri/cau-hinh-website/menus');
@endphp

<div
    class="space-y-4"
    data-menu-tree
    data-reorder-url="{{ $reorderUrl }}"
    data-version="{{ $versionToken }}"
>
    {{-- Toolbar --}}
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">
            Kéo-thả để sắp xếp. Tối đa 4 cấp.
            <strong>{{ $flatRecords->count() }}</strong> menu.
        </p>
        <button
            type="button"
            @click="$dispatch('open-slide-over', { id: 'menu-form-slide-over' })"
            class="inline-flex items-center gap-1.5 rounded bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700 transition-colors"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Thêm menu
        </button>
    </div>

    <div data-tree-status class="hidden rounded px-4 py-2 text-sm" role="alert"></div>

    <x-admin::form.section>
        @if($flatRecords->isEmpty())
        <p class="text-sm text-gray-500 text-center py-6">Chưa có menu nào.</p>
        @else
        <ul id="menu-root" class="space-y-1">
            @include('admin.website._menu-node', ['nodes' => $tree])
        </ul>
        @endif
    </x-admin::form.section>
</div>

{{-- Menu create/edit slide-over --}}
<x-admin::display.slide-over id="menu-form-slide-over" title="Menu" width="max-w-lg">
    @include('admin.website._menu-form', [
        'parentOptions' => $parentOptions,
        'routeOptions'  => $routeOptions,
        'updateUrlTpl'  => $updateUrlTpl,
    ])
</x-admin::display.slide-over>
