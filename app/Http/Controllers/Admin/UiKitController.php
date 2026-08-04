<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Admin\TableColumn;
use App\Support\Admin\TableConfig;
use App\Support\Admin\TableTab;
use App\Support\Admin\TableQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class UiKitController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(app()->environment('local'), 404);

        // Demo table data using a collection (works without a real DB table)
        $demoItems = collect([
            ['id' => 1, 'name' => 'Alice Nguyễn', 'status' => 'active', 'amount' => 1500000, 'created_at' => now()->subDays(5)],
            ['id' => 2, 'name' => 'Bob Trần', 'status' => 'inactive', 'amount' => 2400000, 'created_at' => now()->subDays(3)],
            ['id' => 3, 'name' => 'Carol Lê', 'status' => 'active', 'amount' => 750000, 'created_at' => now()->subDays(1)],
            ['id' => 4, 'name' => 'Dave Phạm', 'status' => 'pending', 'amount' => 3200000, 'created_at' => now()->subHours(2)],
            ['id' => 5, 'name' => 'Eve Đỗ', 'status' => 'active', 'amount' => 950000, 'created_at' => now()->subMinutes(30)],
        ]);

        $activeTab = $request->input('tab', 'all');
        if ($activeTab === 'active') {
            $filtered = $demoItems->where('status', 'active');
        } elseif ($activeTab === 'inactive') {
            $filtered = $demoItems->where('status', 'inactive');
        } else {
            $filtered = $demoItems;
        }

        $tabs = [
            ['key' => 'all',      'label' => 'Tất cả',  'badge' => $demoItems->count()],
            ['key' => 'active',   'label' => 'Hoạt động', 'badge' => $demoItems->where('status', 'active')->count()],
            ['key' => 'inactive', 'label' => 'Không hoạt động', 'badge' => $demoItems->where('status', 'inactive')->count()],
        ];

        $columns = [
            TableColumn::make('id', '#')->sortable()->hideable(false),
            TableColumn::make('name', 'Họ tên')->sortable(),
            TableColumn::make('status', 'Trạng thái')->sortable(),
            TableColumn::make('amount', 'Số tiền')->sortable(),
            TableColumn::make('created_at', 'Ngày tạo')->sortable()->defaultHidden(true),
        ];

        return view('admin.ui-kit', compact('demoItems', 'filtered', 'tabs', 'columns', 'activeTab'));
    }
}
