{{--
  Polled partial: table rows + pagination.
  Alpine polls this via fetch() every 60s when the browser tab is visible.
  Receives: $paginator, $config, $activeTab, $activeSortKey, $activeSortDir, $badges
  Optional: $activeSearch, $activeFilters
--}}
@php
    use App\Enums\BookingStatus;
    use App\Enums\PaymentStatus;

    $statusColorMap = [
        'pending'   => 'warning',
        'confirmed' => 'success',
        'cancelled' => 'danger',
        'completed' => 'info',
    ];
    $payStatusColorMap = [
        'unpaid' => 'warning',
        'paid'   => 'success',
    ];

    $search = $activeSearch ?? request('search', '');
    $filters = $activeFilters ?? request('filter', []);
    $colSpan = count($config->getColumns()) + 1; // + actions
@endphp

<x-admin::table.tabs
    :tabs="$config->getTabs()"
    :activeTab="$activeTab"
    :badges="$badges"
/>

<div class="overflow-x-auto">
<table class="min-w-full text-sm">
    <thead>
        <tr class="border-b border-gray-200 bg-gray-50">
            @foreach($config->getColumns() as $column)
            <th
                data-col="{{ $column->key }}"
                class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wide whitespace-nowrap {{ in_array($column->key, ['quantity', 'total_price'], true) ? 'text-right' : '' }}"
            >
                @include('admin.bookings._sort-th', [
                    'key' => $column->key,
                    'label' => $column->label,
                    'sortable' => $column->sortable || isset($config->getSortWhitelist()[$column->key]),
                    'sortKey' => $activeSortKey,
                    'sortDir' => $activeSortDir,
                    'activeTab' => $activeTab,
                    'search' => $search,
                    'filters' => $filters,
                ])
            </th>
            @endforeach
            <th class="px-4 py-2.5 text-right text-xs font-medium text-gray-500 uppercase tracking-wide">Thao tác</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-100">
        @forelse($paginator as $booking)
        @php
            $statusColor = $statusColorMap[$booking->status?->value] ?? 'secondary';
            $payColor    = $payStatusColorMap[$booking->payment_status?->value] ?? 'secondary';
            $isPending   = $booking->status === BookingStatus::Pending;
            $isConfirmed = $booking->status === BookingStatus::Confirmed;
            $canCancel   = ! in_array($booking->status, [BookingStatus::Cancelled, BookingStatus::Completed], true);
        @endphp
        <tr class="hover:bg-gray-50 transition-colors">
            <td data-col="booking_code" class="px-4 py-3 font-mono text-xs text-gray-700 whitespace-nowrap">
                <div class="flex items-center gap-1">
                    <button
                        type="button"
                        data-open-detail="{{ $booking->id }}"
                        class="hover:text-brand-600 hover:underline cursor-pointer text-left"
                    >{{ $booking->booking_code }}</button>
                    <button
                        type="button"
                        data-copy-code="{{ $booking->booking_code }}"
                        title="Sao chép mã"
                        class="p-0.5 text-gray-400 hover:text-brand-600 transition-colors"
                        aria-label="Sao chép mã đặt vé"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                </div>
            </td>

            <td data-col="created_at" class="px-4 py-3 text-gray-600 whitespace-nowrap text-xs">
                {{ $booking->created_at?->format('d/m/Y H:i') }}
            </td>

            <td data-col="departure_at" class="px-4 py-3 whitespace-nowrap text-xs">
                <span class="text-gray-800">{{ $booking->departure_at ? \Carbon\Carbon::parse($booking->departure_at)->format('d/m/Y H:i') : '—' }}</span>
                @if($booking->booking_date?->isToday())
                    <br><span class="text-brand-600 font-medium text-xs">Đi hôm nay</span>
                @elseif($booking->booking_date?->isTomorrow())
                    <br><span class="text-yellow-600 text-xs">Đi ngày mai</span>
                @endif
            </td>

            <td data-col="trip_route" class="px-4 py-3 text-gray-700 text-xs">{{ $booking->trip?->route?->name ?? '—' }}</td>

            <td data-col="customer_name" class="px-4 py-3 text-xs">
                <span class="font-medium text-gray-900">{{ $booking->customer_name }}</span>
                @if($booking->customer_phone)
                <br><span class="text-gray-500">{{ $booking->customer_phone }}</span>
                @endif
            </td>

            <td data-col="pickup_dropoff" class="px-4 py-3 text-xs text-gray-700">
                {{ $booking->pickupStop?->name ?? '—' }}
                @if($booking->dropoffStop?->name)
                <br><span class="text-gray-500">→ {{ $booking->dropoffStop->name }}</span>
                @endif
            </td>

            <td data-col="quantity" class="px-4 py-3 text-right text-gray-700 text-xs">{{ $booking->quantity }}</td>

            <td data-col="total_price" class="px-4 py-3 text-right text-gray-900 font-medium text-xs whitespace-nowrap">
                {{ number_format($booking->total_price, 0, ',', '.') }} ₫
            </td>

            <td data-col="status" class="px-4 py-3">
                <x-admin::display.badge :color="$statusColor">{{ $booking->status?->getLabel() }}</x-admin::display.badge>
            </td>

            <td data-col="payment_status" class="px-4 py-3">
                <x-admin::display.badge :color="$payColor">{{ $booking->payment_status?->getLabel() }}</x-admin::display.badge>
            </td>

            <td data-col="customer_email" class="px-4 py-3 text-xs text-gray-600">
                {{ $booking->customer_email ?: '—' }}
            </td>

            <td data-col="payment_method" class="px-4 py-3 text-xs text-gray-600">
                {{ $booking->payment_method?->getLabel() ?? '—' }}
            </td>

            <td data-col="booking_date" class="px-4 py-3 text-xs text-gray-600 whitespace-nowrap">
                {{ $booking->booking_date?->format('d/m/Y') ?? '—' }}
            </td>

            <td data-col="confirmed_at" class="px-4 py-3 text-xs text-gray-600 whitespace-nowrap">
                {{ $booking->confirmed_at?->format('d/m/Y H:i') ?? '—' }}
            </td>

            <td data-col="updated_at" class="px-4 py-3 text-xs text-gray-600 whitespace-nowrap">
                {{ $booking->updated_at?->format('d/m/Y H:i') ?? '—' }}
            </td>

            <td class="px-4 py-3 text-right whitespace-nowrap space-x-1">
                <button
                    type="button"
                    data-open-detail="{{ $booking->id }}"
                    class="inline-flex items-center px-2 py-1 text-xs rounded border border-gray-300 text-gray-600 hover:bg-gray-100 transition-colors"
                >Xem</button>

                @if($isPending)
                <form method="POST" action="{{ route('admin.bookings.confirm', $booking) }}" class="inline"
                      onsubmit="return confirm('Xác nhận đặt vé này?')">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center px-2 py-1 text-xs rounded border border-green-300 text-green-700 hover:bg-green-50 transition-colors">
                        Xác nhận
                    </button>
                </form>
                @endif

                @if($isConfirmed)
                <form method="POST" action="{{ route('admin.bookings.complete', $booking) }}" class="inline"
                      onsubmit="return confirm('Đánh dấu hoàn thành?')">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center px-2 py-1 text-xs rounded border border-blue-300 text-blue-700 hover:bg-blue-50 transition-colors">
                        Hoàn thành
                    </button>
                </form>
                @endif

                @if($canCancel)
                <button
                    type="button"
                    data-open-cancel="{{ $booking->id }}"
                    data-booking-code="{{ $booking->booking_code }}"
                    class="inline-flex items-center px-2 py-1 text-xs rounded border border-red-300 text-red-700 hover:bg-red-50 transition-colors">
                    Hủy vé
                </button>
                @endif

                <a href="{{ route('admin.bookings.edit', $booking) }}"
                   class="inline-flex items-center px-2 py-1 text-xs rounded border border-gray-300 text-gray-600 hover:bg-gray-100 transition-colors">
                    Sửa
                </a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="{{ $colSpan }}" class="px-4 py-10 text-center text-sm text-gray-400">
                Không có đặt vé nào.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
</div>

@if($paginator->hasPages())
    <x-admin::table.pagination :paginator="$paginator" />
@endif
