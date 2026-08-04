@extends('admin.layouts.app')

@section('title', $district ? 'Sửa địa điểm' : 'Thêm địa điểm')

@section('content')
<div
     x-data="{
         slugEdited: {{ $district ? 'true' : 'false' }},
         toSlug(str) {
             const map = {
                 'à':'a','á':'a','ạ':'a','ả':'a','ã':'a','â':'a','ầ':'a','ấ':'a','ậ':'a','ẩ':'a','ẫ':'a','ă':'a','ằ':'a','ắ':'a','ặ':'a','ẳ':'a','ẵ':'a',
                 'è':'e','é':'e','ẹ':'e','ẻ':'e','ẽ':'e','ê':'e','ề':'e','ế':'e','ệ':'e','ể':'e','ễ':'e',
                 'ì':'i','í':'i','ị':'i','ỉ':'i','ĩ':'i',
                 'ò':'o','ó':'o','ọ':'o','ỏ':'o','õ':'o','ô':'o','ồ':'o','ố':'o','ộ':'o','ổ':'o','ỗ':'o','ơ':'o','ờ':'o','ớ':'o','ợ':'o','ở':'o','ỡ':'o',
                 'ù':'u','ú':'u','ụ':'u','ủ':'u','ũ':'u','ư':'u','ừ':'u','ứ':'u','ự':'u','ử':'u','ữ':'u',
                 'ỳ':'y','ý':'y','ỵ':'y','ỷ':'y','ỹ':'y',
                 'đ':'d'
             };
             return str.toLowerCase()
                 .replace(/[^a-z0-9\s-]/g, c => map[c] || '')
                 .replace(/\s+/g, '-')
                 .replace(/-+/g, '-')
                 .replace(/^-|-$/g, '');
         }
     }">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-5">
        <a href="{{ route('admin.locations.index', ['section' => 'districts']) }}" class="hover:text-gray-700">Địa điểm</a>
        <span>/</span>
        <span class="text-gray-700">{{ $district ? 'Sửa địa điểm' : 'Thêm địa điểm' }}</span>
    </nav>

    <h1 class="text-lg font-semibold text-gray-900 mb-6">{{ $district ? 'Sửa: ' . $district->name : 'Thêm địa điểm' }}</h1>

    <form method="POST"
          action="{{ $district ? route('admin.locations.districts.update', $district) : route('admin.locations.districts.store') }}"
          class="space-y-6">
        @csrf
        @if($district) @method('PUT') @endif

        <x-admin::form.section title="Thông tin chung">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-admin::form.select-search
                    name="province_id"
                    label="Tỉnh/thành"
                    source="provinces"
                    :value="old('province_id', $district?->province_id)"
                    :valueText="$district?->province?->name"
                    required
                />
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Loại địa điểm <span class="text-red-500 ml-0.5">*</span>
                    </label>
                    <select name="district_type_id" required
                            class="block w-full rounded border {{ $errors->has('district_type_id') ? 'border-red-400' : 'border-gray-300' }} py-2 px-3 text-sm focus:outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="">— Chọn loại —</option>
                        @foreach($districtTypes as $typeId => $typeName)
                        <option value="{{ $typeId }}" @selected(old('district_type_id', $district?->district_type_id) == $typeId)>{{ $typeName }}</option>
                        @endforeach
                    </select>
                    <x-admin::form.field-error name="district_type_id" />
                </div>
                <div>
                    <x-admin::form.input
                        name="name"
                        label="Tên địa điểm"
                        :value="old('name', $district?->name)"
                        required
                        x-on:blur="if (!slugEdited) { $refs.slugInput.value = toSlug($event.target.value) }"
                    />
                </div>
                <div>
                    <x-admin::form.input
                        name="slug"
                        label="Đường dẫn (slug)"
                        :value="old('slug', $district?->slug)"
                        required
                        x-ref="slugInput"
                        x-on:input="slugEdited = $event.target.value !== ''"
                    />
                </div>
                <div>
                    <x-admin::form.input
                        name="title"
                        label="Tiêu đề SEO"
                        :value="old('title', $district?->title)"
                    />
                </div>
                <div>
                    <x-admin::form.input
                        name="priority"
                        label="Độ ưu tiên"
                        type="number"
                        :value="old('priority', $district?->priority ?? 0)"
                        required
                    />
                </div>
                <div class="md:col-span-2">
                    <x-admin::form.input
                        name="description"
                        label="Mô tả SEO"
                        :value="old('description', $district?->description)"
                    />
                </div>
            </div>
        </x-admin::form.section>

        <x-admin::form.section title="Hình ảnh">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-admin::form.upload
                    name="thumbnail_url"
                    label="Ảnh đại diện"
                    :value="old('thumbnail_url', $district?->thumbnail_url)"
                />
                <x-admin::form.upload
                    name="image_list_url"
                    label="Album ảnh"
                    :value="old('image_list_url', $district?->image_list_url ?? [])"
                    :multiple="true"
                />
            </div>
        </x-admin::form.section>

        <x-admin::form.section title="Nội dung">
            <x-admin::form.rich-text
                name="content"
                label="Nội dung chi tiết"
                :value="old('content', $district?->content)"
            />
        </x-admin::form.section>

        <div class="flex items-center gap-4 pb-6">
            <button type="submit"
                    class="px-6 py-2.5 bg-brand-600 text-white text-sm font-medium rounded hover:bg-brand-700 transition-colors">
                {{ $district ? 'Lưu thay đổi' : 'Thêm địa điểm' }}
            </button>
            <a href="{{ route('admin.locations.index', ['section' => 'districts']) }}"
               class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded hover:bg-gray-50 transition-colors">
                Hủy
            </a>
        </div>
    </form>
</div>
@endsection
