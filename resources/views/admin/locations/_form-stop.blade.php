{{-- Create slide-over --}}
<x-admin::display.slide-over id="stop-create-over" title="Thêm điểm dừng">
    <form method="POST" action="{{ route('admin.locations.stops.store') }}" class="space-y-5">
        @csrf
        <x-admin::form.select-search
            name="district_id"
            label="Địa điểm"
            source="districts"
            :value="old('district_id')"
            required
        />
        <x-admin::form.input
            name="name"
            label="Tên điểm dừng"
            :value="old('name')"
            required
        />
        <x-admin::form.input
            name="address"
            label="Địa chỉ"
            :value="old('address')"
            required
        />
        <x-admin::form.input
            name="priority"
            label="Độ ưu tiên"
            type="number"
            :value="old('priority', 0)"
            required
        />
        <div class="flex gap-3 pt-2">
            <button type="submit" class="flex-1 px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded hover:bg-brand-700 transition-colors">Thêm</button>
            <button type="button" @click="open = false" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded hover:bg-gray-50 transition-colors">Hủy</button>
        </div>
    </form>
</x-admin::display.slide-over>

@if($errors->isNotEmpty() && old('_section') === 'stops' && request()->routeIs('admin.locations.index'))
<script>
document.addEventListener('alpine:init', () => {
    setTimeout(() => window.dispatchEvent(new CustomEvent('open-slide-over', { detail: { id: 'stop-create-over' } })), 50);
});
</script>
@endif
