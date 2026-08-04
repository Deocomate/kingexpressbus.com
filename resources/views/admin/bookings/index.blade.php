@extends('admin.layouts.app')

@section('title', 'Đặt vé')
@section('breadcrumb')
    <span class="text-gray-700 font-medium">Đặt vé</span>
@endsection

@section('content')
@php
    $hiddenByDefault = collect($config->getColumns())
        ->filter(fn ($c) => $c->defaultHidden)
        ->pluck('key')
        ->all();
@endphp
<style>
    @foreach($hiddenByDefault as $colKey)
    .booking-col-hide-{{ $colKey }} [data-col="{{ $colKey }}"] { display: none !important; }
    @endforeach
</style>
<div
    x-data="bookingList(@js($hiddenByDefault))"
    x-init="startPolling()"
    @click="onDelegatedClick($event)"
    :class="hiddenColClasses"
    class="space-y-4"
>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    {{-- Toolbar --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold text-gray-900">Đặt vé</h1>
        <div class="flex items-center gap-2">
            @php
                $hideableColumns = array_filter(
                    $config->getColumns(),
                    fn ($c) => is_object($c) ? $c->hideable : true
                );
            @endphp
            @if(!empty($hideableColumns))
            <div x-data="{ open: false }" class="relative" data-column-toggle data-table-id="booking-table">
                <button
                    type="button"
                    @click="open = !open"
                    class="flex items-center gap-1 px-3 py-2 text-sm text-gray-600 border border-gray-300 rounded hover:bg-gray-50 transition-colors"
                    aria-haspopup="true"
                    :aria-expanded="open"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    Cột
                </button>
                <div
                    x-show="open"
                    @click.outside="open = false"
                    x-cloak
                    class="absolute right-0 z-20 mt-1 w-48 bg-white rounded border border-gray-200 shadow-lg py-1"
                >
                    @foreach($hideableColumns as $column)
                    <label class="flex items-center gap-2 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 cursor-pointer">
                        <input
                            type="checkbox"
                            @if(! $column->defaultHidden) checked @endif
                            data-column-key="{{ $column->key }}"
                            class="rounded border-gray-300 text-brand-500 focus:ring-brand-500"
                        >
                        {{ $column->label }}
                    </label>
                    @endforeach
                </div>
            </div>
            @endif
            <a href="{{ route('admin.bookings.create') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded hover:bg-brand-700 transition-colors">
                + Tạo đặt vé
            </a>
        </div>
    </div>

    {{-- Search & Filters --}}
    <form method="GET" action="{{ route('admin.bookings.index') }}" class="flex flex-wrap gap-2 items-end" data-table-filter>
        <input type="hidden" name="tab" value="{{ $activeTab }}">

        <div class="flex-1 min-w-48">
            <input
                type="text"
                name="search"
                value="{{ $activeSearch }}"
                placeholder="Tìm mã vé, tên, SĐT, email..."
                class="block w-full rounded border border-gray-300 py-2 px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500"
            >
        </div>

        <select name="filter[status]"
                class="rounded border border-gray-300 py-2 px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500">
            <option value="">— Trạng thái —</option>
            @foreach($statusOptions as $val => $label)
            <option value="{{ $val }}" @selected(($activeFilters['status'] ?? '') === $val)>{{ $label }}</option>
            @endforeach
        </select>

        <select name="filter[payment_status]"
                class="rounded border border-gray-300 py-2 px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500">
            <option value="">— Thanh toán —</option>
            @foreach($paymentStatusOptions as $val => $label)
            <option value="{{ $val }}" @selected(($activeFilters['payment_status'] ?? '') === $val)>{{ $label }}</option>
            @endforeach
        </select>

        <input type="date" name="filter[from]" value="{{ $activeFilters['from'] ?? '' }}"
               title="Từ ngày đi"
               class="rounded border border-gray-300 py-2 px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500">
        <input type="date" name="filter[until]" value="{{ $activeFilters['until'] ?? '' }}"
               title="Đến ngày đi"
               class="rounded border border-gray-300 py-2 px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500">

        <button type="submit"
                class="px-4 py-2 bg-gray-700 text-white text-sm font-medium rounded hover:bg-gray-800 transition-colors">
            Lọc
        </button>
        @if($activeSearch || array_filter($activeFilters))
        <a href="{{ route('admin.bookings.index', ['tab' => $activeTab]) }}"
           class="px-3 py-2 text-sm text-gray-600 border border-gray-300 rounded hover:bg-gray-50 transition-colors">
            Xóa bộ lọc
        </a>
        @endif
    </form>

    {{-- Main card — tabs live inside poll wrapper so badge counts refresh --}}
    <div class="bg-white rounded border border-gray-200 shadow-sm overflow-hidden">
        <div id="booking-table-wrapper" data-table>
            @include('admin.bookings._table', [
                'paginator'      => $paginator,
                'config'         => $config,
                'activeTab'      => $activeTab,
                'activeSortKey'  => $activeSortKey,
                'activeSortDir'  => $activeSortDir,
                'activeSearch'   => $activeSearch,
                'activeFilters'  => $activeFilters,
                'badges'         => $badges,
            ])
        </div>
    </div>

    @include('admin.bookings._detail')
    @include('admin.bookings._cancel-form')

</div>

@push('scripts')
<script>
function bookingList(hiddenKeys) {
    const hidden = {};
    (hiddenKeys || []).forEach((k) => { hidden[k] = true; });

    return {
        pollInterval: null,
        hiddenCols: hidden,
        detailUrlTemplate: @json(route('admin.bookings.show', ['booking' => '__ID__'])),

        get hiddenColClasses() {
            return Object.entries(this.hiddenCols)
                .filter(([, v]) => v)
                .map(([k]) => 'booking-col-hide-' + k)
                .join(' ');
        },

        startPolling() {
            this.bindColumnToggle();
            this.pollInterval = setInterval(() => {
                if (document.visibilityState !== 'visible') return;
                this.refreshTable();
            }, 60000);
        },

        bindColumnToggle() {
            this.$el.querySelectorAll('[data-column-toggle] input[data-column-key]').forEach((input) => {
                const key = input.getAttribute('data-column-key');
                input.checked = !this.hiddenCols[key];
                input.addEventListener('change', () => {
                    this.hiddenCols[key] = !input.checked;
                });
            });
        },

        onDelegatedClick(e) {
            const detailBtn = e.target.closest('[data-open-detail]');
            if (detailBtn) {
                this.$dispatch('open-booking-detail', {
                    bookingId: Number(detailBtn.getAttribute('data-open-detail')),
                });
                return;
            }

            const cancelBtn = e.target.closest('[data-open-cancel]');
            if (cancelBtn) {
                this.$dispatch('open-cancel-slide-over', {
                    bookingId: Number(cancelBtn.getAttribute('data-open-cancel')),
                    bookingCode: cancelBtn.getAttribute('data-booking-code') || '',
                });
                return;
            }

            const copyBtn = e.target.closest('[data-copy-code]');
            if (copyBtn) {
                const code = copyBtn.getAttribute('data-copy-code');
                if (navigator.clipboard?.writeText) {
                    navigator.clipboard.writeText(code).then(() => {
                        window.Alpine?.store('toast')?.success?.('Đã sao chép ' + code);
                    });
                }
            }
        },

        async refreshTable() {
            const params = new URLSearchParams(window.location.search);
            const url = @json(route('admin.bookings.table')) + '?' + params.toString();
            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!response.ok) return;
                const html = await response.text();
                const wrapper = document.getElementById('booking-table-wrapper');
                if (!wrapper) return;

                if (window.AdminRegistry?.destroyTree) {
                    window.AdminRegistry.destroyTree(wrapper);
                }
                wrapper.innerHTML = html;
                if (window.AdminRegistry?.initTree) {
                    window.AdminRegistry.initTree(wrapper);
                }
            } catch (e) {
                // Silent fail — user sees last known data
            }
        },

        destroy() {
            if (this.pollInterval) clearInterval(this.pollInterval);
        }
    };
}
</script>
@endpush
@endsection
