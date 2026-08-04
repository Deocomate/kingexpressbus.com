<x-admin::form.section title="Danh sách hồ sơ website">
    @if($profiles->isEmpty())
        <p class="text-sm text-gray-500">Chưa có hồ sơ nào. <a href="{{ route('admin.website.profiles.create') }}" class="text-brand-600 underline">Thêm mới</a>.</p>
    @else
    <div class="overflow-x-auto -mx-6 -mb-5">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Tên cấu hình</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Tiêu đề</th>
                    <th class="px-6 py-3 text-center font-medium text-gray-500 uppercase tracking-wide text-xs">Mặc định</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Email</th>
                    <th class="px-6 py-3 text-right font-medium text-gray-500 uppercase tracking-wide text-xs">Hành động</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @foreach($profiles as $profile)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">
                        {{ $profile->profile_name }}
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $profile->title ?: '—' }}</td>
                    <td class="px-6 py-4 text-center">
                        @if($profile->is_default)
                            <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Mặc định
                            </span>
                        @else
                            <form method="POST" action="{{ route('admin.website.profiles.setDefault', $profile) }}">
                                @csrf
                                <button type="submit" class="text-xs text-brand-600 hover:text-brand-800 underline">
                                    Đặt mặc định
                                </button>
                            </form>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $profile->email ?: '—' }}</td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <a href="{{ route('admin.website.profiles.edit', $profile) }}"
                           class="inline-flex items-center gap-1 rounded px-2.5 py-1 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors mr-1">
                            Sửa
                        </a>
                        @if(!$profile->is_default || $profiles->count() === 1)
                        <form method="POST" action="{{ route('admin.website.profiles.destroy', $profile) }}" class="inline"
                              x-data
                              @submit.prevent="if(confirm('Xóa hồ sơ này?')) $el.submit()">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center gap-1 rounded px-2.5 py-1 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 transition-colors">
                                Xóa
                            </button>
                        </form>
                        @else
                        <span class="inline-flex items-center gap-1 rounded px-2.5 py-1 text-xs font-medium text-gray-400 bg-gray-50 cursor-not-allowed"
                              title="Đặt hồ sơ khác làm mặc định trước khi xóa hồ sơ này">
                            Xóa
                        </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</x-admin::form.section>
