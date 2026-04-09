@php
    $filterIdPrefix = $filterIdPrefix ?? '';
@endphp

<div class="grow space-y-0">
    <div class="filter-collapsible border-b border-slate-100 p-5">
        <button type="button" class="filter-toggle flex w-full items-center justify-between text-xs font-bold uppercase tracking-[0.06em] text-slate-500" data-target="{{ $filterIdPrefix }}filter-sort" aria-expanded="true">
            <span class="flex items-center gap-2">
                <i class="fa-solid fa-arrow-down-wide-short text-primary-600"></i>
                {{ __('client.route_show.filters.sort_title') }}
            </span>
            <i class="fa-solid fa-chevron-down filter-chevron text-[10px] text-slate-400 transition-transform duration-200"></i>
        </button>
        <div id="{{ $filterIdPrefix }}filter-sort" class="filter-content mt-3 space-y-2.5">
            @foreach ($sortOptions as $value => $label)
                <label class="flex cursor-pointer items-center gap-3 text-sm text-slate-600 hover:text-slate-900">
                    <input type="radio" name="sort" value="{{ $value }}" @checked(($filterState['sort'] ?? 'recommended') === $value)
                        class="h-4 w-4 border-gray-300 text-primary-600 focus:ring-primary-500">
                    <span>{{ $label }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <div class="filter-collapsible border-b border-slate-100 p-5">
        <button type="button" class="filter-toggle flex w-full items-center justify-between text-xs font-bold uppercase tracking-[0.06em] text-slate-500" data-target="{{ $filterIdPrefix }}filter-price" aria-expanded="true">
            <span class="flex items-center gap-2">
                <i class="fa-solid fa-money-bill-wave text-emerald-500"></i>
                {{ __('client.route_show.filters.price_title') }}
            </span>
            <i class="fa-solid fa-chevron-down filter-chevron text-[10px] text-slate-400 transition-transform duration-200"></i>
        </button>
        <div id="{{ $filterIdPrefix }}filter-price" class="filter-content mt-3">
            <div class="flex items-center gap-3">
                <input type="number" name="price_min" value="{{ $filterState['price_min'] ?? '' }}"
                    placeholder="{{ $priceRange['min'] ?? 0 ? number_format($priceRange['min']) : __('client.route_show.filters.price_from') }}"
                    class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-2.5 text-sm transition focus:border-primary-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-100"
                    min="0" inputmode="numeric">
                <span class="text-neutral-300">-</span>
                <input type="number" name="price_max" value="{{ $filterState['price_max'] ?? '' }}"
                    placeholder="{{ $priceRange['max'] ?? 0 ? number_format($priceRange['max']) : __('client.route_show.filters.price_to') }}"
                    class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-2.5 text-sm transition focus:border-primary-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-100"
                    min="0" inputmode="numeric">
            </div>
        </div>
    </div>

    @if (($timeRangeOptions ?? collect())->isNotEmpty())
        <div class="filter-collapsible border-b border-slate-100 p-5">
            <button type="button" class="filter-toggle flex w-full items-center justify-between text-xs font-bold uppercase tracking-[0.06em] text-slate-500" data-target="{{ $filterIdPrefix }}filter-time" aria-expanded="true">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-clock text-amber-500"></i>
                    {{ __('client.route_show.filters.time_range_title') }}
                </span>
                <i class="fa-solid fa-chevron-down filter-chevron text-[10px] text-slate-400 transition-transform duration-200"></i>
            </button>
            <div id="{{ $filterIdPrefix }}filter-time" class="filter-content mt-3">
                <div class="flex flex-wrap gap-2">
                    @foreach ($timeRangeOptions as $key => $range)
                        <label class="cursor-pointer">
                            <input type="checkbox" name="time_ranges[]" value="{{ $key }}"
                                @checked(in_array($key, $filterState['time_ranges'] ?? [])) class="peer sr-only">
                            <span class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-medium text-slate-600 transition peer-checked:border-primary-600 peer-checked:bg-primary-600 peer-checked:text-white">
                                {{ $range['label'] ?? $key }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if (($availableServices ?? collect())->isNotEmpty())
        <div class="filter-collapsible border-b border-slate-100 p-5">
            <button type="button" class="filter-toggle flex w-full items-center justify-between text-xs font-bold uppercase tracking-[0.06em] text-slate-500" data-target="{{ $filterIdPrefix }}filter-services" aria-expanded="true">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-star text-yellow-500"></i>
                    {{ __('client.route_show.filters.services_title') }}
                </span>
                <i class="fa-solid fa-chevron-down filter-chevron text-[10px] text-slate-400 transition-transform duration-200"></i>
            </button>
            <div id="{{ $filterIdPrefix }}filter-services" class="filter-content mt-3">
                <div class="flex flex-wrap gap-2">
                    @foreach ($availableServices as $service)
                        <label class="cursor-pointer">
                            <input type="checkbox" name="services[]" value="{{ $service }}" @checked(in_array($service, $filterState['services'] ?? [])) class="peer sr-only">
                            <span class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-medium text-slate-600 transition peer-checked:border-primary-600 peer-checked:bg-primary-600 peer-checked:text-white">
                                {{ $service }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if (($busCategoryOptions ?? collect())->isNotEmpty())
        <div class="filter-collapsible p-5">
            <button type="button" class="filter-toggle flex w-full items-center justify-between text-xs font-bold uppercase tracking-[0.06em] text-slate-500" data-target="{{ $filterIdPrefix }}filter-bus-types" aria-expanded="true">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-van-shuttle text-purple-500"></i>
                    {{ __('client.route_show.filters.bus_type_title') }}
                </span>
                <i class="fa-solid fa-chevron-down filter-chevron text-[10px] text-slate-400 transition-transform duration-200"></i>
            </button>
            <div id="{{ $filterIdPrefix }}filter-bus-types" class="filter-content mt-3 space-y-2.5">
                @foreach ($busCategoryOptions as $category)
                    <label class="flex cursor-pointer items-center gap-3 text-sm text-slate-600 hover:text-slate-900">
                        <input type="checkbox" name="bus_categories[]" value="{{ $category }}"
                            @checked(in_array($category, $filterState['bus_categories'] ?? [])) class="h-4 w-4 border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span>{{ $category }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    @endif
</div>

<div class="border-t border-amber-100 bg-amber-50/60 p-4">
    <button type="submit"
        class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary-600 py-2.5 text-sm font-semibold text-white transition duration-200 hover:bg-primary-700">
        <i class="fa-solid fa-check"></i>
        {{ __('client.route_show.filters.apply_button') }}
    </button>
    <a href="{{ $clearFiltersUrl }}"
        class="mt-2 flex w-full items-center justify-center gap-2 rounded-xl border border-neutral-200 bg-white py-2.5 text-sm font-medium text-neutral-600 transition duration-200 hover:border-primary-200 hover:bg-primary-50 hover:text-primary-700">
        <i class="fa-solid fa-rotate-left"></i>
        {{ __('client.route_show.filters.clear_button') }}
    </a>
</div>

