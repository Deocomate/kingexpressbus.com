<x-admin.layout title="Quản lý Đội xe">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Quản lý Đội xe</li>
    </x-slot:breadcrumb>

@push('styles')
    <style>
        /* ========== Statistics Cards ========== */
        .stats-card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.12);
        }
        .stats-card .card-body {
            padding: 1.25rem;
        }
        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .stats-number {
            font-size: 1.75rem;
            font-weight: 700;
            line-height: 1.2;
        }
        .stats-label {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 2px;
        }

        /* ========== View Toggle ========== */
        .view-toggle .btn {
            padding: 0.375rem 0.75rem;
        }
        .view-toggle .btn.active {
            background-color: #007bff;
            color: white;
        }

        /* ========== Filter Section ========== */
        .filter-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .filter-section .form-group {
            margin-bottom: 0;
        }

        /* ========== Table Improvements ========== */
        .bus-table-wrapper {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        #buses-table {
            margin-bottom: 0;
        }
        #buses-table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            font-weight: 600;
            padding: 15px 12px;
            font-size: 0.9rem;
        }
        #buses-table tbody td {
            vertical-align: middle;
            padding: 12px;
        }
        #buses-table tbody tr:hover {
            background-color: #f8f9ff;
        }

        /* Bus Info Cell */
        .bus-info-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .bus-thumbnail {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .bus-name-wrapper {
            flex: 1;
        }
        .bus-name {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 2px;
            font-size: 0.95rem;
        }
        .bus-model {
            color: #718096;
            font-size: 0.85rem;
        }

        /* Seat Count Badge */
        .seat-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .seat-badge i {
            font-size: 0.8rem;
        }

        /* Services Pills */
        .services-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            max-width: 200px;
        }
        .service-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #e8f4fd;
            color: #0077b6;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 500;
            white-space: nowrap;
        }
        .service-pill i {
            font-size: 0.7rem;
        }
        .more-services {
            background: #f0f0f0;
            color: #666;
        }

        /* Priority Badge */
        .priority-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            font-weight: 600;
            font-size: 0.85rem;
        }
        .priority-high { background: #d4edda; color: #155724; }
        .priority-medium { background: #fff3cd; color: #856404; }
        .priority-low { background: #f8f9fa; color: #6c757d; }

        /* Actions */
        .action-btns {
            display: flex;
            gap: 5px;
            justify-content: flex-end;
        }
        .action-btn {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: all 0.2s ease;
        }
        .action-btn:hover {
            transform: scale(1.1);
        }
        .btn-view { background: #e3f2fd; color: #1976d2; }
        .btn-view:hover { background: #1976d2; color: white; }
        .btn-edit { background: #fff8e1; color: #f57c00; }
        .btn-edit:hover { background: #f57c00; color: white; }
        .btn-delete { background: #ffebee; color: #d32f2f; }
        .btn-delete:hover { background: #d32f2f; color: white; }
        .btn-duplicate { background: #e8f5e9; color: #388e3c; }
        .btn-duplicate:hover { background: #388e3c; color: white; }

        /* ========== Card View ========== */
        .bus-cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }
        .bus-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .bus-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        .bus-card-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .bus-card-body {
            padding: 20px;
        }
        .bus-card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 5px;
        }
        .bus-card-subtitle {
            color: #718096;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }
        .bus-card-stats {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
            margin-bottom: 15px;
        }
        .bus-card-stat {
            text-align: center;
        }
        .bus-card-stat-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #667eea;
        }
        .bus-card-stat-label {
            font-size: 0.75rem;
            color: #a0aec0;
            text-transform: uppercase;
        }
        .bus-card-services {
            margin-bottom: 15px;
        }
        .bus-card-actions {
            display: flex;
            gap: 10px;
        }
        .bus-card-actions .btn {
            flex: 1;
        }

        /* ========== Quick View Modal ========== */
        .quick-view-header {
            position: relative;
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            overflow: hidden;
        }
        .quick-view-header img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.9;
        }
        .quick-view-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            background: linear-gradient(transparent, rgba(0,0,0,0.7));
            color: white;
        }
        .quick-view-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .quick-view-body {
            padding: 20px;
        }
        .info-row {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1rem;
        }
        .info-label {
            color: #718096;
            font-size: 0.85rem;
        }
        .info-value {
            font-weight: 600;
            color: #2d3748;
        }

        /* ========== Form Sections ========== */
        .form-section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 4px solid #667eea;
            padding-left: 12px;
            margin-bottom: 1.5rem;
            margin-top: 1.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: #2d3748;
        }
        .form-section-title i {
            color: #667eea;
        }

        .service-checkbox {
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .service-checkbox .form-check-label {
            cursor: pointer;
            padding: 8px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            transition: all 0.2s ease;
        }
        .service-checkbox .form-check-input:checked + .form-check-label {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        .service-checkbox .form-check-input {
            display: none;
        }

        /* ========== Seat Map Preview ========== */
        #seat-map-preview {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8eb 100%);
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-top: 15px;
            min-height: 150px;
        }

        .seat-deck {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px dashed #cbd5e0;
        }
        .seat-deck:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .seat-deck-title {
            font-weight: 600;
            margin-bottom: 15px;
            color: #4a5568;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .seat-deck-title::before {
            content: '';
            display: inline-block;
            width: 12px;
            height: 12px;
            background: #667eea;
            border-radius: 3px;
        }

        .seat-row {
            display: flex;
            margin-bottom: 8px;
            justify-content: center;
        }

        .seat {
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            border: 2px solid #cbd5e0;
            border-radius: 8px;
            margin-right: 8px;
            background: white;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            user-select: none;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .seat:hover {
            background: #e3f2fd;
            border-color: #667eea;
            transform: scale(1.05);
        }

        .seat.seat-disabled {
            background: linear-gradient(135deg, #a0aec0 0%, #718096 100%);
            color: white;
            text-decoration: line-through;
            border-color: #718096;
            cursor: not-allowed;
        }
        .seat.seat-disabled:hover {
            transform: none;
        }

        .seat-aisle {
            width: 25px;
            height: 40px;
            margin-right: 8px;
            position: relative;
        }
        .seat-aisle::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e2e8f0;
        }

        .seat-map-legend {
            display: flex;
            gap: 20px;
            font-size: 0.85rem;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .legend-color-box {
            width: 24px;
            height: 24px;
            border: 2px solid #cbd5e0;
            border-radius: 5px;
        }

        /* ========== Modal Improvements ========== */
        #bus-modal .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom: none;
            padding: 20px 25px;
        }
        #bus-modal .modal-header .close {
            color: white;
            opacity: 0.8;
        }
        #bus-modal .modal-header .close:hover {
            opacity: 1;
        }
        #bus-modal .modal-body {
            padding: 25px;
        }
        #bus-modal .modal-footer {
            border-top: 1px solid #e2e8f0;
            padding: 15px 25px;
        }

        /* ========== Empty State ========== */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        .empty-state-icon {
            font-size: 4rem;
            color: #cbd5e0;
            margin-bottom: 20px;
        }
        .empty-state-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 10px;
        }
        .empty-state-text {
            color: #718096;
            margin-bottom: 20px;
        }

        /* ========== Loading State ========== */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #e2e8f0;
            border-top-color: #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
@endpush

    {{-- Statistics Overview --}}
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card stats-card">
                <div class="card-body d-flex align-items-center">
                    <div class="stats-icon bg-primary text-white mr-3">
                        <i class="fas fa-bus"></i>
                    </div>
                    <div>
                        <div class="stats-number" id="stat-total-buses">0</div>
                        <div class="stats-label">Tổng số xe</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card stats-card">
                <div class="card-body d-flex align-items-center">
                    <div class="stats-icon bg-success text-white mr-3">
                        <i class="fas fa-chair"></i>
                    </div>
                    <div>
                        <div class="stats-number" id="stat-total-seats">0</div>
                        <div class="stats-label">Tổng số ghế</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card stats-card">
                <div class="card-body d-flex align-items-center">
                    <div class="stats-icon bg-info text-white mr-3">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <div class="stats-number" id="stat-models">0</div>
                        <div class="stats-label">Loại xe</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card stats-card">
                <div class="card-body d-flex align-items-center">
                    <div class="stats-icon bg-warning text-white mr-3">
                        <i class="fas fa-concierge-bell"></i>
                    </div>
                    <div>
                        <div class="stats-number" id="stat-services">{{ count($services) }}</div>
                        <div class="stats-label">Dịch vụ</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
            <h3 class="card-title mb-0">
                <i class="fas fa-bus-alt mr-2"></i>Quản lý Đội xe
            </h3>
            <div class="d-flex align-items-center" style="gap: 15px;">
                {{-- View Toggle --}}
                <div class="btn-group view-toggle" role="group">
                    <button type="button" class="btn btn-outline-secondary btn-sm active" data-view="table" title="Xem dạng bảng">
                        <i class="fas fa-list"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-view="card" title="Xem dạng thẻ">
                        <i class="fas fa-th-large"></i>
                    </button>
                </div>
                {{-- Add Button --}}
                <button class="btn btn-success" id="btn-add">
                    <i class="fas fa-plus mr-1"></i> Thêm Xe mới
                </button>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="filter-section mx-3 mt-3">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="small text-muted mb-1">Tìm kiếm</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" class="form-control" id="filter-search" placeholder="Tên xe, dòng xe...">
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="small text-muted mb-1">Dịch vụ</label>
                        <select class="form-control" id="filter-service">
                            <option value="">-- Tất cả dịch vụ --</option>
                            @foreach($services as $service)
                                <option value="{{ $service->name }}">{{ $service->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="small text-muted mb-1">Số ghế</label>
                        <select class="form-control" id="filter-seats">
                            <option value="">-- Tất cả --</option>
                            <option value="1-20">1 - 20 ghế</option>
                            <option value="21-35">21 - 35 ghế</option>
                            <option value="36-50">36 - 50 ghế</option>
                            <option value="51+">51+ ghế</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 text-right">
                    <div class="form-group">
                        <button class="btn btn-outline-secondary btn-sm" id="btn-reset-filter">
                            <i class="fas fa-redo mr-1"></i> Đặt lại
                        </button>
                        <button class="btn btn-primary btn-sm" id="btn-export">
                            <i class="fas fa-file-excel mr-1"></i> Xuất Excel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            {{-- Table View --}}
            <div id="table-view" class="bus-table-wrapper">
                <table id="buses-table" class="table table-hover">
                    <thead>
                    <tr>
                        <th style="width: 35%;">Thông tin xe</th>
                        <th style="width: 20%;">Dịch vụ</th>
                        <th class="text-center" style="width: 12%;">Số ghế</th>
                        <th class="text-center" style="width: 10%;">Ưu tiên</th>
                        <th style="width: 23%;" class="text-right">Hành động</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

            {{-- Card View --}}
            <div id="card-view" class="bus-cards-container" style="display: none;">
                {{-- Cards will be loaded dynamically --}}
            </div>
        </div>
    </div>

    {{-- Add/Edit Modal --}}
    <div class="modal fade" id="bus-modal" tabindex="-1" role="dialog" aria-labelledby="bus-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <form id="bus-form" novalidate>
                    <div class="modal-header">
                        <h5 class="modal-title" id="bus-modal-label">
                            <i class="fas fa-bus mr-2"></i>Thêm Xe mới
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="bus_id" name="id">

                        <h4 class="form-section-title">
                            <i class="fas fa-info-circle"></i> Thông tin cơ bản
                        </h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Tên xe (Tên gợi nhớ) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           placeholder="Ví dụ: Xe giường nằm 01" required>
                                    <small class="form-text text-muted">Tên dễ nhớ để phân biệt các xe với nhau</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="model_name">Dòng xe</label>
                                    <input type="text" class="form-control" id="model_name" name="model_name"
                                           placeholder="Ví dụ: Hyundai Universe">
                                    <small class="form-text text-muted">Hãng xe và model (nếu có)</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="seat_count">Số ghế</label>
                                    <input type="number" class="form-control" id="seat_count" name="seat_count" readonly>
                                    <small class="form-text text-muted">Tự động tính từ sơ đồ ghế</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="priority">Độ ưu tiên</label>
                                    <input type="number" class="form-control" id="priority" name="priority" value="0" required>
                                    <small class="form-text text-muted">Số càng cao, xe càng hiển thị trước</small>
                                </div>
                            </div>
                        </div>

                        <h4 class="form-section-title">
                            <i class="fas fa-th"></i> Sơ đồ ghế & Dịch vụ
                        </h4>
                        <div class="row">
                            <div class="col-lg-7">
                                <label>Trình tạo sơ đồ ghế</label>
                                <div class="card border-0 shadow-sm p-3">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="generator_decks" class="small">Số tầng</label>
                                                <select id="generator_decks" class="form-control">
                                                    <option value="1">1 tầng</option>
                                                    <option value="2">2 tầng</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="generator_rows" class="small">Số hàng</label>
                                                <input type="number" id="generator_rows" class="form-control" value="10" min="1">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="generator_layout" class="small">Bố cục dãy ghế</label>
                                                <select id="generator_layout" class="form-control">
                                                    <option value="1-1">2 Dãy (1-1)</option>
                                                    <option value="2-2">4 Dãy (2-2)</option>
                                                    <option value="1-1-1">3 Dãy (1-1-1)</option>
                                                    <option value="2-1">3 Dãy (2-1)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <div class="form-group w-100">
                                                <button type="button" id="btn-generate-map" class="btn btn-info w-100">
                                                    <i class="fas fa-magic mr-1"></i> Tạo
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="seat-map-preview">
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-couch fa-3x mb-3 d-block"></i>
                                            Nhấn "Tạo" để tạo sơ đồ ghế<br>
                                            <small>Sau đó click vào ghế để vô hiệu hóa/kích hoạt</small>
                                        </div>
                                    </div>
                                    <div class="seat-map-legend">
                                        <div class="legend-item">
                                            <div class="legend-color-box" style="background-color: #fff;"></div>
                                            <span>Còn trống</span>
                                        </div>
                                        <div class="legend-item">
                                            <div class="legend-color-box" style="background: linear-gradient(135deg, #a0aec0 0%, #718096 100%); border-color: #718096;"></div>
                                            <span>Vô hiệu hóa</span>
                                        </div>
                                    </div>
                                </div>
                                <textarea class="form-control" id="seat_map" name="seat_map" rows="3" required style="display: none;"></textarea>
                            </div>
                            <div class="col-lg-5">
                                <div class="form-group">
                                    <label>Các dịch vụ đi kèm</label>
                                    <div class="p-3 border rounded bg-light" style="min-height: 200px;">
                                        @forelse($services as $service)
                                            <div class="form-check form-check-inline service-checkbox">
                                                <input class="form-check-input" type="checkbox" name="services[]"
                                                       id="service-{{ $service->id }}" value="{{ $service->name }}">
                                                <label class="form-check-label" for="service-{{ $service->id }}">
                                                    <i class="{{ $service->icon }} mr-1"></i>{{ $service->name }}
                                                </label>
                                            </div>
                                        @empty
                                            <div class="text-center text-muted py-4">
                                                <i class="fas fa-concierge-bell fa-2x mb-2 d-block"></i>
                                                Chưa có dịch vụ nào<br>
                                                <a href="{{ route('admin.bus-services.index') }}" class="btn btn-sm btn-outline-primary mt-2">
                                                    <i class="fas fa-plus mr-1"></i> Thêm dịch vụ
                                                </a>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h4 class="form-section-title">
                            <i class="fas fa-images"></i> Hình ảnh & Mô tả
                        </h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="thumbnail_url">Ảnh đại diện</label>
                                    <div class="dropzone-wrapper">
                                        <input type="hidden" name="thumbnail_url" id="thumbnail_url" value="">
                                        <div id="dropzone-logo" class="dropzone" data-upload-url="{{ route('ckfinder_upload') }}">
                                            <div class="dz-message" data-dz-message>
                                                <span><i class="fas fa-cloud-upload-alt fa-2x mb-2 d-block"></i>Kéo thả ảnh hoặc <button type="button" class="dz-button">chọn ảnh</button></span>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-ckfinder-browse mt-2" data-target-dz="dropzone-logo">
                                            <i class="far fa-folder-open"></i> Duyệt thư viện
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="image_list_url">Album ảnh</label>
                                    <div class="dropzone-wrapper">
                                        <input type="hidden" name="image_list_url" id="image_list_url" value="[]">
                                        <div id="dropzone-album" class="dropzone" data-upload-url="{{ route('ckfinder_upload') }}">
                                            <div class="dz-message" data-dz-message>
                                                <span><i class="fas fa-images fa-2x mb-2 d-block"></i>Kéo thả nhiều ảnh hoặc <button type="button" class="dz-button">chọn ảnh</button></span>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-ckfinder-browse mt-2" data-target-dz="dropzone-album">
                                            <i class="far fa-folder-open"></i> Duyệt thư viện
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="content">Nội dung giới thiệu xe</label>
                            <textarea name="content" id="content" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Đóng
                        </button>
                        <button type="submit" class="btn btn-primary" id="save-bus-btn">
                            <i class="fas fa-save mr-1"></i> Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Quick View Modal --}}
    <div class="modal fade" id="quick-view-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="overflow: hidden;">
                <div class="quick-view-header">
                    <img id="qv-image" src="" alt="Bus">
                    <div class="quick-view-overlay">
                        <h3 class="quick-view-title" id="qv-name"></h3>
                        <span id="qv-model" class="text-white-50"></span>
                    </div>
                    <button type="button" class="close position-absolute" style="top: 15px; right: 20px; color: white; text-shadow: 0 2px 5px rgba(0,0,0,0.5);" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="quick-view-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-icon bg-primary text-white">
                                    <i class="fas fa-chair"></i>
                                </div>
                                <div>
                                    <div class="info-label">Số ghế</div>
                                    <div class="info-value" id="qv-seats"></div>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-icon bg-warning text-white">
                                    <i class="fas fa-sort-numeric-up"></i>
                                </div>
                                <div>
                                    <div class="info-label">Độ ưu tiên</div>
                                    <div class="info-value" id="qv-priority"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-icon bg-info text-white">
                                    <i class="fas fa-concierge-bell"></i>
                                </div>
                                <div>
                                    <div class="info-label">Dịch vụ</div>
                                    <div class="info-value" id="qv-services"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4" id="qv-content-wrapper" style="display: none;">
                        <h6 class="text-muted mb-2">Mô tả</h6>
                        <div id="qv-content" class="border rounded p-3 bg-light"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="qv-edit-btn">
                        <i class="fas fa-edit mr-1"></i> Chỉnh sửa
                    </button>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
    <script>
        $(function () {
            const placeholder = '{{ asset("/shared/dist/img/placeholder.png") }}';
            let contentEditor;
            let allBusesData = [];

            initCkEditor('#content').then(editor => contentEditor = editor).catch(e => console.error(e));
            initDropzoneDefault('dropzone-logo');
            initDropzoneMultipleImages('dropzone-album');

            // ========== DataTable Setup ==========
            const table = $('#buses-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.buses.list") }}',
                    data: function(d) {
                        d.service_filter = $('#filter-service').val();
                        d.seats_filter = $('#filter-seats').val();
                    },
                    dataSrc: function(json) {
                        // Update statistics
                        if (json.stats) {
                            $('#stat-total-buses').text(json.stats.total_buses);
                            $('#stat-total-seats').text(json.stats.total_seats);
                            $('#stat-models').text(json.stats.unique_models);
                        }
                        allBusesData = json.data;
                        return json.data;
                    }
                },
                columns: [
                    {
                        data: null, name: 'name', orderable: true,
                        render: function (data) {
                            const imageUrl = data.thumbnail_url || placeholder;
                            return `
                                <div class="bus-info-cell">
                                    <img src="${imageUrl}" alt="${data.name}" class="bus-thumbnail"
                                         onerror="this.onerror=null;this.src='${placeholder}';">
                                    <div class="bus-name-wrapper">
                                        <div class="bus-name">${data.name}</div>
                                        <div class="bus-model">${data.model_name || '<span class="text-muted">Chưa có thông tin</span>'}</div>
                                    </div>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'services', name: 'services', orderable: false, searchable: false,
                        render: function (data) {
                            if (!data || data.length === 0) {
                                return '<span class="text-muted small">Không có dịch vụ</span>';
                            }
                            const services = typeof data === 'string' ? JSON.parse(data) : data;
                            if (!Array.isArray(services) || services.length === 0) {
                                return '<span class="text-muted small">Không có dịch vụ</span>';
                            }
                            let html = '<div class="services-wrapper">';
                            const displayCount = Math.min(services.length, 3);
                            for (let i = 0; i < displayCount; i++) {
                                html += `<span class="service-pill"><i class="fas fa-check-circle"></i> ${services[i]}</span>`;
                            }
                            if (services.length > 3) {
                                html += `<span class="service-pill more-services">+${services.length - 3}</span>`;
                            }
                            html += '</div>';
                            return html;
                        }
                    },
                    {
                        data: 'seat_count', name: 'seat_count', className: 'text-center',
                        render: function (data) {
                            return `<span class="seat-badge"><i class="fas fa-chair"></i> ${data}</span>`;
                        }
                    },
                    {
                        data: 'priority', name: 'priority', className: 'text-center',
                        render: function (data) {
                            let cls = 'priority-low';
                            if (data >= 10) cls = 'priority-high';
                            else if (data >= 5) cls = 'priority-medium';
                            return `<span class="priority-badge ${cls}">${data}</span>`;
                        }
                    },
                    {
                        data: 'id', name: 'action', className: 'text-right', orderable: false, searchable: false,
                        render: function (data, type, row) {
                            return `
                                <div class="action-btns">
                                    <button class="action-btn btn-view view-btn" data-id="${data}" title="Xem nhanh">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="action-btn btn-edit edit-btn" data-id="${data}" title="Chỉnh sửa">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    <button class="action-btn btn-duplicate duplicate-btn" data-id="${data}" title="Nhân bản">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    <button class="action-btn btn-delete delete-btn" data-id="${data}" title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            `;
                        }
                    },
                ],
                language: {
                    processing: '<div class="loading-spinner"></div>',
                    emptyTable: `
                        <div class="empty-state">
                            <div class="empty-state-icon"><i class="fas fa-bus"></i></div>
                            <div class="empty-state-title">Chưa có xe nào</div>
                            <div class="empty-state-text">Bắt đầu thêm xe mới để quản lý đội xe của bạn</div>
                        </div>
                    `,
                    search: '',
                    searchPlaceholder: 'Tìm kiếm...',
                    lengthMenu: 'Hiển thị _MENU_ dòng',
                    info: 'Đang hiển thị _START_ đến _END_ trong tổng số _TOTAL_ xe',
                    infoEmpty: 'Không có dữ liệu',
                    paginate: {
                        first: '<i class="fas fa-angle-double-left"></i>',
                        last: '<i class="fas fa-angle-double-right"></i>',
                        next: '<i class="fas fa-angle-right"></i>',
                        previous: '<i class="fas fa-angle-left"></i>'
                    }
                },
                dom: "<'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>",
                pageLength: 10,
                order: [[3, 'desc']] // Order by priority
            });

            // ========== Custom Search ==========
            $('#filter-search').on('keyup', function() {
                table.search(this.value).draw();
            });

            $('#filter-service, #filter-seats').on('change', function() {
                table.ajax.reload();
            });

            $('#btn-reset-filter').on('click', function() {
                $('#filter-search').val('');
                $('#filter-service').val('');
                $('#filter-seats').val('');
                table.search('').ajax.reload();
            });

            // ========== View Toggle ==========
            $('.view-toggle .btn').on('click', function() {
                const view = $(this).data('view');
                $('.view-toggle .btn').removeClass('active');
                $(this).addClass('active');

                if (view === 'card') {
                    $('#table-view').hide();
                    $('#card-view').show();
                    renderCardView();
                } else {
                    $('#card-view').hide();
                    $('#table-view').show();
                }
            });

            function renderCardView() {
                const container = $('#card-view');
                container.empty();

                if (allBusesData.length === 0) {
                    container.html(`
                        <div class="col-12">
                            <div class="empty-state">
                                <div class="empty-state-icon"><i class="fas fa-bus"></i></div>
                                <div class="empty-state-title">Chưa có xe nào</div>
                                <div class="empty-state-text">Bắt đầu thêm xe mới để quản lý đội xe của bạn</div>
                            </div>
                        </div>
                    `);
                    return;
                }

                allBusesData.forEach(bus => {
                    const imageUrl = bus.thumbnail_url || placeholder;
                    const services = typeof bus.services === 'string' ? JSON.parse(bus.services || '[]') : (bus.services || []);
                    let servicesHtml = '';
                    if (Array.isArray(services) && services.length > 0) {
                        services.forEach(s => {
                            servicesHtml += `<span class="service-pill"><i class="fas fa-check-circle"></i> ${s}</span>`;
                        });
                    } else {
                        servicesHtml = '<span class="text-muted small">Không có dịch vụ</span>';
                    }

                    container.append(`
                        <div class="bus-card">
                            <img src="${imageUrl}" alt="${bus.name}" class="bus-card-image"
                                 onerror="this.onerror=null;this.src='${placeholder}';">
                            <div class="bus-card-body">
                                <h5 class="bus-card-title">${bus.name}</h5>
                                <p class="bus-card-subtitle">${bus.model_name || 'Chưa có thông tin dòng xe'}</p>
                                <div class="bus-card-stats">
                                    <div class="bus-card-stat">
                                        <div class="bus-card-stat-value">${bus.seat_count}</div>
                                        <div class="bus-card-stat-label">Ghế</div>
                                    </div>
                                    <div class="bus-card-stat">
                                        <div class="bus-card-stat-value">${bus.priority}</div>
                                        <div class="bus-card-stat-label">Ưu tiên</div>
                                    </div>
                                </div>
                                <div class="bus-card-services services-wrapper mb-3">
                                    ${servicesHtml}
                                </div>
                                <div class="bus-card-actions">
                                    <button class="btn btn-outline-primary btn-sm view-btn" data-id="${bus.id}">
                                        <i class="fas fa-eye"></i> Xem
                                    </button>
                                    <button class="btn btn-outline-warning btn-sm edit-btn" data-id="${bus.id}">
                                        <i class="fas fa-edit"></i> Sửa
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm delete-btn" data-id="${bus.id}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `);
                });
            }

            // ========== Export ==========
            $('#btn-export').on('click', function() {
                window.location.href = '{{ route("admin.buses.list") }}?export=excel';
            });

            // ========== Modal & Form ==========
            const modal = $('#bus-modal');
            const form = $('#bus-form');
            const previewContainer = $('#seat-map-preview');
            const hiddenSeatMapInput = $('#seat_map');
            const seatCountInput = $('#seat_count');

            function generateSeatMap() {
                const decks = parseInt($('#generator_decks').val(), 10);
                const rows = parseInt($('#generator_rows').val(), 10);
                const layout = $('#generator_layout').val().split('-').map(Number);

                let seatMapJson = [];
                let totalSeats = 0;
                previewContainer.empty();

                for (let d = 1; d <= decks; d++) {
                    const deckDiv = $('<div class="seat-deck"></div>');
                    if (decks > 1) {
                        deckDiv.append(`<div class="seat-deck-title">Tầng ${d}</div>`);
                    }

                    for (let r = 1; r <= rows; r++) {
                        const rowDiv = $('<div class="seat-row"></div>');
                        let charCode = 65;

                        for (let i = 0; i < layout.length; i++) {
                            for (let j = 0; j < layout[i]; j++) {
                                const seatLabel = `${String.fromCharCode(charCode++)}${r}`;
                                const seatNumber = `T${d}-${seatLabel}`;
                                rowDiv.append(`<div class="seat" data-seat-number="${seatNumber}">${seatLabel}</div>`);
                                seatMapJson.push({seat_number: seatNumber, status: 'available', deck: d});
                                totalSeats++;
                            }
                            if (i < layout.length - 1) {
                                rowDiv.append('<div class="seat-aisle"></div>');
                            }
                        }
                        deckDiv.append(rowDiv);
                    }
                    previewContainer.append(deckDiv);
                }

                hiddenSeatMapInput.val(JSON.stringify(seatMapJson, null, 2));
                seatCountInput.val(totalSeats);
                toastr.success(`Đã tạo sơ đồ ghế với ${totalSeats} ghế`);
            }

            function renderSeatMapPreview(seatMapString) {
                previewContainer.empty();
                if (!seatMapString) {
                    previewContainer.html(`
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-couch fa-3x mb-3 d-block"></i>
                            Nhấn "Tạo" để tạo sơ đồ ghế<br>
                            <small>Sau đó click vào ghế để vô hiệu hóa/kích hoạt</small>
                        </div>
                    `);
                    return;
                }
                try {
                    const seatMap = JSON.parse(seatMapString);
                    if (!Array.isArray(seatMap)) return;

                    const decks = {};
                    seatMap.forEach(seat => {
                        const deckNum = seat.deck || 1;
                        if (!decks[deckNum]) decks[deckNum] = [];
                        decks[deckNum].push(seat);
                    });

                    Object.keys(decks).sort().forEach(deckNumber => {
                        const deckDiv = $('<div class="seat-deck"></div>');
                        if (Object.keys(decks).length > 1) {
                            deckDiv.append(`<div class="seat-deck-title">Tầng ${deckNumber}</div>`);
                        }

                        const rows = {};
                        decks[deckNumber].forEach(seat => {
                            const rowNumber = parseInt(seat.seat_number.match(/\d+$/)[0], 10);
                            if (!rows[rowNumber]) rows[rowNumber] = [];
                            rows[rowNumber].push(seat);
                        });

                        Object.keys(rows).sort((a, b) => a - b).forEach(rowNumber => {
                            const rowDiv = $('<div class="seat-row"></div>');
                            const rowSeats = rows[rowNumber].sort((a, b) => a.seat_number.localeCompare(b.seat_number));
                            rowSeats.forEach(seat => {
                                const seatLabel = seat.seat_number.replace(`T${deckNumber}-`, '');
                                const isDisabled = seat.status !== 'available';
                                rowDiv.append(`<div class="seat ${isDisabled ? 'seat-disabled' : ''}" data-seat-number="${seat.seat_number}">${seatLabel}</div>`);
                            });
                            deckDiv.append(rowDiv);
                        });
                        previewContainer.append(deckDiv);
                    });
                } catch (e) {
                    console.error("Invalid seat map JSON", e);
                }
            }

            $('#btn-generate-map').on('click', generateSeatMap);

            previewContainer.on('click', '.seat', function () {
                const clickedSeat = $(this);
                const seatNumber = clickedSeat.data('seat-number');
                try {
                    let seatMap = JSON.parse(hiddenSeatMapInput.val());
                    const targetSeat = seatMap.find(s => s.seat_number === seatNumber);
                    if (targetSeat) {
                        targetSeat.status = (targetSeat.status === 'available') ? 'disabled' : 'available';
                        clickedSeat.toggleClass('seat-disabled');
                        hiddenSeatMapInput.val(JSON.stringify(seatMap, null, 2));

                        // Update seat count (count only available seats)
                        const availableSeats = seatMap.filter(s => s.status === 'available').length;
                        seatCountInput.val(availableSeats);

                        const action = targetSeat.status === 'available' ? 'kích hoạt' : 'vô hiệu hóa';
                        toastr.info(`Đã ${action} ghế ${seatNumber.replace(/T\d+-/, '')}`);
                    }
                } catch (e) {
                    toastr.error('Lỗi dữ liệu sơ đồ ghế. Vui lòng tạo lại.');
                }
            });

            function resetForm() {
                form[0].reset();
                form.find('input[type="hidden"]').val('');
                $('#image_list_url').val('[]');
                form.find('.is-invalid').removeClass('is-invalid').next('.invalid-feedback').remove();
                if (contentEditor) contentEditor.setData('');
                Dropzone.forElement("#dropzone-logo").removeAllFiles(true);
                Dropzone.forElement("#dropzone-album").removeAllFiles(true);
                $('input[name="services[]"]').prop('checked', false);
                previewContainer.html(`
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-couch fa-3x mb-3 d-block"></i>
                        Nhấn "Tạo" để tạo sơ đồ ghế<br>
                        <small>Sau đó click vào ghế để vô hiệu hóa/kích hoạt</small>
                    </div>
                `);
                seatCountInput.val(0);
            }

            $('#btn-add').on('click', function () {
                resetForm();
                $('#bus-modal-label').html('<i class="fas fa-bus mr-2"></i>Thêm Xe mới');
                modal.modal('show');
            });

            // ========== Edit Bus ==========
            $(document).on('click', '.edit-btn', function () {
                const id = $(this).data('id');
                $.get(`{{ url('admin/buses') }}/${id}`, function (response) {
                    if (response.success) {
                        resetForm();
                        const data = response.data;
                        $('#bus-modal-label').html(`<i class="fas fa-edit mr-2"></i>Chỉnh sửa: ${data.name}`);
                        $('#bus_id').val(data.id);
                        $('#name').val(data.name);
                        $('#model_name').val(data.model_name);
                        $('#priority').val(data.priority);

                        const seatMapStr = data.seat_map ? JSON.stringify(data.seat_map, null, 2) : '[]';
                        hiddenSeatMapInput.val(seatMapStr);
                        seatCountInput.val(data.seat_count);
                        renderSeatMapPreview(JSON.stringify(data.seat_map));

                        if (data.services && Array.isArray(data.services)) {
                            data.services.forEach(service => {
                                $(`input[name="services[]"][value="${service}"]`).prop('checked', true);
                            });
                        }
                        if (contentEditor) contentEditor.setData(data.content || '');

                        const logoDz = Dropzone.forElement("#dropzone-logo");
                        $('#thumbnail_url').val(data.thumbnail_url || '');
                        if (data.thumbnail_url) {
                            const mockFile = {name: data.thumbnail_url.split('/').pop(), size: 12345, serverUrl: data.thumbnail_url};
                            logoDz.emit("addedfile", mockFile);
                            logoDz.emit("thumbnail", mockFile, data.thumbnail_url);
                            logoDz.emit("complete", mockFile);
                            logoDz.files.push(mockFile);
                        }

                        const albumDz = Dropzone.forElement("#dropzone-album");
                        const imageList = data.image_list_url || [];
                        $('#image_list_url').val(JSON.stringify(imageList));
                        if (Array.isArray(imageList)) {
                            imageList.forEach(url => {
                                const mockFile = {name: url.split('/').pop(), size: 12345, serverUrl: url};
                                albumDz.emit("addedfile", mockFile);
                                albumDz.emit("thumbnail", mockFile, url);
                                albumDz.emit("complete", mockFile);
                                albumDz.files.push(mockFile);
                            });
                        }

                        modal.modal('show');
                    }
                });
            });

            // ========== Quick View ==========
            $(document).on('click', '.view-btn', function () {
                const id = $(this).data('id');
                $.get(`{{ url('admin/buses') }}/${id}`, function (response) {
                    if (response.success) {
                        const data = response.data;
                        $('#qv-image').attr('src', data.thumbnail_url || placeholder);
                        $('#qv-name').text(data.name);
                        $('#qv-model').text(data.model_name || 'Chưa có thông tin dòng xe');
                        $('#qv-seats').text(data.seat_count + ' ghế');
                        $('#qv-priority').text(data.priority);

                        const services = data.services || [];
                        if (services.length > 0) {
                            let html = services.map(s => `<span class="service-pill">${s}</span>`).join(' ');
                            $('#qv-services').html(html);
                        } else {
                            $('#qv-services').html('<span class="text-muted">Không có</span>');
                        }

                        if (data.content) {
                            $('#qv-content').html(data.content);
                            $('#qv-content-wrapper').show();
                        } else {
                            $('#qv-content-wrapper').hide();
                        }

                        $('#qv-edit-btn').data('id', id);
                        $('#quick-view-modal').modal('show');
                    }
                });
            });

            $('#qv-edit-btn').on('click', function() {
                $('#quick-view-modal').modal('hide');
                $(`.edit-btn[data-id="${$(this).data('id')}"]`).click();
            });

            // ========== Duplicate Bus ==========
            $(document).on('click', '.duplicate-btn', function () {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Nhân bản xe?',
                    text: "Một bản sao của xe này sẽ được tạo",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Nhân bản',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.get(`{{ url('admin/buses') }}/${id}`, function (response) {
                            if (response.success) {
                                resetForm();
                                const data = response.data;
                                $('#bus-modal-label').html('<i class="fas fa-copy mr-2"></i>Nhân bản xe');
                                $('#name').val(data.name + ' (Bản sao)');
                                $('#model_name').val(data.model_name);
                                $('#priority').val(data.priority);

                                const seatMapStr = data.seat_map ? JSON.stringify(data.seat_map, null, 2) : '[]';
                                hiddenSeatMapInput.val(seatMapStr);
                                seatCountInput.val(data.seat_count);
                                renderSeatMapPreview(JSON.stringify(data.seat_map));

                                if (data.services && Array.isArray(data.services)) {
                                    data.services.forEach(service => {
                                        $(`input[name="services[]"][value="${service}"]`).prop('checked', true);
                                    });
                                }
                                if (contentEditor) contentEditor.setData(data.content || '');

                                modal.modal('show');
                            }
                        });
                    }
                });
            });

            // ========== Submit Form ==========
            form.on('submit', function (e) {
                e.preventDefault();
                const id = $('#bus_id').val();
                const url = id ? `{{ url('admin/buses') }}/${id}` : '{{ route("admin.buses.store") }}';

                let formData = new FormData(this);
                if (id) formData.append('_method', 'PUT');
                if (contentEditor) formData.set('content', contentEditor.getData());

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            modal.modal('hide');
                            table.ajax.reload();
                            toastr.success(response.message);
                        }
                    },
                    error: function (xhr) {
                        form.find('.is-invalid').removeClass('is-invalid').next('.invalid-feedback').remove();
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                $('#' + key).addClass('is-invalid').after(`<div class="invalid-feedback d-block">${value[0]}</div>`);
                            });
                            toastr.error('Vui lòng kiểm tra lại thông tin đã nhập.');
                        } else {
                            toastr.error('Đã xảy ra lỗi. Vui lòng thử lại.');
                        }
                    }
                });
            });

            // ========== Delete Bus ==========
            $(document).on('click', '.delete-btn', function () {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Xác nhận xóa xe?',
                    text: "Hành động này không thể hoàn tác!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-trash mr-1"></i> Xóa',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ url('admin/buses') }}/${id}`,
                            method: 'DELETE',
                            success: function (response) {
                                table.ajax.reload();
                                toastr.success(response.message);
                            },
                            error: (xhr) => toastr.error(xhr.responseJSON.message || 'Đã xảy ra lỗi khi xóa.')
                        });
                    }
                });
            });

            // ========== Keyboard Shortcuts ==========
            $(document).on('keydown', function(e) {
                // Ctrl+Shift+N: Add new bus
                if (e.ctrlKey && e.shiftKey && e.key === 'N') {
                    e.preventDefault();
                    $('#btn-add').click();
                }
            });
        });
    </script>
@endpush
</x-admin.layout>
