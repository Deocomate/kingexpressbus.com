@extends('admin.layouts.app')

@section('title', $profile ? 'Sửa hồ sơ: ' . $profile->profile_name : 'Thêm hồ sơ website')

@section('content')
@php
    $isCreate = $profile === null;
    $action   = $isCreate
        ? route('admin.website.profiles.store')
        : route('admin.website.profiles.update', $profile);
@endphp

<div class="max-w-3xl mx-auto">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.website.index', ['section' => 'profile']) }}"
           class="text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-gray-900">
                {{ $isCreate ? 'Thêm hồ sơ website' : 'Sửa hồ sơ: ' . $profile->profile_name }}
            </h1>
        </div>
    </div>

    <form method="POST" action="{{ $action }}" novalidate>
        @csrf
        @if(!$isCreate) @method('PUT') @endif

        <div class="space-y-6">

            {{-- Thông tin cơ bản --}}
            <x-admin::form.section title="Thông tin cơ bản">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <x-admin::form.input
                        name="profile_name"
                        label="Tên cấu hình"
                        :value="old('profile_name', $profile?->profile_name)"
                        :required="true"
                        class="md:col-span-2"
                    />
                    <x-admin::form.toggle
                        name="is_default"
                        label="Cấu hình mặc định"
                        :value="old('is_default', $profile?->is_default ?? false)"
                        hint="Chỉ một cấu hình có thể là mặc định. Các cấu hình khác sẽ tự động bỏ mặc định."
                    />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
                    <x-admin::form.input
                        name="title"
                        label="Tiêu đề website"
                        :value="old('title', $profile?->title)"
                    />
                    <x-admin::form.input
                        name="description"
                        label="Mô tả website (SEO)"
                        :value="old('description', $profile?->description)"
                    />
                </div>
            </x-admin::form.section>

            {{-- Nhận diện --}}
            <x-admin::form.section title="Nhận diện thương hiệu">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <x-admin::form.upload
                        name="logo_url"
                        label="Logo"
                        :value="old('logo_url', $profile?->logo_url)"
                        accept="image/*"
                        hint="PNG, SVG hoặc JPEG. Tối đa 2MB."
                    />
                    <x-admin::form.upload
                        name="favicon_url"
                        label="Favicon"
                        :value="old('favicon_url', $profile?->favicon_url)"
                        accept="image/*,image/x-icon,.ico"
                        hint="ICO, PNG. Khuyến nghị 32×32px."
                    />
                </div>
            </x-admin::form.section>

            {{-- Liên hệ --}}
            <x-admin::form.section title="Thông tin liên hệ">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <x-admin::form.input
                        name="email"
                        label="Email"
                        type="email"
                        :value="old('email', $profile?->email)"
                    />
                    <x-admin::form.input
                        name="phone"
                        label="Số điện thoại"
                        :value="old('phone', $profile?->phone)"
                    />
                    <x-admin::form.input
                        name="hotline"
                        label="Hotline"
                        :value="old('hotline', $profile?->hotline)"
                    />
                    <x-admin::form.input
                        name="whatsapp"
                        label="WhatsApp"
                        :value="old('whatsapp', $profile?->whatsapp)"
                    />
                    <div class="md:col-span-2">
                        <x-admin::form.input
                            name="address"
                            label="Địa chỉ"
                            :value="old('address', $profile?->address)"
                        />
                    </div>
                    <x-admin::form.input
                        name="facebook_url"
                        label="Facebook URL"
                        type="url"
                        :value="old('facebook_url', $profile?->facebook_url)"
                        placeholder="https://facebook.com/..."
                    />
                    <x-admin::form.input
                        name="zalo_url"
                        label="Zalo URL"
                        type="url"
                        :value="old('zalo_url', $profile?->zalo_url)"
                        placeholder="https://zalo.me/..."
                    />
                </div>
            </x-admin::form.section>

            {{-- Nội dung --}}
            <x-admin::form.section title="Nội dung & Bản đồ">
                <x-admin::form.textarea
                    name="map_embedded"
                    label="Mã nhúng bản đồ (iframe)"
                    :value="old('map_embedded', $profile?->map_embedded)"
                    :rows="5"
                    placeholder='<iframe src="https://maps.google.com/..." ...></iframe>'
                    hint="Dán thẳng mã &lt;iframe&gt; từ Google Maps. Không dùng rich editor."
                />

                <div class="mt-5">
                    <x-admin::form.rich-text
                        name="policy_content"
                        label="Nội dung chính sách"
                        :value="old('policy_content', $profile?->policy_content)"
                        height="240px"
                    />
                </div>

                <div class="mt-5">
                    <x-admin::form.rich-text
                        name="introduction_content"
                        label="Nội dung giới thiệu"
                        :value="old('introduction_content', $profile?->introduction_content)"
                        height="240px"
                    />
                </div>
            </x-admin::form.section>

        </div>

        <div class="mt-6 flex items-center justify-end gap-3 pb-8">
            <a href="{{ route('admin.website.index', ['section' => 'profile']) }}"
               class="rounded border border-gray-300 px-5 py-2 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                Hủy
            </a>
            <button type="submit"
                    class="rounded bg-brand-600 px-5 py-2 text-sm font-medium text-white hover:bg-brand-700 transition-colors">
                {{ $isCreate ? 'Thêm hồ sơ' : 'Lưu thay đổi' }}
            </button>
        </div>
    </form>
</div>
@endsection
