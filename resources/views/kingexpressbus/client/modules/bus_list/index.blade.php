@extends("kingexpressbus.client.layouts.main")

{{-- Section Title --}}
@section("title")
    Đặt vé xe {{ $route->start_province_name }} đi {{ $route->end_province_name }} ngày {{ $departure_date->format('d/m/Y') }}
@endsection

{{-- Push CSS/JS của Flowbite (nếu chưa có trong layout chính) --}}
@pushonce('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet"/>
    {{-- Thêm CSS tùy chỉnh nếu cần --}}
    <style>
        /* Tùy chỉnh nhỏ cho Flowbite để hợp theme vàng */
        [data-accordion-trigger] span, [data-accordion-trigger] svg {
            color: #ca8a04; /* text-yellow-600 */
        }

        [data-accordion-trigger]:focus {
            --tw-ring-color: #fcd34d; /* ring-yellow-300 */
        }

        [data-dropdown-toggle] {
            /* Style cho nút dropdown filter */
        }

        /* Thêm style cho các card xe nếu cần */
        .bus-card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
    </style>
@endpushonce

@pushonce('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
@endpushonce


@section("content")
    <div class="bg-gray-50 min-h-screen">
        <div class="container mx-auto px-4 py-8">

            {{-- 1. Thông tin Tuyến và Ngày đi --}}
            <div class="mb-6 bg-white p-4 rounded-lg shadow">
                <h1 class="text-2xl md:text-3xl font-bold text-yellow-800">
                    {{ $route->start_province_name }}
                    <svg class="inline-block w-5 h-5 mx-1 text-gray-400" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                    {{ $route->end_province_name }}
                </h1>
                <p class="text-gray-600 mt-1">Ngày đi: <span
                        class="font-semibold">{{ $departure_date->format('d/m/Y') }}</span></p>
                {{-- Có thể thêm nút đổi ngày/tuyến ở đây --}}
                <a href="{{ route('homepage') }}#search-section"
                   class="text-sm text-yellow-600 hover:text-yellow-800 mt-2 inline-block">&larr; Đổi tìm kiếm</a>
            </div>

            {{-- Hiển thị thông báo lỗi chung nếu có từ redirect --}}
            @if(session('error'))
                <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            {{-- 2. Bộ lọc và Sắp xếp --}}
            <div class="mb-6 bg-white p-4 rounded-lg shadow">
                <form id="filterSortForm" method="GET"
                      action="{{ route('client.bus_list', ['route_slug' => $route->slug]) }}">
                    {{-- Luôn truyền lại ngày đi khi filter/sort --}}
                    <input type="hidden" name="departure_date" value="{{ $departure_date->format('Y-m-d') }}">

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
                        {{-- Sắp xếp --}}
                        <div>
                            <label for="sort_by" class="block mb-1 text-sm font-medium text-gray-700">Sắp xếp
                                theo</label>
                            <select id="sort_by" name="sort_by"
                                    onchange="document.getElementById('filterSortForm').submit()"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5">
                                <option value="time_asc" @selected($sort_by == 'time_asc')>Giờ đi sớm nhất</option>
                                <option value="time_desc" @selected($sort_by == 'time_desc')>Giờ đi muộn nhất</option>
                                <option value="price_asc" @selected($sort_by == 'price_asc')>Giá tăng dần</option>
                                <option value="price_desc" @selected($sort_by == 'price_desc')>Giá giảm dần</option>
                            </select>
                        </div>

                        {{-- Lọc theo giờ --}}
                        <div>
                            <label for="filter_time_start" class="block mb-1 text-sm font-medium text-gray-700">Giờ đi
                                từ</label>
                            <input type="time" id="filter_time_start" name="filter_time_start"
                                   value="{{ $filter_time_start }}"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5">
                        </div>
                        <div>
                            <label for="filter_time_end" class="block mb-1 text-sm font-medium text-gray-700">Đến
                                giờ</label>
                            <input type="time" id="filter_time_end" name="filter_time_end"
                                   value="{{ $filter_time_end }}"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5">
                        </div>

                        {{-- Lọc theo loại xe --}}
                        @if($availableBusTypes->isNotEmpty())
                            <div>
                                <label for="filter_bus_type" class="block mb-1 text-sm font-medium text-gray-700">Loại
                                    xe</label>
                                <select id="filter_bus_type" name="filter_bus_type"
                                        onchange="document.getElementById('filterSortForm').submit()"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5">
                                    <option value="">Tất cả loại xe</option>
                                    @foreach($availableBusTypes as $typeName => $typeValue)
                                        <option
                                            value="{{ $typeValue }}" @selected($filter_bus_type == $typeValue)>{{ $typeName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- Nút Lọc (nếu không muốn tự submit khi thay đổi) --}}
                        {{-- <button type="submit" class="text-white bg-yellow-600 hover:bg-yellow-700 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Lọc</button> --}}

                        {{-- Nút Reset Filter --}}
                        @if($request->has('sort_by') || $request->has('filter_time_start') || $request->has('filter_time_end') || $request->has('filter_bus_type'))
                            <a href="{{ route('client.bus_list', ['route_slug' => $route->slug, 'departure_date' => $departure_date->format('Y-m-d')]) }}"
                               class="text-gray-600 hover:text-yellow-700 border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition duration-150">
                                Bỏ lọc
                            </a>
                        @endif

                    </div>
                </form>
            </div>

            {{-- 3. Danh sách Chuyến xe --}}
            <div class="space-y-6">
                @forelse($busRoutes as $busRoute)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden transition duration-300 bus-card-hover">
                        <div class="md:flex">
                            {{-- Hình ảnh xe --}}
                            <div class="md:flex-shrink-0">
                                <img class="h-48 h-full object-cover md:w-80"
                                     src="{{ $busRoute->bus_thumbnail ?? 'https://placehold.co/300x200/fef3c7/ca8a04?text=' . urlencode($busRoute->bus_name) }}"
                                     alt="Xe {{ $busRoute->bus_name }}">
                            </div>
                            {{-- Thông tin chuyến --}}
                            <div class="p-4 md:p-6 flex-grow flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h3 class="text-xl font-semibold text-gray-800">{{ $busRoute->bus_name }}</h3>
                                            <p class="text-sm text-yellow-700">{{ $busRoute->bus_type_name }}</p>
                                        </div>
                                        <div class="text-right ml-4 flex-shrink-0">
                                            <p class="text-xl font-bold text-yellow-600">
                                                {{ $busRoute->price ? number_format($busRoute->price) . 'đ' : 'Liên hệ' }}
                                            </p>
                                            <p class="text-xs text-gray-500">/vé</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center text-gray-700 mb-3">
                                        <span
                                            class="font-bold text-lg">{{ \Carbon\Carbon::parse($busRoute->start_at)->format('H:i') }}</span>
                                        <svg class="w-4 h-4 mx-2 text-gray-400" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                        </svg>
                                        <span
                                            class="text-gray-500">{{ \Carbon\Carbon::parse($busRoute->end_at)->format('H:i') }}</span>
                                        <span class="text-gray-500 text-sm ml-3">({{ $busRoute->duration_formatted ?? '...' }})</span>
                                    </div>

                                    <p class="text-sm text-gray-600 mb-3">
                                        <span class="font-medium">Điểm đi:</span> {{ $route->start_province_name }} -
                                        <span class="font-medium">Điểm đến:</span> {{ $route->end_province_name }}
                                    </p>

                                    <p class="text-sm text-gray-600 mb-3">
                                        <span class="font-medium">Còn lại:</span> {{ $busRoute->total_seats ?? 'N/A' }}
                                        chỗ {{-- Tạm hiển thị tổng số ghế --}}
                                    </p>

                                    {{-- Tiện ích --}}
                                    @if(!empty($busRoute->bus_services))
                                        <div class="mb-4">
                                            <p class="text-sm font-medium text-gray-700 mb-1">Tiện ích:</p>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($busRoute->bus_services as $service)
                                                    <span
                                                        class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded border border-yellow-300">{{ $service }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Điểm dừng (Accordion) --}}
                                    @if($busRoute->stops->isNotEmpty())
                                        <div id="accordion-stops-{{ $busRoute->bus_route_id }}"
                                             data-accordion="collapse" class="mb-4">
                                            <h2 id="accordion-stops-heading-{{ $busRoute->bus_route_id }}">
                                                <button type="button"
                                                        class="flex items-center justify-between w-full py-2 px-3 text-sm font-medium text-left text-gray-500 border border-gray-200 rounded-t-lg focus:ring-2 focus:ring-yellow-200 hover:bg-gray-50"
                                                        data-accordion-target="#accordion-stops-body-{{ $busRoute->bus_route_id }}"
                                                        aria-expanded="false"
                                                        aria-controls="accordion-stops-body-{{ $busRoute->bus_route_id }}">
                                                    <span>Xem các điểm dừng</span>
                                                    <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0"
                                                         aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                         fill="none" viewBox="0 0 10 6">
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                              stroke-linejoin="round" stroke-width="2"
                                                              d="M9 5 5 1 1 5"/>
                                                    </svg>
                                                </button>
                                            </h2>
                                            <div id="accordion-stops-body-{{ $busRoute->bus_route_id }}" class="hidden"
                                                 aria-labelledby="accordion-stops-heading-{{ $busRoute->bus_route_id }}">
                                                <div
                                                    class="p-3 border border-t-0 border-gray-200 rounded-b-lg bg-gray-50">
                                                    <ul class="list-disc list-inside space-y-1 text-xs text-gray-600">
                                                        @foreach($busRoute->stops as $stop)
                                                            <li>
                                                                <span
                                                                    class="font-semibold">{{ \Carbon\Carbon::parse($stop->stop_at)->format('H:i') }}</span>
                                                                - {{ $stop->stop_title ? $stop->stop_title . ' (' . $stop->district_name . ')' : $stop->district_name }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                </div>

                                {{-- Nút chọn chuyến --}}
                                <div class="mt-auto pt-4 border-t border-gray-100 text-right">
                                    {{-- Link đến trang đặt vé với slug và ngày đi --}}
                                    <a href="{{ route('client.bus_detail', ['bus_route_slug' => $busRoute->bus_route_slug, 'departure_date' => $departure_date->format('Y-m-d')]) }}"
                                       class="inline-block text-white bg-yellow-600 hover:bg-yellow-700 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition duration-200">
                                        Xem chi tiết
                                    </a>
                                    {{-- Có thể thêm nút "Xem chi tiết" nếu có trang chi tiết bus_route --}}
                                    {{-- <a href="{{ route('client.bus_detail', ['bus_route_slug' => $busRoute->bus_route_slug]) }}" class="ml-2 text-sm text-yellow-600 hover:underline">Xem chi tiết</a> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- Thông báo khi không có chuyến xe nào --}}
                    <div class="text-center py-10 bg-white rounded-lg shadow">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" aria-hidden="true">
                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2H4a2 2 0 01-2-2z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Không tìm thấy chuyến xe</h3>
                        <p class="mt-1 text-sm text-gray-500">Không có chuyến xe nào phù hợp với lựa chọn của bạn vào
                            ngày này.</p>
                        <div class="mt-6">
                            <a href="{{ route('homepage') }}#search-section"
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                     fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                          d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                          clip-rule="evenodd"/>
                                </svg>
                                Tìm kiếm lại
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- 4. Phân trang (Nếu có) --}}
            {{-- Nếu $busRoutes là đối tượng Paginator, hiển thị link phân trang --}}
            {{-- <div class="mt-8">
                {{ $busRoutes->appends(request()->query())->links() }} // Giữ lại các query param khi chuyển trang
            </div> --}}

        </div>
    </div>
@endsection
