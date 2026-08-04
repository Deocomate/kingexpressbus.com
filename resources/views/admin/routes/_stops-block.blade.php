{{--
    Route stops block — independent fetch CRUD, no form submission with parent route form.
    Only rendered on edit page. Drag&drop reorder (Shopify Draggable, via window.ShopifyDraggable
    set by public/js/admin/sortable.js) posts new priority order to reorderUrl.
--}}
@php
    $stops = $route->routeStops->map(fn ($rs) => [
        'id'              => $rs->id,
        'stop_id'         => $rs->stop_id,
        'stop_name'       => $rs->stop?->name ?? '',
        'stop_type'       => $rs->stop_type,
        'stop_type_label' => match($rs->stop_type) {
            'pickup'  => 'Đón',
            'dropoff' => 'Trả',
            default   => 'Đón và trả',
        },
        'priority'        => $rs->priority,
    ])->values()->all();
@endphp

<div
    x-data="routeStopsBlock({
        routeId: {{ $route->id }},
        reorderUrl: '{{ route('admin.routes.stops.reorder', $route->id) }}',
        storeUrl: '{{ route('admin.routes.stops.store', $route->id) }}',
        updateUrl: (id) => `{{ url('quan-tri/tuyen-duong/' . $route->id . '/diem-dung') }}/${id}`,
        destroyUrl: (id) => `{{ url('quan-tri/tuyen-duong/' . $route->id . '/diem-dung') }}/${id}`,
        csrfToken: '{{ csrf_token() }}',
        initialStops: {{ Js::from($stops) }},
        optionsUrl: '{{ route('admin.api.options', 'stops') }}',
    })"
    class="mt-6"
