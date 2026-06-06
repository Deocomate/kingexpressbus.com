@php
    $defaults = $searchData['defaults'] ?? [];
    $searchPayload = [
        'locations' => $searchData['locations'] ?? [],
        'defaults' => $defaults,
        'translations' => [
            'noResults' => __('client.search.no_results'),
            'loading' => __('client.search.loading'),
            'submit' => $submitLabel ?? __('client.search.submit'),
            'originLabel' => __('client.search.origin_label'),
            'destinationLabel' => __('client.search.destination_label'),
            'departureLabel' => __('client.search.departure_label'),
            'returnLabel' => __('client.search.return_label'),
            'addReturn' => __('client.search.add_return'),
            'datePlaceholder' => __('client.search.date_placeholder'),
            'validationSelect' => __('client.search.validation.select_suggestion'),
            'validationDifferent' => __('client.search.validation.different_locations'),
            'types' => [
                'province' => __('client.search.types.province'),
                'district' => __('client.search.types.district'),
                'stop' => __('client.search.types.stop'),
            ],
        ],
    ];
@endphp

<div x-data="kingSearchBar(@js($searchPayload))" x-init="init()" class="relative">
    <form x-ref="form" action="{{ $action }}" method="GET" @submit.prevent="submitForm"
        class="ksb-surface p-3 sm:p-4">
        <input type="hidden" name="origin_id" :value="origin.id || ''">
        <input type="hidden" name="origin_type" :value="origin.type || ''">
        <input type="hidden" name="origin_label" :value="origin.name || ''">
        <input type="hidden" name="destination_id" :value="destination.id || ''">
        <input type="hidden" name="destination_type" :value="destination.type || ''">
        <input type="hidden" name="destination_label" :value="destination.name || ''">
        <input type="hidden" name="departure_date" :value="departureDate">
        <input type="hidden" name="return_date" :value="showReturn ? returnDate : ''">

        <div class="grid gap-3 lg:grid-cols-[minmax(0,1.2fr)_auto_minmax(0,1.2fr)_minmax(0,0.85fr)_minmax(0,0.85fr)_auto] lg:items-center">
            <div class="relative" @click.outside="if (activeDropdown === 'origin') activeDropdown = null">
                <button type="button" @click="toggleDropdown('origin')"
                    class="ksb-form-control group flex h-14 w-full items-center gap-3 px-4 text-left"
                    :class="activeDropdown === 'origin' ? 'border-primary-600 bg-white shadow-[var(--ksb-focus)]' : ''"
                    aria-haspopup="listbox" :aria-expanded="activeDropdown === 'origin'">
                    <img src="{{ asset('/client/icons/pickup.svg') }}" alt="{{ __('client.search.origin_icon_alt') }}" class="h-5 w-5 shrink-0">
                    <span class="min-w-0 flex-1">
                        <span class="block text-[11px] font-semibold uppercase tracking-wide text-slate-400" x-text="translations.originLabel"></span>
                        <span class="block truncate text-sm font-bold text-slate-800"
                            x-text="origin.name || '{{ __('client.search.origin_placeholder') }}'"></span>
                    </span>
                    <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition" :class="activeDropdown === 'origin' ? 'rotate-180 text-primary-600' : ''"></i>
                </button>

                <div x-show="activeDropdown === 'origin'" x-cloak x-transition class="ksb-dropdown absolute left-0 right-0 top-[calc(100%+8px)]">
                    <div class="ksb-slide-fade overflow-hidden rounded-2xl border border-amber-100 bg-white shadow-card">
                        <div class="border-b border-amber-100 p-3">
                            <label class="relative block">
                                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                                <input x-ref="originSearch" x-model="queries.origin" type="text" autocomplete="off"
                                    class="ksb-form-control h-11 w-full pl-9 pr-3 text-sm"
                                    :placeholder="translations.originLabel">
                            </label>
                        </div>
                        <div class="max-h-72 overflow-y-auto p-2" role="listbox">
                            <template x-for="group in groupedLocations('origin')" :key="group.type">
                                <div class="mb-2 last:mb-0">
                                    <p class="px-2 py-1 text-[11px] font-bold uppercase tracking-wide text-slate-400" x-text="group.label"></p>
                                    <template x-for="item in group.items" :key="item.type + '-' + item.id">
                                        <button type="button" @click="selectLocation('origin', item)"
                                            class="mb-1 flex w-full items-start gap-2 rounded-xl px-3 py-2 text-left transition last:mb-0"
                                            :class="isSelected('origin', item) ? 'bg-primary-50 text-primary-700' : 'text-slate-700 hover:bg-slate-50'">
                                            <i class="fa-solid fa-location-dot mt-1 text-xs" :class="isSelected('origin', item) ? 'text-primary-600' : 'text-slate-300'"></i>
                                            <span class="min-w-0">
                                                <span class="block truncate text-sm font-semibold" x-text="item.name"></span>
                                                <span class="block truncate text-xs text-slate-500" x-text="item.context || item.type_label || ''"></span>
                                            </span>
                                        </button>
                                    </template>
                                </div>
                            </template>
                            <p x-show="groupedLocations('origin').length === 0" class="px-3 py-5 text-center text-sm text-slate-500" x-text="translations.noResults"></p>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" @click="swapLocations"
                class="hidden h-11 w-11 items-center justify-center rounded-xl border border-primary-100 bg-white text-primary-600 shadow-soft transition hover:border-primary-500 hover:text-primary-700 active:scale-95 lg:inline-flex"
                aria-label="{{ __('client.search.swap_aria') }}">
                <i class="fa-solid fa-right-left"></i>
            </button>

            <div class="relative" @click.outside="if (activeDropdown === 'destination') activeDropdown = null">
                <button type="button" @click="toggleDropdown('destination')"
                    class="ksb-form-control group flex h-14 w-full items-center gap-3 px-4 text-left"
                    :class="activeDropdown === 'destination' ? 'border-primary-600 bg-white shadow-[var(--ksb-focus)]' : ''"
                    aria-haspopup="listbox" :aria-expanded="activeDropdown === 'destination'">
                    <img src="{{ asset('/client/icons/dropoff.svg') }}" alt="{{ __('client.search.destination_icon_alt') }}" class="h-5 w-5 shrink-0">
                    <span class="min-w-0 flex-1">
                        <span class="block text-[11px] font-semibold uppercase tracking-wide text-slate-400" x-text="translations.destinationLabel"></span>
                        <span class="block truncate text-sm font-bold text-slate-800"
                            x-text="destination.name || '{{ __('client.search.destination_placeholder') }}'"></span>
                    </span>
                    <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition" :class="activeDropdown === 'destination' ? 'rotate-180 text-primary-600' : ''"></i>
                </button>

                <div x-show="activeDropdown === 'destination'" x-cloak x-transition class="ksb-dropdown absolute left-0 right-0 top-[calc(100%+8px)]">
                    <div class="ksb-slide-fade overflow-hidden rounded-2xl border border-amber-100 bg-white shadow-card">
                        <div class="border-b border-amber-100 p-3">
                            <label class="relative block">
                                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                                <input x-ref="destinationSearch" x-model="queries.destination" type="text" autocomplete="off"
                                    class="ksb-form-control h-11 w-full pl-9 pr-3 text-sm"
                                    :placeholder="translations.destinationLabel">
                            </label>
                        </div>
                        <div class="max-h-72 overflow-y-auto p-2" role="listbox">
                            <template x-for="group in groupedLocations('destination')" :key="group.type">
                                <div class="mb-2 last:mb-0">
                                    <p class="px-2 py-1 text-[11px] font-bold uppercase tracking-wide text-slate-400" x-text="group.label"></p>
                                    <template x-for="item in group.items" :key="item.type + '-' + item.id">
                                        <button type="button" @click="selectLocation('destination', item)"
                                            class="mb-1 flex w-full items-start gap-2 rounded-xl px-3 py-2 text-left transition last:mb-0"
                                            :class="isSelected('destination', item) ? 'bg-primary-50 text-primary-700' : 'text-slate-700 hover:bg-slate-50'">
                                            <i class="fa-solid fa-location-dot mt-1 text-xs" :class="isSelected('destination', item) ? 'text-primary-600' : 'text-slate-300'"></i>
                                            <span class="min-w-0">
                                                <span class="block truncate text-sm font-semibold" x-text="item.name"></span>
                                                <span class="block truncate text-xs text-slate-500" x-text="item.context || item.type_label || ''"></span>
                                            </span>
                                        </button>
                                    </template>
                                </div>
                            </template>
                            <p x-show="groupedLocations('destination').length === 0" class="px-3 py-5 text-center text-sm text-slate-500" x-text="translations.noResults"></p>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" @click="openDeparturePicker"
                class="ksb-form-control group flex h-14 w-full items-center gap-3 px-4 text-left">
                <img src="{{ asset('/client/icons/date.svg') }}" alt="{{ __('client.search.departure_icon_alt') }}" class="h-5 w-5 shrink-0">
                <span class="min-w-0 flex-1">
                    <span class="block text-[11px] font-semibold uppercase tracking-wide text-slate-400" x-text="translations.departureLabel"></span>
                    <span class="block truncate text-sm font-bold" :class="departureDate ? 'text-slate-800' : 'text-slate-400'" x-text="departureDate || translations.datePlaceholder"></span>
                </span>
                <input x-ref="departureInput" type="text" class="pointer-events-none absolute h-0 w-0 opacity-0" aria-hidden="true" tabindex="-1">
            </button>

            <div class="relative">
                <template x-if="showReturn">
                    <div class="relative">
                        <button type="button" @click="openReturnPicker"
                            class="ksb-form-control group flex h-14 w-full items-center gap-3 px-4 pr-10 text-left">
                            <img src="{{ asset('/client/icons/date.svg') }}" alt="{{ __('client.search.return_icon_alt') }}" class="h-5 w-5 shrink-0">
                            <span class="min-w-0 flex-1">
                                <span class="block text-[11px] font-semibold uppercase tracking-wide text-slate-400" x-text="translations.returnLabel"></span>
                                <span class="block truncate text-sm font-bold" :class="returnDate ? 'text-slate-800' : 'text-slate-400'" x-text="returnDate || translations.datePlaceholder"></span>
                            </span>
                        </button>
                        <button type="button" @click="removeReturnDate"
                            class="absolute right-2 top-1/2 inline-flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg text-slate-400 transition hover:bg-rose-50 hover:text-rose-500"
                            aria-label="{{ __('client.search.remove_return_aria') }}">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                        <input x-ref="returnInput" type="text" class="pointer-events-none absolute h-0 w-0 opacity-0" aria-hidden="true" tabindex="-1">
                    </div>
                </template>
                <template x-if="!showReturn">
                    <button type="button" @click="enableReturnDate"
                        class="flex h-14 w-full items-center justify-center gap-2 rounded-xl border border-dashed border-primary-500/35 bg-primary-50 text-sm font-semibold text-primary-700 transition hover:border-primary-600 hover:bg-primary-100 active:scale-[0.99]">
                        <i class="fa-solid fa-plus"></i>
                        <span x-text="translations.addReturn"></span>
                    </button>
                </template>
            </div>

            <div class="flex items-center gap-2 lg:justify-end">
                <button type="button" @click="swapLocations"
                    class="inline-flex h-12 w-12 items-center justify-center rounded-xl border border-slate-200 text-slate-500 transition hover:border-primary-500 hover:text-primary-600 active:scale-95 lg:hidden"
                    aria-label="{{ __('client.search.swap_aria') }}">
                    <i class="fa-solid fa-right-left"></i>
                </button>
                <button type="submit" x-ref="submitButton" data-default-label="{{ $submitLabel ?? __('client.search.submit') }}"
                    class="ksb-btn-primary h-14 min-w-36 px-6 text-sm disabled:cursor-not-allowed disabled:opacity-70">
                    {{ $submitLabel ?? __('client.search.submit') }}
                </button>
            </div>
        </div>

        <p x-show="errorMessage" x-text="errorMessage" class="mt-3 rounded-xl border border-rose-100 bg-rose-50 px-3 py-2 text-sm text-rose-600"></p>
    </form>
</div>
