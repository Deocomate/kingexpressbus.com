{{-- Scrollable Filter Content --}}
<div class="filters-scrollable">
    {{-- Sort --}}
    <div class="filter-section filter-collapsible">
        <h3 class="filter-title filter-toggle" data-target="filter-sort">
            <span class="flex items-center gap-2">
                <i class="fa-solid fa-arrow-down-wide-short text-primary-600"></i>
                {{ __('client.route_show.filters.sort_title') }}
            </span>
            <i class="fa-solid fa-chevron-down filter-chevron text-xs text-gray-400 transition-transform duration-200"></i>
        </h3>
        <div id="filter-sort" class="filter-content">
            <div class="space-y-2">
                @foreach ($sortOptions as $value => $label)
                    <label class="flex items-center gap-3 text-sm text-gray-600 cursor-pointer hover:text-gray-900">
                        <input type="radio" name="sort" value="{{ $value }}" @checked(($filterState['sort'] ?? 'recommended') === $value)
                            class="h-4 w-4 text-primary-600 border-gray-300 focus:ring-primary-500">
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Price Range --}}
    <div class="filter-section filter-collapsible">
        <h3 class="filter-title filter-toggle" data-target="filter-price">
            <span class="flex items-center gap-2">
                <i class="fa-solid fa-money-bill-wave text-emerald-500"></i>
                {{ __('client.route_show.filters.price_title') }}
            </span>
            <i class="fa-solid fa-chevron-down filter-chevron text-xs text-gray-400 transition-transform duration-200"></i>
        </h3>
        <div id="filter-price" class="filter-content">
            <div class="flex items-center gap-3">
                <input type="number" name="price_min" value="{{ $filterState['price_min'] ?? '' }}"
                    placeholder="{{ $priceRange['min'] ?? 0 ? number_format($priceRange['min']) : __('client.route_show.filters.price_from') }}"
                    class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm transition focus:border-primary-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-100"
                    min="0" inputmode="numeric">
                <span class="text-neutral-300">-</span>
                <input type="number" name="price_max" value="{{ $filterState['price_max'] ?? '' }}"
                    placeholder="{{ $priceRange['max'] ?? 0 ? number_format($priceRange['max']) : __('client.route_show.filters.price_to') }}"
                    class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm transition focus:border-primary-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-100"
                    min="0" inputmode="numeric">
            </div>
        </div>
    </div>

    {{-- Time Range --}}
    @if (($timeRangeOptions ?? collect())->isNotEmpty())
        <div class="filter-section filter-collapsible">
            <h3 class="filter-title filter-toggle" data-target="filter-time">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-clock text-amber-500"></i>
                    {{ __('client.route_show.filters.time_range_title') }}
                </span>
                <i class="fa-solid fa-chevron-down filter-chevron text-xs text-gray-400 transition-transform duration-200"></i>
            </h3>
            <div id="filter-time" class="filter-content">
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
        </div>
    @endif

    {{-- Services --}}
    @if (($availableServices ?? collect())->isNotEmpty())
        <div class="filter-section filter-collapsible">
            <h3 class="filter-title filter-toggle" data-target="filter-services">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-star text-yellow-500"></i>
                    {{ __('client.route_show.filters.services_title') }}
                </span>
                <i class="fa-solid fa-chevron-down filter-chevron text-xs text-gray-400 transition-transform duration-200"></i>
            </h3>
            <div id="filter-services" class="filter-content">
                <div class="flex flex-wrap gap-2">
                    @foreach ($availableServices as $service)
                        <label class="filter-pill">
                            <input type="checkbox" name="services[]" value="{{ $service }}" @checked(in_array($service, $filterState['services'] ?? []))>
                            <span>{{ $service }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Bus Types --}}
    @if (($busCategoryOptions ?? collect())->isNotEmpty())
        <div class="filter-section filter-collapsible">
            <h3 class="filter-title filter-toggle" data-target="filter-bus-types">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-van-shuttle text-purple-500"></i>
                    {{ __('client.route_show.filters.bus_type_title') }}
                </span>
                <i class="fa-solid fa-chevron-down filter-chevron text-xs text-gray-400 transition-transform duration-200"></i>
            </h3>
            <div id="filter-bus-types" class="filter-content">
                <div class="space-y-2">
                    @foreach ($busCategoryOptions as $category)
                        <label class="flex items-center gap-3 text-sm text-gray-600 cursor-pointer hover:text-gray-900">
                            <input type="checkbox" name="bus_categories[]" value="{{ $category }}"
                                @checked(in_array($category, $filterState['bus_categories'] ?? [])) class="h-4 w-4 text-primary-600 border-gray-300 focus:ring-primary-500">
                            <span>{{ $category }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Sticky Action Buttons --}}
<div class="filters-sticky-footer">
    <button type="submit"
        class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary-600 py-3 text-sm font-semibold text-white transition duration-200 hover:bg-primary-700 active:scale-95">
        <i class="fa-solid fa-check"></i>
        {{ __('client.route_show.filters.apply_button') }}
    </button>
    <a href="{{ $clearFiltersUrl }}"
        class="mt-2 flex w-full items-center justify-center gap-2 rounded-xl border border-neutral-200 bg-white py-2.5 text-sm font-medium text-neutral-600 transition duration-200 hover:border-primary-200 hover:bg-primary-50 hover:text-primary-700">
        <i class="fa-solid fa-rotate-left"></i>
        {{ __('client.route_show.filters.clear_button') }}
    </a>
</div>