>
    <div class="flex items-center justify-between mb-3">
        <div>
            <h2 class="text-base font-semibold text-gray-900">Điểm dừng tuyến</h2>
            <p class="text-xs text-gray-500 mt-0.5">Kéo để sắp xếp thứ tự. Thay đổi không ảnh hưởng form tuyến đường bên trên.</p>
        </div>
        <button
            type="button"
            @click="openAdd()"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white bg-amber-500 hover:bg-amber-600 rounded-md transition-colors"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Thêm điểm dừng
        </button>
    </div>

    {{-- Error message --}}
    <p x-show="errorMsg" x-text="errorMsg" class="text-sm text-red-600 mb-2" x-cloak></p>

    {{-- Stops table --}}
    <div class="bg-white rounded border border-gray-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2.5 w-8 text-gray-400"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg></th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase">Điểm dừng</th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase">Loại</th>
                    <th class="px-4 py-2.5 text-right text-xs font-medium text-gray-500 uppercase">Ưu tiên</th>
                    <th class="px-4 py-2.5 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                </tr>
            </thead>
            <tbody id="stops-sortable" class="divide-y divide-gray-100">
                <template x-if="stops.length === 0">
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm">Chưa có điểm dừng nào. Thêm điểm dừng để sắp xếp hành trình.</td></tr>
                </template>
                <template x-for="stop in stops" :key="stop.id">
                    <tr class="hover:bg-gray-50 transition-colors" :data-id="stop.id" :data-sortable-id="stop.id">
                        <td class="px-4 py-2.5 text-gray-400 cursor-grab active:cursor-grabbing" title="Kéo để sắp xếp" data-drag-handle>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                        </td>
                        <td class="px-4 py-2.5 font-medium text-gray-900" x-text="stop.stop_name"></td>
                        <td class="px-4 py-2.5">
                            <span
                                class="inline-flex items-center font-medium rounded-full text-xs px-2 py-1"
                                :class="{
                                    'bg-green-100 text-green-800': stop.stop_type === 'pickup',
                                    'bg-red-100 text-red-800': stop.stop_type === 'dropoff',
                                    'bg-blue-100 text-blue-800': stop.stop_type === 'both',
                                }"
                                x-text="stop.stop_type_label"
                            ></span>
                        </td>
                        <td class="px-4 py-2.5 text-right text-gray-500 text-xs" data-priority-value x-text="stop.priority"></td>
                        <td class="px-4 py-2.5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" @click="openEdit(stop)" class="text-xs px-2 py-1 rounded border border-gray-300 text-gray-600 hover:bg-gray-50 transition-colors">Sửa</button>
                                <button type="button" @click="deleteStop(stop.id)" class="text-xs px-2 py-1 rounded border border-red-300 text-red-600 hover:bg-red-50 transition-colors">Xóa</button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    {{-- Slide-over: Add/Edit stop --}}
    <div
        x-show="slideOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click.self="slideOpen = false"
        class="fixed inset-0 bg-black/30 z-40"
        x-cloak
    ></div>

    <div class="fixed inset-y-0 right-0 z-50 w-full max-w-md flex flex-col bg-white shadow-xl transition-transform duration-300"
        :class="slideOpen ? 'translate-x-0' : 'translate-x-full'"
        x-cloak
    >
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-900" x-text="editingStop ? 'Sửa điểm dừng' : 'Thêm điểm dừng'"></h3>
            <button type="button" @click="slideOpen = false" class="rounded p-1 text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto px-6 py-5 space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Điểm dừng <span class="text-red-500">*</span></label>
                <select
                    x-ref="stopIdSelect"
                    data-select-search
                    :data-source="optionsUrl"
                    class="block w-full text-sm"
                ></select>
                <p x-show="formError.stop_id" x-text="formError.stop_id" class="mt-1 text-xs text-red-600" x-cloak></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Loại điểm dừng <span class="text-red-500">*</span></label>
                <select x-model="form.stop_type" class="block w-full rounded border border-gray-300 py-2 px-3 text-sm">
                    <option value="pickup">Đón</option>
                    <option value="dropoff">Trả</option>
                    <option value="both">Đón và trả</option>
                </select>
                <p x-show="formError.stop_type" x-text="formError.stop_type" class="mt-1 text-xs text-red-600" x-cloak></p>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex items-center gap-3">
            <button
                type="button"
                @click="saveStop()"
                :disabled="saving"
                class="flex-1 px-4 py-2 text-sm font-medium text-white bg-amber-500 hover:bg-amber-600 rounded-md transition-colors disabled:opacity-50"
                x-text="saving ? 'Đang lưu...' : (editingStop ? 'Lưu thay đổi' : 'Thêm điểm dừng')"
            ></button>
            <button type="button" @click="slideOpen = false" class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-md hover:bg-gray-50 transition-colors">Hủy</button>
        </div>
    </div>
</div>

