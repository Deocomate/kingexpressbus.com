<x-admin.layout title="Bảng điều khiển">
    <x-slot:breadcrumb>
        {{-- Breadcrumb có thể để trống hoặc chỉ có Dashboard vì đây là trang chính --}}
    </x-slot:breadcrumb>

    {{-- 1. Info Boxes --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($totalBookings) }}</h3>
                    <p>Tổng số Đặt vé</p>
                </div>
                <div class="icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($totalRevenue) }}<sup style="font-size: 20px">đ</sup></h3>
                    <p>Tổng Doanh thu</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($totalBuses ?? 0) }} <small class="text-sm">xe</small> / {{ number_format($totalTrips ?? 0) }} <small class="text-sm">chuyến</small></h3>
                    <p>Đội xe & Chuyến hoạt động</p>
                </div>
                <div class="icon">
                    <i class="fas fa-bus"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format($totalCustomers) }}</h3>
                    <p>Khách hàng</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Charts --}}
    <div class="row">
        <div class="col-md-7">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Doanh thu 12 tháng gần nhất</h3>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="monthlyRevenueChart"
                                style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Thống kê trạng thái đặt vé</h3>
                </div>
                <div class="card-body">
                    <canvas id="bookingStatusChart"
                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Latest Bookings --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Đặt vé gần đây</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>Mã Đặt vé</th>
                            <th>Khách hàng</th>
                            <th>Tuyến đường</th>
                            <th>Ngày đi</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($latestBookings as $booking)
                            <tr>
                                <td><strong>#{{ $booking->booking_code }}</strong></td>
                                <td>{{ $booking->customer_name }}</td>
                                <td>{{ $booking->route_name }}</td>
                                <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</td>
                                <td>{{ number_format($booking->total_price) }}đ</td>
                                <td>
                                    @php
                                        $statusClasses = [
                                            'pending' => 'badge-warning',
                                            'confirmed' => 'badge-success',
                                            'cancelled' => 'badge-danger',
                                            'completed' => 'badge-primary',
                                        ];
                                        $statusTexts = [
                                            'pending' => 'Chờ xác nhận',
                                            'confirmed' => 'Đã xác nhận',
                                            'cancelled' => 'Đã hủy',
                                            'completed' => 'Hoàn thành',
                                        ];
                                    @endphp
                                    <span class="badge {{ $statusClasses[$booking->status] ?? 'badge-secondary' }}">
                                        {{ $statusTexts[$booking->status] ?? ucfirst($booking->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Chưa có dữ liệu đặt vé.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // --- Booking Status Chart (Doughnut) ---
                const bookingStatusCtx = document.getElementById('bookingStatusChart').getContext('2d');
                new Chart(bookingStatusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($bookingStatusLabels) !!},
                        datasets: [{
                            data: {!! json_encode($bookingStatusData) !!},
                            backgroundColor: ['#ffc107', '#28a745', '#dc3545', '#007bff'],
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            }
                        }
                    }
                });

                // --- Monthly Revenue Chart (Bar) ---
                const monthlyRevenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
                new Chart(monthlyRevenueCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($monthlyRevenueLabels) !!},
                        datasets: [{
                            label: 'Doanh thu',
                            data: {!! json_encode($monthlyRevenueData) !!},
                            backgroundColor: 'rgba(0, 123, 255, 0.7)',
                            borderColor: 'rgba(0, 123, 255, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function (value) {
                                        return new Intl.NumberFormat('vi-VN', {
                                            style: 'currency',
                                            currency: 'VND'
                                        }).format(value);
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('vi-VN', {
                                                style: 'currency',
                                                currency: 'VND'
                                            }).format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-admin.layout>
