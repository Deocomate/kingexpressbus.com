{{-- Sort --}}
<div class="filter-section">
    <h3 class="filter-title">
        <i class="fa-solid fa-arrow-down-wide-short text-blue-500"></i>
        {{ __('client.route_show.filters.sort_title') }}
    </h3>
    <div class="space-y-2">
        @foreach ($sortOptions as $value => $label)
            <label class="flex items-center gap-3 text-sm text-gray-600 cursor-pointer hover:text-gray-900">
                <input type="radio" name="sort" value="{{ $value }}" @checked(($filterState['sort'] ?? 'recommended') === $value)
                    class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                <span>{{ $label }}</span>
            </label>
        @endforeach
    </div>
</div>

{{-- Price Range --}}
<div class="filter-section">
    <h3 class="filter-title">
        <i class="fa-solid fa-money-bill-wave text-emerald-500"></i>
        {{ __('client.route_show.filters.price_title') }}
    </h3>
    <div class="flex items-center gap-3">
        <input type="number" name="price_min" value="{{ $filterState['price_min'] ?? '' }}"
            placeholder="{{ $priceRange['min'] ?? 0 ? number_format($priceRange['min']) : __('client.route_show.filters.price_from') }}"
            class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
            min="0" inputmode="numeric">
        <span class="text-gray-300">-</span>
        <input type="number" name="price_max" value="{{ $filterState['price_max'] ?? '' }}"
            placeholder="{{ $priceRange['max'] ?? 0 ? number_format($priceRange['max']) : __('client.route_show.filters.price_to') }}"
            class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
            min="0" inputmode="numeric">
    </div>
</div>

{{-- Time Range --}}
@if (($timeRangeOptions ?? collect())->isNotEmpty())
    <div class="filter-section">
        <h3 class="filter-title">
            <i class="fa-solid fa-clock text-amber-500"></i>
            {{ __('client.route_show.filters.time_range_title') }}
        </h3>
        <div class="flex flex-wrap gap-2">
            @foreach ($timeRangeOptions as $key => $range)
                <label class="filter-pill">
                    <input type="checkbox" name="time_ranges[]" value="{{ $key }}"
                        @checked(in_array($key, $filterState['time_ranges'] ?? []))>
                    <span>{{ $range['label'] ?? $key }}</span>
                </label>
            @endforeach
        </div>
    </div>
@endif

{{-- Services --}}
@if (($availableServices ?? collect())->isNotEmpty())
    <div class="filter-section">
        <h3 class="filter-title">
            <i class="fa-solid fa-star text-yellow-500"></i>
            {{ __('client.route_show.filters.services_title') }}
        </h3>
        <div class="flex flex-wrap gap-2">
            @foreach ($availableServices as $service)
                <label class="filter-pill">
                    <input type="checkbox" name="services[]" value="{{ $service }}" @checked(in_array($service, $filterState['services'] ?? []))>
                    <span>{{ $service }}</span>
                </label>
            @endforeach
        </div>
    </div>
@endif

{{-- Bus Types --}}
@if (($busCategoryOptions ?? collect())->isNotEmpty())
    <div class="filter-section">
        <h3 class="filter-title">
            <i class="fa-solid fa-van-shuttle text-purple-500"></i>
            {{ __('client.route_show.filters.bus_type_title') }}
        </h3>
        <div class="space-y-2">
            @foreach ($busCategoryOptions as $category)
                <label class="flex items-center gap-3 text-sm text-gray-600 cursor-pointer hover:text-gray-900">
                    <input type="checkbox" name="bus_categories[]" value="{{ $category }}"
                        @checked(in_array($category, $filterState['bus_categories'] ?? [])) class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span>{{ $category }}</span>
                </label>
            @endforeach
        </div>
    </div>
@endif

{{-- Action Buttons --}}
<div class="filter-section bg-gray-50 rounded-b-2xl">
    <button type="submit"
        class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition flex items-center justify-center gap-2">
        <i class="fa-solid fa-check"></i>
        {{ __('client.route_show.filters.apply_button') }}
    </button>
    <a href="{{ $clearFiltersUrl }}"
        class="w-full mt-3 py-3.5 bg-white border border-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition flex items-center justify-center gap-2">
        <i class="fa-solid fa-rotate-left"></i>
        {{ __('client.route_show.filters.clear_button') }}
    </a>
</div>