<script>
function routeStopsBlock(config) {
    return {
        stops: config.initialStops,
        slideOpen: false,
        editingStop: null,
        form: { stop_id: '', stop_type: 'both' },
        formError: {},
        errorMsg: '',
        saving: false,
        optionsUrl: config.optionsUrl,
        sortableInstance: null,

        init() {
            this.$nextTick(() => this.initSortable());
        },

        initSortable() {
            var el = document.getElementById('stops-sortable');
            var SortableCtor = window.ShopifyDraggable && window.ShopifyDraggable.Sortable;
            if (!el || !SortableCtor) return;
            if (this.sortableInstance) this.sortableInstance.destroy();
            this.sortableInstance = new SortableCtor([el], {
                draggable: 'tr[data-id]',
                handle: '[data-drag-handle]',
                mirror: { appendTo: 'body', constrainDimensions: true },
            });
            // Rows have @click actions (Alpine) — keep the <body>-appended mirror
            // clone out of Alpine's mutation observer so it doesn't try to init
            // those directives outside their x-data scope.
            this.sortableInstance.on('mirror:created', ({ mirror }) => mirror.setAttribute('x-ignore', ''));
            this.sortableInstance.on('sortable:stop', () => this.onReorder(el));
        },

        onReorder(el) {
            var ids = Array.from(el.querySelectorAll('tr[data-id]')).map(r => r.dataset.id);
            var total = ids.length;
            fetch(config.reorderUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': config.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ ids }),
            }).then(r => {
                if (!r.ok) throw new Error('reorder failed');
                return r.json();
            }).then(data => {
                if (!data.ok) {
                    this.errorMsg = data.message || 'Lỗi khi sắp xếp.';
                    return;
                }
                this.errorMsg = '';
                // Keep Alpine state in sync with DOM so a later re-render
                // does not snap rows back to the pre-drag order.
                var byId = Object.fromEntries(this.stops.map(s => [String(s.id), s]));
                this.stops = ids.map((id, index) => {
                    var stop = byId[String(id)];
                    if (stop) stop.priority = total - index;
                    return stop;
                }).filter(Boolean);
            }).catch(() => {
                this.errorMsg = 'Không lưu được thứ tự. Tải lại trang và thử lại.';
            });
        },

        openAdd() {
            this.editingStop = null;
            this.form = { stop_id: '', stop_type: 'both' };
            this.formError = {};
            this.slideOpen = true;
            this.$nextTick(() => this.resetSelect(null));
        },

        openEdit(stop) {
            this.editingStop = stop;
            this.form = { stop_id: stop.stop_id, stop_type: stop.stop_type };
            this.formError = {};
            this.slideOpen = true;
            this.$nextTick(() => this.resetSelect(stop));
        },

        resetSelect(stop) {
            var sel = this.$refs.stopIdSelect;
            if (!sel) return;
            // Reinitialize Choices/TomSelect or just set value if select-search is already initialized
            if (sel._tomSelectInstance) {
                sel._tomSelectInstance.clear(true);
                if (stop) {
                    sel._tomSelectInstance.addItem(stop.stop_id, true);
                    if (!sel._tomSelectInstance.options[stop.stop_id]) {
                        sel._tomSelectInstance.addOption({ value: stop.stop_id, text: stop.stop_name });
                    }
                }
            }
        },

        saveStop() {
            var sel = this.$refs.stopIdSelect;
            var stopId = sel?._tomSelectInstance ? (sel._tomSelectInstance.getValue() || '') : this.form.stop_id;
            this.form.stop_id = stopId;
            this.formError = {};

            if (!this.form.stop_id) {
                this.formError.stop_id = 'Vui lòng chọn điểm dừng.';
                return;
            }

            this.saving = true;
            var isEdit = !!this.editingStop;
            var url    = isEdit ? config.updateUrl(this.editingStop.id) : config.storeUrl;
            var method = isEdit ? 'PUT' : 'POST';

            fetch(url, {
                method,
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrfToken},
                body: JSON.stringify(this.form),
            }).then(r => r.json()).then(data => {
                this.saving = false;
                if (!data.ok) {
                    this.errorMsg = data.message || 'Lỗi khi lưu.';
                    return;
                }
                if (isEdit) {
                    var idx = this.stops.findIndex(s => s.id === data.stop.id);
                    if (idx !== -1) this.stops.splice(idx, 1, data.stop);
                } else {
                    this.stops.push(data.stop);
                    this.$nextTick(() => this.initSortable());
                }
                this.slideOpen = false;
                this.errorMsg = '';
            }).catch(() => {
                this.saving = false;
                this.errorMsg = 'Lỗi kết nối. Vui lòng thử lại.';
            });
        },

        deleteStop(id) {
            if (!confirm('Xóa điểm dừng này?')) return;
            fetch(config.destroyUrl(id), {
                method: 'DELETE',
                headers: {'X-CSRF-TOKEN': config.csrfToken},
            }).then(r => r.json()).then(data => {
                if (data.ok) this.stops = this.stops.filter(s => s.id !== id);
                else this.errorMsg = data.message || 'Lỗi khi xóa.';
            });
        },
    };
}
</script>
