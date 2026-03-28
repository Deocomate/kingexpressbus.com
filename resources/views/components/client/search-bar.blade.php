@php
	$defaults = $searchData['defaults'] ?? [];
	$searchPayload = [
		'locations' => $searchData['locations'] ?? [],
		'defaults' => $defaults,
		'translations' => [
			'noResults' => __('client.search.no_results'),
			'loading' => __('client.search.loading'),
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

@once
	@push('styles')
		<style>
			@keyframes ksb-slide-fade {
				from {
					opacity: 0;
					transform: translateY(-10px) scale(0.985);
				}

				to {
					opacity: 1;
					transform: translateY(0) scale(1);
				}
			}

			.animate-ksb-slide-fade {
				animation: ksb-slide-fade .22s ease-out;
			}

			.flatpickr-day.selected,
			.flatpickr-day.startRange,
			.flatpickr-day.endRange {
				border-color: #FF9B00;
				background: #FF9B00;
			}

			.flatpickr-day.today {
				border-color: #FFC900;
			}

			.flatpickr-day:hover {
				border-color: #fff7d6;
				background: #fff7d6;
			}
		</style>
	@endpush
@endonce

<div x-data="kingSearchBar(@js($searchPayload))" x-init="init()" class="relative z-50" style="z-index: 95;">
	<form x-ref="form" action="{{ $action }}" method="GET" @submit.prevent="submitForm"
		class="rounded-2xl border border-amber-100 bg-white/95 p-3 shadow-soft backdrop-blur sm:p-4">
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
						class="group flex h-14 w-full items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 text-left transition hover:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-600/35"
						:class="activeDropdown === 'origin' ? 'border-primary-600 ring-2 ring-primary-600/20' : ''"
						aria-haspopup="listbox" :aria-expanded="activeDropdown === 'origin'">
						<img src="{{ asset('/client/icons/pickup.svg') }}" alt="{{ __('client.search.origin_icon_alt') }}" class="h-5 w-5 shrink-0">
						<div class="min-w-0 flex-1">
							<p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400" x-text="translations.originLabel"></p>
							<p class="truncate text-sm font-bold text-slate-700"
								x-text="origin.name || '{{ __('client.search.origin_placeholder') }}'"></p>
						</div>
						<i class="fa-solid fa-chevron-down text-xs text-slate-400 transition" :class="activeDropdown === 'origin' ? 'rotate-180 text-primary-600' : ''"></i>
					</button>

					<div x-show="activeDropdown === 'origin'" x-cloak x-transition class="absolute left-0 right-0 top-[calc(100%+8px)] z-50" style="z-index: 140;">
						<div class="animate-ksb-slide-fade overflow-hidden rounded-2xl border border-amber-100 bg-white shadow-soft">
							<div class="border-b border-amber-100 p-3">
								<label class="relative block">
									<i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
									<input x-ref="originSearch" x-model="queries.origin" type="text" autocomplete="off"
										class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 pl-9 pr-3 text-sm outline-none transition focus:border-primary-600 focus:bg-white"
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
						class="group flex h-14 w-full items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 text-left transition hover:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-600/35"
						:class="activeDropdown === 'destination' ? 'border-primary-600 ring-2 ring-primary-600/20' : ''"
						aria-haspopup="listbox" :aria-expanded="activeDropdown === 'destination'">
						<img src="{{ asset('/client/icons/dropoff.svg') }}" alt="{{ __('client.search.destination_icon_alt') }}" class="h-5 w-5 shrink-0">
						<div class="min-w-0 flex-1">
							<p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400" x-text="translations.destinationLabel"></p>
							<p class="truncate text-sm font-bold text-slate-700"
								x-text="destination.name || '{{ __('client.search.destination_placeholder') }}'"></p>
						</div>
						<i class="fa-solid fa-chevron-down text-xs text-slate-400 transition" :class="activeDropdown === 'destination' ? 'rotate-180 text-primary-600' : ''"></i>
					</button>

					<div x-show="activeDropdown === 'destination'" x-cloak x-transition class="absolute left-0 right-0 top-[calc(100%+8px)] z-50" style="z-index: 140;">
						<div class="animate-ksb-slide-fade overflow-hidden rounded-2xl border border-amber-100 bg-white shadow-soft">
							<div class="border-b border-amber-100 p-3">
								<label class="relative block">
									<i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
									<input x-ref="destinationSearch" x-model="queries.destination" type="text" autocomplete="off"
										class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 pl-9 pr-3 text-sm outline-none transition focus:border-primary-600 focus:bg-white"
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

			<div class="relative">
				<button type="button" @click="openDeparturePicker"
					class="group flex h-14 w-full items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 text-left transition hover:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-600/35">
					<img src="{{ asset('/client/icons/date.svg') }}" alt="{{ __('client.search.departure_icon_alt') }}" class="h-5 w-5 shrink-0">
					<div class="min-w-0 flex-1">
						<p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400" x-text="translations.departureLabel"></p>
						<p class="truncate text-sm font-bold" :class="departureDate ? 'text-slate-700' : 'text-slate-400'" x-text="departureDate || translations.datePlaceholder"></p>
					</div>
				</button>
				<input x-ref="departureInput" type="text" class="pointer-events-none absolute h-0 w-0 opacity-0" aria-hidden="true" tabindex="-1">
			</div>

			<div class="relative">
				<template x-if="showReturn">
					<div class="relative">
						<button type="button" @click="openReturnPicker"
							class="group flex h-14 w-full items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 pr-10 text-left transition hover:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-600/35">
							<img src="{{ asset('/client/icons/date.svg') }}" alt="{{ __('client.search.return_icon_alt') }}" class="h-5 w-5 shrink-0">
							<div class="min-w-0 flex-1">
								<p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400" x-text="translations.returnLabel"></p>
								<p class="truncate text-sm font-bold" :class="returnDate ? 'text-slate-700' : 'text-slate-400'" x-text="returnDate || translations.datePlaceholder"></p>
							</div>
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
				<button type="submit" x-ref="submitButton"
					class="h-14 min-w-36 rounded-xl bg-primary-600 px-6 text-sm font-bold text-white shadow-soft transition hover:bg-primary-700 active:scale-95">
					{{ $submitLabel ?? __('client.search.submit') }}
				</button>
			</div>
		</div>

		<p x-show="errorMessage" x-text="errorMessage" class="mt-3 rounded-xl border border-rose-100 bg-rose-50 px-3 py-2 text-sm text-rose-600"></p>
	</form>
</div>

@once
	@push('scripts')
		<script>
			if (!window.kingSearchBar) {
				window.kingSearchBar = function (payload) {
					return {
						locations: (payload.locations || []).map((item) => ({
							id: Number(item.id),
							type: item.type || 'province',
							name: item.name || '',
							context: item.context || item.address || '',
							typeLabel: item.type_label || ((payload.translations || {}).types || {})[item.type] || '',
							normalized: '',
						})),
						translations: payload.translations || {},
						defaults: payload.defaults || {},
						activeDropdown: null,
						queries: { origin: '', destination: '' },
						origin: { id: '', type: '', name: '', context: '', typeLabel: '' },
						destination: { id: '', type: '', name: '', context: '', typeLabel: '' },
						departureDate: '',
						returnDate: '',
						showReturn: false,
						errorMessage: '',
						departurePicker: null,
						returnPicker: null,

						init() {
							this.locations = this.locations.map((item) => {
								item.normalized = this.normalize([item.name, item.context].join(' '));
								return item;
							});

							this.origin = this.resolveDefault(this.defaults.origin);
							this.destination = this.resolveDefault(this.defaults.destination);

							if (!this.origin.id && this.locations.length > 0) {
								this.origin = { ...this.locations[0] };
							}

							if (!this.destination.id && this.locations.length > 1) {
								this.destination = { ...this.locations[1] };
							}

							this.departureDate = this.defaults.departure_date || this.todayDate();
							this.returnDate = this.defaults.return_date || '';
							this.showReturn = !!this.returnDate;

							this.$nextTick(() => {
								this.mountDeparturePicker();
								if (this.showReturn) {
									this.mountReturnPicker();
								}
							});

							window.addEventListener('pageshow', () => {
								const submitButton = this.$refs.submitButton;
								if (submitButton) {
									submitButton.disabled = false;
									submitButton.textContent = @js($submitLabel ?? __('client.search.submit'));
								}
							});
						},

						todayDate() {
							const now = new Date();
							const day = String(now.getDate()).padStart(2, '0');
							const month = String(now.getMonth() + 1).padStart(2, '0');
							const year = now.getFullYear();
							return `${day}/${month}/${year}`;
						},

						normalize(value) {
							return (value || '')
								.toString()
								.normalize('NFD')
								.replace(/[\u0300-\u036f]/g, '')
								.replace(/đ/g, 'd')
								.replace(/Đ/g, 'D')
								.toLowerCase()
								.trim();
						},

						resolveDefault(defaultValue) {
							if (!defaultValue || !defaultValue.id || !defaultValue.type) {
								return { id: '', type: '', name: '', context: '', typeLabel: '' };
							}

							const found = this.locations.find((item) => Number(item.id) === Number(defaultValue.id) && item.type === defaultValue.type);
							if (found) {
								return { ...found };
							}

							return {
								id: Number(defaultValue.id),
								type: defaultValue.type,
								name: defaultValue.name || '',
								context: defaultValue.context || '',
								typeLabel: defaultValue.type_label || ((this.translations.types || {})[defaultValue.type] || ''),
							};
						},

						groupedLocations(field) {
							const query = this.normalize(this.queries[field]);
							const filtered = !query
								? this.locations.slice(0, 24)
								: this.locations.filter((item) => item.normalized.includes(query)).slice(0, 24);

							const orderedTypes = ['province', 'district', 'stop'];
							return orderedTypes
								.map((type) => ({
									type,
									label: (this.translations.types || {})[type] || type,
									items: filtered.filter((item) => item.type === type),
								}))
								.filter((group) => group.items.length > 0);
						},

						isSelected(field, item) {
							const selected = this[field];
							return Number(selected.id) === Number(item.id) && selected.type === item.type;
						},

						toggleDropdown(field) {
							this.activeDropdown = this.activeDropdown === field ? null : field;
							this.queries[field] = '';
							this.errorMessage = '';

							this.$nextTick(() => {
								if (field === 'origin' && this.$refs.originSearch) {
									this.$refs.originSearch.focus();
								}
								if (field === 'destination' && this.$refs.destinationSearch) {
									this.$refs.destinationSearch.focus();
								}
							});
						},

						selectLocation(field, item) {
							this[field] = { ...item };
							this.activeDropdown = null;
							this.errorMessage = '';
						},

						swapLocations() {
							const oldOrigin = { ...this.origin };
							this.origin = { ...this.destination };
							this.destination = oldOrigin;
							this.errorMessage = '';
						},

						parseDate(value) {
							const parts = (value || '').split('/');
							if (parts.length !== 3) {
								return null;
							}

							const day = Number(parts[0]);
							const month = Number(parts[1]) - 1;
							const year = Number(parts[2]);
							return new Date(year, month, day);
						},

						mountDeparturePicker() {
							if (typeof flatpickr !== 'function' || !this.$refs.departureInput) {
								return;
							}

							this.departurePicker = flatpickr(this.$refs.departureInput, {
								dateFormat: 'd/m/Y',
								minDate: 'today',
								defaultDate: this.departureDate,
								onChange: (_dates, dateStr) => {
									this.departureDate = dateStr;
									this.errorMessage = '';

									if (this.returnPicker) {
										this.returnPicker.set('minDate', dateStr);
									}

									if (this.returnDate) {
										const returnDate = this.parseDate(this.returnDate);
										const departureDate = this.parseDate(dateStr);
										if (returnDate && departureDate && returnDate < departureDate) {
											this.returnDate = '';
										}
									}
								},
							});
						},

						openDeparturePicker() {
							if (this.departurePicker) {
								this.departurePicker.open();
							}
						},

						mountReturnPicker() {
							if (typeof flatpickr !== 'function' || !this.$refs.returnInput) {
								return;
							}

							if (this.returnPicker) {
								this.returnPicker.destroy();
							}

							this.returnPicker = flatpickr(this.$refs.returnInput, {
								dateFormat: 'd/m/Y',
								minDate: this.departureDate || 'today',
								defaultDate: this.returnDate || null,
								onChange: (_dates, dateStr) => {
									this.returnDate = dateStr;
									this.errorMessage = '';
								},
							});
						},

						enableReturnDate() {
							this.showReturn = true;
							this.$nextTick(() => {
								this.mountReturnPicker();
								this.openReturnPicker();
							});
						},

						openReturnPicker() {
							if (!this.showReturn) {
								return;
							}

							if (!this.returnPicker) {
								this.mountReturnPicker();
							}

							if (this.returnPicker) {
								this.returnPicker.open();
							}
						},

						removeReturnDate() {
							this.returnDate = '';
							this.showReturn = false;
							if (this.returnPicker) {
								this.returnPicker.destroy();
								this.returnPicker = null;
							}
						},

						submitForm() {
							this.errorMessage = '';

							if (!this.origin.id || !this.destination.id) {
								this.errorMessage = this.translations.validationSelect;
								return;
							}

							if (this.origin.id === this.destination.id && this.origin.type === this.destination.type) {
								this.errorMessage = this.translations.validationDifferent;
								return;
							}

							if (!this.departureDate) {
								this.errorMessage = this.translations.datePlaceholder;
								return;
							}

							const submitButton = this.$refs.submitButton;
							if (submitButton) {
								submitButton.disabled = true;
								submitButton.textContent = this.translations.loading;
							}

							this.$refs.form.submit();
						},
					};
				};
			}
		</script>
	@endpush
@endonce
