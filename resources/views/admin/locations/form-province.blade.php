@extends('admin.layouts.app')

@section('title', $province ? 'Sửa tỉnh/thành phố' : 'Thêm tỉnh/thành phố')

@section('content')
<div
     x-data="{
         slugEdited: {{ $province ? 'true' : 'false' }},
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
        <a href="{{ route('admin.locations.index', ['section' => 'provinces']) }}" class="hover:text-gray-700">Địa điểm</a>
        <span>/</span>
        <span class="text-gray-700">{{ $province ? 'Sửa tỉnh/thành' : 'Thêm tỉnh/thành' }}</span>
    </nav>

    <h1 class="text-lg font-semibold text-gray-900 mb-6">{{ $province ? 'Sửa: ' . $province->name : 'Thêm tỉnh/thành phố' }}</h1>

    <form method="POST"
          action="{{ $province ? route('admin.locations.provinces.update', $province) : route('admin.locations.provinces.store') }}"
          class="space-y-6">
        @csrf
        @if($province) @method('PUT') @endif

        <x-admin::form.section title="Thông tin chung">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <x-admin::form.input
                        name="name"
                        label="Tên tỉnh/thành"
                        :value="old('name', $province?->name)"
                        required
                        x-on:blur="if (!slugEdited) { $refs.slugInput.value = toSlug($event.target.value) }"
                    />
                </div>
                <div>
                    <x-admin::form.input
                        name="slug"
                        label="Đường dẫn (slug)"
                        :value="old('slug', $province?->slug)"
                        required
                        x-ref="slugInput"
                        x-on:input="slugEdited = $event.target.value !== ''"
                    />
                </div>
                <div>
                    <x-admin::form.input
                        name="title"
                        label="Tiêu đề SEO"
                        :value="old('title', $province?->title)"
                    />
                </div>
                <div>
                    <x-admin::form.input
                        name="priority"
                        label="Độ ưu tiên"
                        type="number"
                        :value="old('priority', $province?->priority ?? 0)"
                        required
                    />
                </div>
                <div class="md:col-span-2">
                    <x-admin::form.input
                        name="description"
                        label="Mô tả SEO"
                        :value="old('description', $province?->description)"
                    />
                </div>
            </div>
        </x-admin::form.section>

        <x-admin::form.section title="Hình ảnh">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-admin::form.upload
                    name="thumbnail_url"
                    label="Ảnh đại diện"
                    :value="old('thumbnail_url', $province?->thumbnail_url)"
                />
                <x-admin::form.upload
                    name="image_list_url"
                    label="Album ảnh"
                    :value="old('image_list_url', $province?->image_list_url ?? [])"
                    :multiple="true"
                />
            </div>
        </x-admin::form.section>

        <x-admin::form.section title="Nội dung">
            <x-admin::form.rich-text
                name="content"
                label="Nội dung chi tiết"
                :value="old('content', $province?->content)"
            />
        </x-admin::form.section>

        <div class="flex items-center gap-4 pb-6">
            <button type="submit"
                    class="px-6 py-2.5 bg-brand-600 text-white text-sm font-medium rounded hover:bg-brand-700 transition-colors">
                {{ $province ? 'Lưu thay đổi' : 'Thêm tỉnh/thành' }}
            </button>
            <a href="{{ route('admin.locations.index', ['section' => 'provinces']) }}"
               class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded hover:bg-gray-50 transition-colors">
                Hủy
            </a>
        </div>
    </form>
</div>
@endsection
