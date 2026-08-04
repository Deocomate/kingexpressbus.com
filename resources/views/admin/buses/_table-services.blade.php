{{-- Bus services table sub-view --}}
<div
    class="space-y-4"
    x-data="busServicesSection({
        storeUrl: '{{ route('admin.buses.services.store') }}',
        updateUrl: (id) => `{{ url('quan-tri/doi-xe/dich-vu') }}/${id}`,
        destroyUrl: (id) => `{{ url('quan-tri/doi-xe/dich-vu') }}/${id}`,
        csrfToken: '{{ csrf_token() }}',
    })"
>
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Dịch vụ xe</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $servicesPaginator->total() }} dịch vụ</p>
        </div>
        <button type="button" @click="openAdd()" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-amber-500 hover:bg-amber-600 rounded-md transition-colors shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Thêm dịch vụ
        </button>
    </div>

    <p x-show="errorMsg" x-text="errorMsg" class="text-sm text-red-600" x-cloak></p>

    <x-admin::table.table>
        @include('admin.buses._table-services-rows', compact('servicesPaginator'))
    </x-admin::table.table>

    {{-- Slide-over: Add/Edit service --}}
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
            <h3 class="text-base font-semibold text-gray-900" x-text="editing ? 'Sửa dịch vụ' : 'Thêm dịch vụ'"></h3>
            <button type="button" @click="slideOpen = false" class="rounded p-1 text-gray-400 hover:text-gray-600 hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto px-6 py-5 space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tên dịch vụ <span class="text-red-500">*</span></label>
                <input type="text" x-model="form.name" class="block w-full rounded border border-gray-300 py-2 px-3 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="VD: Wifi, Điều hòa, Máy lạnh">
                <p x-show="formError.name" x-text="formError.name" class="mt-1 text-xs text-red-600" x-cloak></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Icon (Heroicons / SVG class)</label>
                <input type="text" x-model="form.icon" class="block w-full rounded border border-gray-300 py-2 px-3 text-sm font-mono focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="VD: wifi, air-conditioner">
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex items-center gap-3">
            <button type="button" @click="save()" :disabled="saving"
                class="flex-1 px-4 py-2 text-sm font-medium text-white bg-amber-500 hover:bg-amber-600 rounded-md transition-colors disabled:opacity-50"
                x-text="saving ? 'Đang lưu...' : (editing ? 'Lưu thay đổi' : 'Thêm dịch vụ')"></button>
            <button type="button" @click="slideOpen = false" class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-md hover:bg-gray-50">Hủy</button>
        </div>
    </div>
</div>

<script>
function busServicesSection(config) {
    return {
        slideOpen: false,
        editing: null,
        form: { name: '', icon: '' },
        formError: {},
        errorMsg: '',
        saving: false,

        openAdd() {
            this.editing = null;
            this.form = { name: '', icon: '' };
            this.formError = {};
            this.slideOpen = true;
        },

        openEdit(id, name, icon) {
            this.editing = id;
            this.form = { name: name || '', icon: icon || '' };
            this.formError = {};
            this.slideOpen = true;
        },

        save() {
            this.formError = {};
            if (!this.form.name.trim()) {
                this.formError.name = 'Vui lòng nhập tên dịch vụ.';
                return;
            }
            this.saving = true;
            var isEdit = !!this.editing;
            var url    = isEdit ? config.updateUrl(this.editing) : config.storeUrl;
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
                // Reload page to reflect changes
                window.location.reload();
            }).catch(() => {
                this.saving = false;
                this.errorMsg = 'Lỗi kết nối.';
            });
        },

        deleteService(id, name) {
            if (!confirm(`Xóa dịch vụ "${name}"?`)) return;
            fetch(config.destroyUrl(id), {
                method: 'DELETE',
                headers: {'X-CSRF-TOKEN': config.csrfToken},
            }).then(r => r.json()).then(data => {
                if (data.ok) window.location.reload();
                else this.errorMsg = data.message || 'Lỗi khi xóa.';
            });
        },
    };
}
</script>
