@extends('admin.layouts.app')
@section('title', 'UI Kit Demo')

@section('content')
<div class="space-y-10">

    {{-- Header --}}
    <div>
        <h1 class="text-xl font-bold text-gray-900">UI Kit — Demo Components</h1>
        <p class="text-sm text-gray-500 mt-1">Chỉ hiển thị ở môi trường <code>local</code>. Cổng nghiệm thu Phase 3.</p>
    </div>

    {{-- ================================================================== --}}
    {{-- FORM COMPONENTS --}}
    {{-- ================================================================== --}}

    <x-admin::form.section title="Form Components" description="All form input variants">

        <div class="grid grid-cols-2 gap-6">
            {{-- Input --}}
            <x-admin::form.input name="demo_text" label="Text input" placeholder="Nhập nội dung…" value="Hello" />
            <x-admin::form.input name="demo_email" label="Email (required)" type="email" required placeholder="email@example.com" />

            {{-- Textarea --}}
            <div class="col-span-2">
                <x-admin::form.textarea name="demo_textarea" label="Textarea" placeholder="Mô tả…" value="Nội dung demo" :rows="3" />
            </div>

            {{-- Select --}}
            <x-admin::form.select
                name="demo_select"
                label="Select"
                :options="['option1' => 'Lựa chọn 1', 'option2' => 'Lựa chọn 2', 'option3' => 'Lựa chọn 3']"
                value="option2"
            />

            {{-- Select search static --}}
            <x-admin::form.select-search
                name="demo_select_search"
                label="Select Search (static)"
                :options="['vn' => 'Việt Nam', 'us' => 'Hoa Kỳ', 'jp' => 'Nhật Bản', 'kr' => 'Hàn Quốc']"
                value="vn"
            />

            {{-- Toggle --}}
            <x-admin::form.toggle name="demo_toggle" label="Toggle switch" :value="true" hint="Bật/tắt tính năng" />
            <x-admin::form.toggle name="demo_toggle_off" label="Toggle (off)" :value="false" />

            {{-- Radio group --}}
            <x-admin::form.radio-group
                name="demo_radio"
                label="Radio group"
                :options="['a' => 'Tùy chọn A', 'b' => 'Tùy chọn B', 'c' => 'Tùy chọn C']"
                value="b"
                :inline="true"
            />

            {{-- Money --}}
            <x-admin::form.money name="demo_money" label="Số tiền (VND)" value="1500000" />

            {{-- Date --}}
            <x-admin::form.date name="demo_date" label="Ngày" value="{{ now()->format('Y-m-d') }}" />
            <x-admin::form.date name="demo_datetime" label="Ngày giờ" mode="datetime" value="{{ now()->format('Y-m-d H:i') }}" />

            {{-- Date range --}}
            <div class="col-span-2">
                <x-admin::form.date-range
                    name-from="demo_from"
                    name-to="demo_to"
                    label="Khoảng ngày"
                    value-from="{{ now()->subDays(7)->format('Y-m-d') }}"
                    value-to="{{ now()->format('Y-m-d') }}"
                />
            </div>

            {{-- Range slider --}}
            <div class="col-span-2">
                <x-admin::form.range name="demo_range" label="Khoảng giá" :min="0" :max="1000000" :step="50000" :value="[200000, 800000]" />
            </div>

            {{-- Upload --}}
            <div class="col-span-2">
                <x-admin::form.upload name="demo_upload" label="Upload ảnh" hint="JPG, PNG, WEBP tối đa 8MB" />
            </div>

            {{-- Rich text --}}
            <div class="col-span-2">
                <x-admin::form.rich-text
                    name="demo_rich_text"
                    label="Rich Text Editor"
                    value="<h2>Tiêu đề H2</h2><p>Nội dung <strong>đậm</strong> và <em>nghiêng</em>.</p>"
                    height="240px"
                />
            </div>
        </div>

    </x-admin::form.section>

    {{-- ================================================================== --}}
    {{-- DISPLAY COMPONENTS --}}
    {{-- ================================================================== --}}

    <x-admin::form.section title="Display Components">

        {{-- Badges --}}
        <div class="space-y-3">
            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Badges</h4>
            <div class="flex flex-wrap gap-2">
                <x-admin::display.badge color="success">Thành công</x-admin::display.badge>
                <x-admin::display.badge color="danger">Lỗi</x-admin::display.badge>
                <x-admin::display.badge color="warning">Cảnh báo</x-admin::display.badge>
                <x-admin::display.badge color="info">Thông tin</x-admin::display.badge>
                <x-admin::display.badge color="primary">Chính</x-admin::display.badge>
                <x-admin::display.badge color="secondary">Phụ</x-admin::display.badge>
            </div>
        </div>

        {{-- Stat cards --}}
        <div class="space-y-3">
            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Stat Cards</h4>
            <div class="grid grid-cols-3 gap-4">
                <x-admin::display.stat-card title="Tổng đơn hàng" value="1,234" icon="📦" change="+12% so với tháng trước" change-type="up" />
                <x-admin::display.stat-card title="Doanh thu" value="₫45.2M" color="success" icon="💰" change="-3%" change-type="down" />
                <x-admin::display.stat-card title="Khách hàng mới" value="89" color="info" icon="👤" />
            </div>
        </div>

        {{-- Detail list --}}
        <div class="space-y-3">
            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Detail List</h4>
            <x-admin::display.detail-list :items="[
                'Họ tên' => 'Nguyễn Văn A',
                'Email' => 'a@example.com',
                'Trạng thái' => new \Illuminate\Support\HtmlString('<span class=\'inline-flex items-center rounded-full bg-green-100 text-green-800 text-xs px-2 py-1 font-medium\'>Hoạt động</span>'),
                'Ngày tạo' => now()->format('d/m/Y'),
            ]" />
        </div>

        {{-- Chart --}}
        <div class="space-y-3">
            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Chart</h4>
            <x-admin::display.chart
                type="bar"
                height="200px"
                :data="[
                    'labels' => ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
                    'datasets' => [[
                        'label' => 'Đơn hàng',
                        'data' => [12, 19, 8, 25, 17, 31, 22],
                        'backgroundColor' => 'rgba(255, 201, 0, 0.6)',
                        'borderColor' => '#FFC900',
                        'borderWidth' => 1,
                    ]]
                ]"
            />
        </div>

    </x-admin::form.section>

    {{-- ================================================================== --}}
    {{-- TABLE COMPONENTS --}}
    {{-- ================================================================== --}}

    <x-admin::form.section title="Table Components">

        {{-- Section tabs (entity switching) --}}
        <div>
            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Section Tabs (?section=)</h4>
            <x-admin::nav.section-tabs
                :tabs="['overview' => 'Tổng quan', 'settings' => 'Cài đặt', 'logs' => 'Nhật ký']"
                active-section="{{ request('section', 'overview') }}"
            />
        </div>

        {{-- Query filter tabs (tab=) --}}
        <div>
            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Table Tabs (?tab=)</h4>
            <x-admin::table.tabs
                :tabs="$tabs"
                :active-tab="$activeTab"
                :badges="collect($tabs)->pluck('badge', 'key')->all()"
            />
        </div>

        {{-- Actual table --}}
        <x-admin::table.table id="demo-table">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-8">
                            <input type="checkbox" class="rounded border-gray-300">
                        </th>
                        @foreach($columns as $col)
                        @if(!$col->defaultHidden)
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide" data-col="{{ $col->key }}">
                            {{ $col->label }}
                        </th>
                        @endif
                        @endforeach
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" data-sortable data-reorder-url="#">
                    @foreach($filtered as $row)
                    <tr class="hover:bg-gray-50" data-sortable-id="{{ $row['id'] }}">
                        <td class="px-4 py-3">
                            <input type="checkbox" value="{{ $row['id'] }}" class="rounded border-gray-300">
                        </td>
                        <td class="px-4 py-3 text-gray-900 font-medium">{{ $row['id'] }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $row['name'] }}</td>
                        <td class="px-4 py-3">
                            @php
                                $statusColor = match($row['status']) {
                                    'active' => 'success',
                                    'inactive' => 'secondary',
                                    default => 'warning',
                                };
                            @endphp
                            <x-admin::display.badge :color="$statusColor">{{ $row['status'] }}</x-admin::display.badge>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ number_format($row['amount']) }} đ</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <x-admin::table.toggle-cell url="#" :value="$row['status'] === 'active'" label="Bật/tắt" />
                                <a href="#" class="text-brand-600 hover:text-brand-800 text-xs font-medium">Sửa</a>
                                <button type="button" data-confirm data-confirm-title="Xóa bản ghi?" data-confirm-text="Bạn có chắc muốn xóa {{ $row['name'] }}?" class="text-red-600 hover:text-red-800 text-xs font-medium">Xóa</button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </x-admin::table.table>

    </x-admin::form.section>

    {{-- ================================================================== --}}
    {{-- SLIDE OVER --}}
    {{-- ================================================================== --}}

    <x-admin::form.section title="Slide Over">
        <button
            type="button"
            @click="$dispatch('open-slide-over', { id: 'demo-slide-over' })"
            class="px-4 py-2 bg-brand-500 text-white text-sm rounded hover:bg-brand-600 transition-colors"
        >
            Mở Slide Over
        </button>
    </x-admin::form.section>

    <x-admin::display.slide-over id="demo-slide-over" title="Thông tin chi tiết">
        <x-admin::display.detail-list :items="[
            'Họ tên' => 'Demo User',
            'Email' => 'demo@example.com',
        ]" />
    </x-admin::display.slide-over>

</div>
@endsection
