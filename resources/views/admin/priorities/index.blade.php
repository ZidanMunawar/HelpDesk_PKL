{{-- resources/views/admin/priorities/index.blade.php --}}
@extends('layouts.main')

@section('title', 'Priority Management | ' . config('app.name'))

@section('page-title', 'Priority Management')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'Admin', 'url' => 'javascript:void(0)'],
            ['title' => 'Priority Management', 'url' => 'javascript:void(0)'],
        ];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">

    <style>
        .form-control,
        .form-select,
        textarea.form-control {
            color: #1e1e1e;
            font-weight: 400;
        }

        /* Saat typing/input juga tetep hitam */
        .form-control:focus,
        .form-select:focus,
        textarea.form-control:focus {
            color: #000000 !important;
        }

        /* ===== RESPONSIVE CARD STYLING ===== */
        .priority-card {
            transition: all 0.3s ease;
            border: 1px solid #f0f1f5;
            border-radius: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .priority-card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }

        .priority-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f0f1f5;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .priority-body {
            padding: 1.25rem;
            flex: 1;
        }

        .priority-footer {
            padding: 0.875rem 1.25rem;
            border-top: 1px solid #f0f1f5;
            background-color: #fafbfc;
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        /* ===== PRIORITY BADGE ===== */
        .priority-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 2rem;
            font-weight: 600;
            font-size: 13px;
            color: white;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            white-space: nowrap;
        }

        @media (max-width: 576px) {
            .priority-badge {
                white-space: normal;
                word-break: break-word;
                width: 100%;
                text-align: center;
            }
        }

        .priority-badge-sm {
            padding: 4px 12px;
            font-size: 12px;
        }

        /* ===== LEVEL BADGE ===== */
        .level-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 2rem;
            font-size: 12px;
            font-weight: 500;
            color: white;
            white-space: nowrap;
        }

        @media (max-width: 576px) {
            .level-badge {
                white-space: normal;
                word-break: break-word;
                width: 100%;
                text-align: center;
            }
        }

        /* ===== STATUS BADGE ===== */
        .badge {
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 500;
            border-radius: 0.375rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
        }

        .badge-danger {
            background-color: #dc3545;
            color: white;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .badge-info {
            background-color: #17a2b8;
            color: white;
        }

        /* ===== STATS CARD ===== */
        .stats-card {
            background: linear-gradient(45deg, #667eea, #19005f);
            border-radius: 1rem;
            padding: 1.25rem;
            color: white;
            margin-bottom: 1.5rem;
        }

        @media (min-width: 768px) {
            .stats-card {
                padding: 1.5rem;
            }
        }

        .stats-number {
            font-size: 1.75rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        @media (min-width: 768px) {
            .stats-number {
                font-size: 2rem;
            }
        }

        .stats-label {
            font-size: 0.813rem;
            opacity: 0.9;
            margin-bottom: 0;
        }

        /* ===== ORANGE BUTTON ===== */
        .btn-orange {
            background-color: #fd7e14;
            border-color: #fd7e14;
            color: white;
            padding: 0.5rem 1.25rem;
            border-radius: 0.75rem;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-orange:hover,
        .btn-orange:focus {
            background-color: #e06b00;
            border-color: #e06b00;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(253, 126, 20, 0.3);
        }

        .btn-orange i {
            font-size: 14px;
        }

        /* ===== ACTION BUTTONS - MOBILE FRIENDLY ===== */
        .btn-action {
            width: 36px;
            height: 36px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            flex: 0 0 auto;
        }

        .btn-action i {
            font-size: 15px;
        }

        .btn-action:hover {
            transform: scale(1.1);
        }

        /* Mobile action buttons */
        @media (max-width: 576px) {
            .priority-footer {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 0.5rem;
                padding: 1rem;
            }

            .priority-footer .btn-action {
                width: 100%;
                height: 42px;
                border-radius: 0.5rem;
            }

            .priority-footer .btn-action i {
                font-size: 16px;
            }

            .priority-footer .text-muted {
                grid-column: span 3;
                text-align: center;
                padding: 0.5rem;
            }
        }

        /* ===== COLOR PREVIEW ===== */
        .color-preview {
            width: 35px;
            height: 35px;
            border-radius: 0.75rem;
            border: 2px solid #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
        }

        @media (max-width: 576px) {
            .color-preview {
                width: 32px;
                height: 32px;
            }
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 2.5rem 1rem;
            background: #f8f9fa;
            border-radius: 1rem;
        }

        @media (min-width: 768px) {
            .empty-state {
                padding: 3rem 1.5rem;
            }
        }

        .empty-state i {
            font-size: 2.5rem;
            color: #adb5bd;
            margin-bottom: 1rem;
        }

        @media (min-width: 768px) {
            .empty-state i {
                font-size: 3rem;
            }
        }

        .empty-state h4 {
            color: #495057;
            margin-bottom: 0.5rem;
            font-size: 1.25rem;
        }

        .empty-state p {
            color: #6c757d;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }

        /* ===== RESPONSIVE GRID ===== */
        @media (max-width: 576px) {
            .row {
                margin-left: -0.5rem;
                margin-right: -0.5rem;
            }

            .col-lg-6,
            .col-xl-4 {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
        }

        /* ===== MODAL RESPONSIVE ===== */
        .modal-content {
            border-radius: 1rem;
            border: none;
        }

        .modal-header {
            background: linear-gradient(45deg, #667eea, #0a004a);
            color: white;
            border-radius: 1rem 1rem 0 0;
            padding: 1rem 1.25rem;
        }

        @media (min-width: 768px) {
            .modal-header {
                padding: 1.25rem 1.5rem;
            }
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.9;
            padding: 0.75rem;
            margin: -0.5rem -0.5rem -0.5rem auto;
        }

        .modal-title {
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        @media (min-width: 768px) {
            .modal-title {
                font-size: 1.25rem;
            }
        }

        .modal-body {
            padding: 1.25rem;
        }

        @media (min-width: 768px) {
            .modal-body {
                padding: 1.5rem;
            }
        }

        .modal-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid #f0f1f5;
            display: flex;
            gap: 0.75rem;
        }

        @media (max-width: 576px) {
            .modal-dialog {
                margin: 0.5rem;
            }

            .modal-footer {
                flex-direction: column-reverse;
                gap: 0.5rem;
            }

            .modal-footer .btn {
                width: 100%;
                margin: 0;
                padding: 0.625rem 1rem;
                justify-content: center;
            }

            .modal-body .row.g-3>div {
                margin-bottom: 0.5rem;
            }
        }

        /* ===== FORM STYLING ===== */
        .form-label {
            font-weight: 500;
            color: #333;
            margin-bottom: 0.35rem;
            font-size: 0.813rem;
        }

        @media (min-width: 768px) {
            .form-label {
                font-size: 0.875rem;
                margin-bottom: 0.5rem;
            }
        }

        .form-control,
        .form-select {
            border-radius: 0.75rem;
            border: 1px solid #f0f1f5;
            padding: 0.5rem 0.875rem;
            font-size: 0.813rem;
            height: auto;
        }

        @media (min-width: 768px) {

            .form-control,
            .form-select {
                padding: 0.625rem 1rem;
                font-size: 0.875rem;
            }
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.1);
        }

        .input-group {
            flex-wrap: nowrap;
        }

        .input-group .form-control-color {
            width: 50px;
            height: 38px;
            padding: 0.25rem;
            flex-shrink: 0;
        }

        @media (max-width: 576px) {
            .input-group .form-control-color {
                width: 45px;
                height: 42px;
            }
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }

        .invalid-feedback {
            display: block;
            font-size: 0.688rem;
            margin-top: 0.25rem;
            color: #dc3545;
        }

        /* ===== PREVIEW SECTION ===== */
        .preview-section {
            background-color: #f8f9fa;
            border-radius: 0.75rem;
            padding: 1rem;
        }

        /* ===== TYPOGRAPHY FIXES ===== */
        small.text-muted {
            font-size: 0.688rem;
        }

        @media (min-width: 768px) {
            small.text-muted {
                font-size: 0.75rem;
            }
        }

        code {
            font-size: 0.75rem;
        }

        /* ===== ADDITIONAL MOBILE FIXES ===== */
        @media (max-width: 576px) {
            .priority-header {
                flex-direction: column;
                align-items: stretch;
            }

            .priority-header>div:first-child,
            .priority-header>div:last-child {
                width: 100%;
            }

            .d-flex.align-items-center {
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .me-3:last-child {
                margin-right: 0 !important;
            }

            .badge {
                padding: 5px 8px;
                font-size: 11px;
            }
        }

        /* Fix for very small devices */
        @media (max-width: 375px) {
            .priority-footer {
                grid-template-columns: 1fr;
            }

            .priority-footer .btn-action {
                height: 44px;
            }

            .priority-footer .text-muted {
                grid-column: span 1;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Add Priority Modal -->
    <div class="modal fade" id="addPriorityModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="color: white;">
                        <i class="fas fa-flag me-2" style="color: white;"></i>
                        Add New Priority
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addPriorityForm" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Priority Name -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    Priority Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" name="name" placeholder="e.g., Critical"
                                    required>
                                <div class="invalid-feedback"></div>
                                <small class="text-muted">Nama tingkat prioritas</small>
                            </div>

                            <!-- Level -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    Level <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" name="level" required>
                                    <option value="">-- Select Level --</option>
                                    <option value="1">Level 1: Lowest</option>
                                    <option value="2">Level 2: Low</option>
                                    <option value="3">Level 3: Medium</option>
                                    <option value="4">Level 4: High</option>
                                    <option value="5">Level 5: Highest</option>
                                </select>
                                <div class="invalid-feedback"></div>
                                <small class="text-muted">Tingkat prioritas (1-5)</small>
                            </div>

                            <!-- Color -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    Color <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" name="color"
                                        value="#007bff" style="max-width: 60px; padding: 0.25rem;">
                                    <input type="text" class="form-control" name="color_text" id="add_color_text"
                                        value="#007bff" placeholder="#RRGGBB" maxlength="7" pattern="^#[a-fA-F0-9]{6}$">
                                </div>
                                <div class="invalid-feedback"></div>
                                <small class="text-muted">Pilih warna atau masukkan kode hex</small>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    Status <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" name="status" required>
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- Preview -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <label class="form-label">Preview</label>
                                <div class="preview-section">
                                    <span id="preview_badge" class="priority-badge" style="background-color: #007bff;">
                                        Priority Name
                                    </span>
                                    <span id="preview_level" class="level-badge ms-2" style="background-color: #28a745;">
                                        Level 1: Lowest
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-orange">
                            <i class="fas fa-save me-1"></i> Save Priority
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Priority Modal -->
    <div class="modal fade" id="editPriorityModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="color: white;">
                        <i class="fas fa-edit me-2" style="color: white;"></i>
                        Edit Priority
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editPriorityForm" novalidate>
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_priority_id" name="id">
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Priority Name -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    Priority Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Level -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    Level <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="edit_level" name="level" required>
                                    <option value="">-- Select Level --</option>
                                    <option value="1">Level 1: Lowest</option>
                                    <option value="2">Level 2: Low</option>
                                    <option value="3">Level 3: Medium</option>
                                    <option value="4">Level 4: High</option>
                                    <option value="5">Level 5: Highest</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Color -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    Color <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" id="edit_color_picker"
                                        name="color" style="max-width: 60px; padding: 0.25rem;">
                                    <input type="text" class="form-control" id="edit_color_text" name="color_text"
                                        placeholder="#RRGGBB" maxlength="7" pattern="^#[a-fA-F0-9]{6}$">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    Status <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="edit_status" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- Preview -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <label class="form-label">Preview</label>
                                <div class="preview-section">
                                    <span id="edit_preview_badge" class="priority-badge"
                                        style="background-color: #007bff;">
                                        Priority Name
                                    </span>
                                    <span id="edit_preview_level" class="level-badge ms-2"
                                        style="background-color: #28a745;">
                                        Level 1: Lowest
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-orange">
                            <i class="fas fa-save me-1"></i> Update Priority
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="stats-card">
                <div class="row align-items-center g-3">
                    <div class="col-7 col-md-6">
                        <div class="stats-number">{{ $priorities->count() }}</div>
                        <div class="stats-label">Total Priority Levels</div>
                    </div>
                    <div class="col-5 col-md-6 text-end">
                        @if (auth()->user()->role === 'superadmin')
                            <button type="button" class="btn btn-orange" data-bs-toggle="modal"
                                data-bs-target="#addPriorityModal">
                                <i class="fas fa-plus-circle me-2"></i>
                                <span class="d-none d-sm-inline">Add New Priority</span>
                                <span class="d-sm-none">Add</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        @forelse ($priorities as $priority)
            <div class="col-lg-6 col-xl-4">
                <div class="priority-card">
                    <div class="priority-header">
                        <div class="w-100 w-sm-auto">
                            <span class="priority-badge" style="background-color: {{ $priority->color }};">
                                <i class="fas fa-flag me-1"></i>
                                {{ $priority->name }}
                            </span>
                        </div>
                        <div class="w-100 w-sm-auto">
                            <span class="level-badge" style="background-color: {{ $priority->level_color }};">
                                Level {{ $priority->level }}: {{ $priority->level_label }}
                            </span>
                        </div>
                    </div>
                    <div class="priority-body">
                        <div class="row g-3">
                            <div class="col-7 col-sm-6">
                                <small class="text-muted d-block mb-2">Color Code</small>
                                <div class="d-flex align-items-center">
                                    <div class="color-preview me-2" style="background-color: {{ $priority->color }};">
                                    </div>
                                    <code class="text-muted">{{ $priority->color }}</code>
                                </div>
                            </div>
                            <div class="col-5 col-sm-6">
                                <small class="text-muted d-block mb-2">Status</small>
                                <span class="badge badge-{{ $priority->status_badge_color }}">
                                    <i
                                        class="fas fa-{{ $priority->status === 'active' ? 'check-circle' : 'minus-circle' }} me-1"></i>
                                    {{ ucfirst($priority->status) }}
                                </span>
                            </div>
                            <div class="col-12 mt-3">
                                <small class="text-muted d-block mb-2">Usage Statistics</small>
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <div>
                                        <span class="badge badge-info">
                                            <i class="fas fa-ticket-alt me-1"></i>
                                            {{ $priority->tickets_count ?? 0 }} Tickets
                                        </span>
                                    </div>
                                    <div class="text-muted small">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        {{ $priority->created_at ? $priority->created_at->format('d M Y') : '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if (auth()->user()->role === 'superadmin')
                        <div class="priority-footer">
                            <button type="button" class="btn btn-sm btn-primary btn-action edit-priority"
                                data-id="{{ $priority->id }}" data-name="{{ $priority->name }}"
                                data-color="{{ $priority->color }}" data-level="{{ $priority->level }}"
                                data-status="{{ $priority->status }}" title="Edit Priority">
                                <i class="fas fa-pencil-alt"></i>
                            </button>

                            <button type="button"
                                class="btn btn-sm btn-{{ $priority->status === 'active' ? 'warning' : 'success' }} btn-action toggle-status"
                                data-id="{{ $priority->id }}" data-name="{{ $priority->name }}"
                                data-status="{{ $priority->status }}"
                                title="{{ $priority->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                <i class="fas fa-{{ $priority->status === 'active' ? 'ban' : 'check' }}"></i>
                            </button>

                            <button type="button" class="btn btn-sm btn-danger btn-action delete-priority"
                                data-id="{{ $priority->id }}" data-name="{{ $priority->name }}"
                                data-tickets-count="{{ $priority->tickets_count ?? 0 }}" title="Delete Priority">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    @else
                        <div class="priority-footer">
                            <span class="text-muted">No actions available</span>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state">
                    <i class="fas fa-flag"></i>
                    <h4>No Priorities Found</h4>
                    <p>Get started by creating your first priority level.</p>
                    @if (auth()->user()->role === 'superadmin')
                        <button type="button" class="btn btn-orange" data-bs-toggle="modal"
                            data-bs-target="#addPriorityModal">
                            <i class="fas fa-plus-circle me-2"></i>
                            Add New Priority
                        </button>
                    @endif
                </div>
            </div>
        @endforelse
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Toastr configuration
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            // Level color mapping
            const levelColors = {
                1: '#28a745', // Green
                2: '#17a2b8', // Blue
                3: '#ffc107', // Yellow
                4: '#fd7e14', // Orange
                5: '#dc3545' // Red
            };

            const levelLabels = {
                1: 'Level 1: Lowest',
                2: 'Level 2: Low',
                3: 'Level 3: Medium',
                4: 'Level 4: High',
                5: 'Level 5: Highest'
            };

            // ============ ADD FORM HANDLING ============

            // Sync color picker and text input
            $('input[name="color"]').on('input', function() {
                var color = $(this).val();
                $('#add_color_text').val(color);
                updateAddPreview();
            });

            $('#add_color_text').on('input', function() {
                var color = $(this).val();
                if (/^#[0-9A-F]{6}$/i.test(color)) {
                    $('input[name="color"]').val(color);
                    updateAddPreview();
                }
            });

            // Update preview when name changes
            $('input[name="name"]').on('input', function() {
                updateAddPreview();
            });

            // Update preview when level changes
            $('select[name="level"]').on('change', function() {
                updateAddPreview();
            });

            function updateAddPreview() {
                var name = $('input[name="name"]').val() || 'Priority Name';
                var color = $('input[name="color"]').val() || '#007bff';
                var level = $('select[name="level"]').val();

                $('#preview_badge').text(name).css('background-color', color);

                if (level) {
                    var levelColor = levelColors[level] || '#6c757d';
                    var levelLabel = levelLabels[level] || 'Level ' + level;
                    $('#preview_level').text(levelLabel).css('background-color', levelColor).show();
                } else {
                    $('#preview_level').hide();
                }
            }

            // ============ EDIT FORM HANDLING ============

            // Sync color picker and text input for edit
            $('#edit_color_picker').on('input', function() {
                var color = $(this).val();
                $('#edit_color_text').val(color);
                updateEditPreview();
            });

            $('#edit_color_text').on('input', function() {
                var color = $(this).val();
                if (/^#[0-9A-F]{6}$/i.test(color)) {
                    $('#edit_color_picker').val(color);
                    updateEditPreview();
                }
            });

            // Update edit preview when name changes
            $('#edit_name').on('input', function() {
                updateEditPreview();
            });

            // Update edit preview when level changes
            $('#edit_level').on('change', function() {
                updateEditPreview();
            });

            function updateEditPreview() {
                var name = $('#edit_name').val() || 'Priority Name';
                var color = $('#edit_color_picker').val() || '#007bff';
                var level = $('#edit_level').val();

                $('#edit_preview_badge').text(name).css('background-color', color);

                if (level) {
                    var levelColor = levelColors[level] || '#6c757d';
                    var levelLabel = levelLabels[level] || 'Level ' + level;
                    $('#edit_preview_level').text(levelLabel).css('background-color', levelColor).show();
                } else {
                    $('#edit_preview_level').hide();
                }
            }

            // ============ ADD PRIORITY ============
            $('#addPriorityForm').on('submit', function(e) {
                e.preventDefault();

                var formData = {
                    _token: '{{ csrf_token() }}',
                    name: $('input[name="name"]').val(),
                    color: $('input[name="color"]').val(),
                    level: $('select[name="level"]').val(),
                    status: $('select[name="status"]').val()
                };

                var submitBtn = $(this).find('button[type="submit"]');
                var originalText = submitBtn.html();

                submitBtn.prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin me-1"></i>Saving...');

                $.ajax({
                    url: "{{ route('admin.priorities.store') }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        submitBtn.prop('disabled', false).html(originalText);

                        if (response.success) {
                            $('#addPriorityModal').modal('hide');
                            $('#addPriorityForm')[0].reset();
                            $('.form-control, .form-select').removeClass('is-invalid');
                            $('.invalid-feedback').text('');

                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(originalText);

                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $('.form-control, .form-select').removeClass('is-invalid');
                            $('.invalid-feedback').text('');

                            $.each(errors, function(key, value) {
                                var input = $('[name="' + key + '"]',
                                    '#addPriorityForm');
                                input.addClass('is-invalid');
                                input.siblings('.invalid-feedback').text(value[0]);

                                if (input.next('.invalid-feedback').length === 0) {
                                    input.closest('.input-group').after(
                                        '<div class="invalid-feedback">' + value[
                                            0] + '</div>');
                                }
                            });

                            toastr.error('Please check the form for errors');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: xhr.responseJSON?.message ||
                                    'An error occurred. Please try again.'
                            });
                        }
                    }
                });
            });

            // ============ EDIT PRIORITY ============
            $(document).on('click', '.edit-priority', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var color = $(this).data('color');
                var level = $(this).data('level');
                var status = $(this).data('status');

                $('#edit_priority_id').val(id);
                $('#edit_name').val(name);
                $('#edit_color_picker').val(color);
                $('#edit_color_text').val(color);
                $('#edit_level').val(level);
                $('#edit_status').val(status);

                $('.form-control, .form-select').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                updateEditPreview();
                $('#editPriorityModal').modal('show');
            });

            $('#editPriorityForm').on('submit', function(e) {
                e.preventDefault();

                var priorityId = $('#edit_priority_id').val();

                var formData = {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT',
                    name: $('#edit_name').val(),
                    color: $('#edit_color_picker').val(),
                    level: $('#edit_level').val(),
                    status: $('#edit_status').val()
                };

                var submitBtn = $(this).find('button[type="submit"]');
                var originalText = submitBtn.html();

                submitBtn.prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin me-1"></i>Updating...');

                $.ajax({
                    url: "{{ route('admin.priorities.update', ':id') }}".replace(':id',
                        priorityId),
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        submitBtn.prop('disabled', false).html(originalText);

                        if (response.success) {
                            $('#editPriorityModal').modal('hide');
                            $('#editPriorityForm')[0].reset();
                            $('.form-control, .form-select').removeClass('is-invalid');
                            $('.invalid-feedback').text('');

                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(originalText);

                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $('.form-control, .form-select').removeClass('is-invalid');
                            $('.invalid-feedback').text('');

                            $.each(errors, function(key, value) {
                                var input = $('#edit_' + key);
                                if (input.length === 0) {
                                    input = $('[name="' + key + '"]',
                                        '#editPriorityForm');
                                }
                                input.addClass('is-invalid');

                                if (input.siblings('.invalid-feedback').length) {
                                    input.siblings('.invalid-feedback').text(value[0]);
                                } else {
                                    input.closest('.input-group').after(
                                        '<div class="invalid-feedback">' + value[
                                            0] + '</div>');
                                }
                            });

                            toastr.error('Please check the form for errors');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: xhr.responseJSON?.message ||
                                    'An error occurred. Please try again.'
                            });
                        }
                    }
                });
            });

            // ============ TOGGLE STATUS ============
            $(document).on('click', '.toggle-status', function() {
                var priorityId = $(this).data('id');
                var priorityName = $(this).data('name');
                var currentStatus = $(this).data('status');
                var newStatus = currentStatus === 'active' ? 'inactive' : 'active';

                var statusColors = {
                    'active': '#28a745',
                    'inactive': '#dc3545'
                };

                var statusText = {
                    'active': 'Active',
                    'inactive': 'Inactive'
                };

                Swal.fire({
                    title: 'Change Priority Status?',
                    html: `Are you sure you want to change <strong>${priorityName}</strong>'s status from
                          <span style="color: ${statusColors[currentStatus]}">${statusText[currentStatus]}</span>
                          to <span style="color: ${statusColors[newStatus]}">${statusText[newStatus]}</span>?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, change it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Processing...',
                            html: 'Please wait',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: "{{ route('admin.priorities.toggle-status', ':id') }}"
                                .replace(':id', priorityId),
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: response.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: response.message
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: xhr.responseJSON?.message ||
                                        'An error occurred. Please try again.'
                                });
                            }
                        });
                    }
                });
            });

            // ============ DELETE PRIORITY ============
            $(document).on('click', '.delete-priority', function() {
                var priorityId = $(this).data('id');
                var priorityName = $(this).data('name');
                var ticketsCount = $(this).data('tickets-count');

                var warningMessage = `You are about to delete <strong>${priorityName}</strong> priority.`;
                if (ticketsCount > 0) {
                    warningMessage +=
                        `<br><span class="text-danger">This priority is used in ${ticketsCount} ticket(s). Deleting it may affect these tickets.</span>`;
                }
                warningMessage += `<br><span class="text-warning">This action cannot be undone!</span>`;

                Swal.fire({
                    title: 'Are you sure?',
                    html: warningMessage,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Deleting...',
                            html: 'Please wait',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: "{{ route('admin.priorities.destroy', ':id') }}".replace(
                                ':id', priorityId),
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: response.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Cannot Delete!',
                                        text: response.message
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: xhr.responseJSON?.message ||
                                        'An error occurred. Please try again.'
                                });
                            }
                        });
                    }
                });
            });

            // ============ MODAL RESET ============
            $('#addPriorityModal, #editPriorityModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $('.form-control, .form-select').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                // Reset add form preview
                if ($(this).attr('id') === 'addPriorityModal') {
                    $('input[name="color"]').val('#007bff');
                    $('#add_color_text').val('#007bff');
                    updateAddPreview();
                }
            });

            // Auto-focus on first input when modal opens
            $('#addPriorityModal').on('shown.bs.modal', function() {
                $(this).find('input[name="name"]').focus();
            });

            $('#editPriorityModal').on('shown.bs.modal', function() {
                $(this).find('#edit_name').focus();
            });

            // Initialize add form preview
            updateAddPreview();
        });
    </script>
@endpush
