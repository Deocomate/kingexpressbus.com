{{--
  Unified menu create/edit form inside a slide-over.
  Uses Alpine to toggle create vs edit state and populate fields.
  Parent page initialises window.setEditMenu(data) which sets Alpine state.
--}}
<div
    x-data="{
        editing: false,
        action:  '{{ route('admin.website.menus.store') }}',
        menuId:  null,
        type:    'custom_link',

        fill(data) {
            this.editing = true;
            this.menuId  = data.id;
            this.action  = '/quan-tri/cau-hinh-website/menus/' + data.id;
            this.type    = data.type || 'custom_link';
            this.$nextTick(() => {
                const form = this.$refs.form;
                if (!form) return;
                ['name','url','related_id','parent_id','priority'].forEach(f => {
                    const el = form.elements[f];
                    if (el) el.value = (data[f] !== null && data[f] !== undefined) ? data[f] : '';
                });
            });
        },

        reset() {
            this.editing = false;
            this.action  = '{{ route('admin.website.menus.store') }}';
            this.menuId  = null;
            this.type    = 'custom_link';
            this.$refs.form?.reset();
        },
    }"
    x-init="
        window.setEditMenu = (data) => fill(data);
        $watch('editing', val => !val && $refs.form?.reset());
    "
>
    <p x-show="editing" class="mb-4 text-sm text-amber-700 bg-amber-50 rounded px-3 py-2">
        Đang sửa menu. <button type="button" @click="reset()" class="underline">Chuyển sang thêm mới</button>
    </p>

    <form method="POST" :action="action" x-ref="form" novalidate>
        @csrf
        <input type="hidden" name="_method" x-bind:value="editing ? 'PUT' : 'POST'">

        {{-- Validation errors --}}
        @if($errors->any())
        <div class="mb-4 rounded bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="space-y-4">
            {{-- Name --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tên menu <span class="text-red-500">*</span></label>
                <input type="text" name="name" required maxlength="1000"
                       class="block w-full rounded border border-gray-300 py-2 px-3 text-sm focus:outline-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500 {{ $errors->has('name') ? 'border-red-400 bg-red-50' : '' }}">
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Type --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Loại menu <span class="text-red-500">*</span></label>
                <div class="flex flex-wrap gap-3">
                    @foreach(['custom_link' => '🔗 Liên kết', 'route' => '🚌 Tuyến đường', 'page' => '📄 Trang tĩnh', 'system_page' => '⚙️ Trang hệ thống'] as $val => $lbl)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="type" value="{{ $val }}"
                               x-model="type"
                               class="h-4 w-4 text-brand-500 border-gray-300 focus:ring-brand-500">
                        <span class="text-sm text-gray-700">{{ $lbl }}</span>
                    </label>
                    @endforeach
                </div>
                @error('type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- URL (visible when type ≠ route) --}}
            <div x-show="type !== 'route'" x-cloak>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <span x-text="type === 'page' ? 'Đường dẫn trang (Slug)' : (type === 'system_page' ? 'Đường dẫn hệ thống' : 'Đường dẫn (URL)')"></span>
                    <span class="text-red-500">*</span>
                </label>
                <input type="text" name="url" maxlength="1000"
                       :placeholder="type === 'page' ? 'vd: gioi-thieu' : (type === 'system_page' ? 'vd: /dat-ve' : 'https://...')"
                       class="block w-full rounded border border-gray-300 py-2 px-3 text-sm focus:outline-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500 {{ $errors->has('url') ? 'border-red-400 bg-red-50' : '' }}">
                @error('url')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Route select (visible when type = route) --}}
            <div x-show="type === 'route'" x-cloak>
                <label class="block text-sm font-medium text-gray-700 mb-1">Chọn tuyến đường <span class="text-red-500">*</span></label>
                <select name="related_id"
                        class="block w-full rounded border border-gray-300 py-2 px-3 text-sm focus:outline-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500 {{ $errors->has('related_id') ? 'border-red-400 bg-red-50' : '' }}">
                    <option value="">— Chọn tuyến đường —</option>
                    @foreach($routeOptions as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('related_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Parent select --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Menu cha</label>
                <select name="parent_id"
                        class="block w-full rounded border border-gray-300 py-2 px-3 text-sm focus:outline-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500">
                    <option value="{{ \App\Models\Menu::ROOT_PARENT_ID }}">— Cấp gốc —</option>
                    @foreach($parentOptions as $id => $label)
                    <option value="{{ $id }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('parent_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button type="button"
                    @click="reset(); $dispatch('close-slide-over', { id: 'menu-form-slide-over' })"
                    class="rounded border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                Hủy
            </button>
            <button type="submit"
                    class="rounded bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700 transition-colors"
                    x-text="editing ? 'Lưu thay đổi' : 'Thêm menu'">
            </button>
        </div>
    </form>
</div>
