@extends('layouts.main')

@section('title', 'Maintenance Requests | ' . config('app.name'))

@section('page-title', 'Maintenance Requests')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'Maintenance Requests', 'url' => 'javascript:void(0)'],
            ['title' => 'All Requests', 'url' => 'javascript:void(0)'],
        ];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        /* ========== HEADER ACTIONS ========== */
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .page-title-section h4 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            color: #333;
        }

        .page-title-section h4 i {
            color: #ff6200;
            margin-right: 8px;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        /* ========== MODERN BUTTON STYLES ========== */
        .btn-modern {
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border: none;
            outline: none;
            position: relative;
            overflow: hidden;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .btn-modern::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-modern:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-modern:active {
            transform: scale(0.95);
        }

        /* Create Button */
        .btn-create {
            background: linear-gradient(135deg, #ff7b00, #ff6200);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 98, 0, 0.3);
            text-decoration: none;
        }

        .btn-create:hover {
            background: linear-gradient(135deg, #ff6200, #ff7b00);
            box-shadow: 0 6px 20px rgba(255, 98, 0, 0.4);
            transform: translateY(-2px);
            color: white;
        }

        /* ========== FLOATING ACTION BUTTON (FAB) ========== */
        .fab-export {
            position: fixed;
            bottom: 80px;
            right: 30px;
            z-index: 1000;
        }

        .fab-button {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff7b00, #ff6200);
            color: white;
            border: none;
            box-shadow: 0 4px 15px rgba(255, 98, 0, 0.4);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            position: relative;
        }

        .fab-button:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(255, 98, 0, 0.5);
        }

        .fab-button i {
            font-size: 24px;
        }

        .fab-menu {
            position: absolute;
            bottom: 70px;
            right: 0;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            min-width: 180px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s ease;
            overflow: hidden;
            z-index: 1001;
        }

        .fab-export:hover .fab-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .fab-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            text-decoration: none;
            color: #333;
            font-size: 14px;
            transition: all 0.2s ease;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
        }

        .fab-menu a:last-child {
            border-bottom: none;
        }

        .fab-menu a:hover {
            background: #fff3e0;
            padding-left: 25px;
        }

        .fab-menu a i {
            width: 20px;
            font-size: 16px;
        }

        .fab-menu a:first-child i {
            color: #27ae60;
        }

        .fab-menu a:last-child i {
            color: #e74c3c;
        }

        /* ========== SEARCH & FILTER SECTION ========== */
        .search-filter-wrapper {
            background: white;
            border-radius: 12px;
            border: 1px solid #eaeaea;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        /* Search Bar */
        .search-bar {
            padding: 12px 16px;
            border-bottom: 1px solid #f0f0f0;
        }

        .search-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .search-input-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 0 15px;
            transition: all 0.3s ease;
        }

        .search-input-wrapper:focus-within {
            border-color: #ff6200;
            box-shadow: 0 0 0 3px rgba(255, 98, 0, 0.1);
        }

        .search-input-wrapper i {
            color: #999;
            font-size: 14px;
        }

        .search-input {
            flex: 1;
            border: none;
            padding: 12px 10px;
            font-size: 14px;
            background: transparent;
        }

        .search-input:focus {
            outline: none;
        }

        .search-btn {
            background: #ff6200;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .search-btn:hover {
            background: #ff7b00;
            transform: translateY(-1px);
        }

        /* Filter Header */
        .filter-header {
            padding: 12px 16px;
            background: #f8f9fa;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.2s ease;
        }

        .filter-header:hover {
            background: #f0f0f0;
        }

        .filter-header-left {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 500;
            color: #555;
        }

        .filter-header-left i {
            color: #ff6200;
        }

        .filter-header-arrow {
            transition: transform 0.3s ease;
            color: #999;
        }

        .filter-header.collapsed .filter-header-arrow {
            transform: rotate(0deg);
        }

        .filter-header:not(.collapsed) .filter-header-arrow {
            transform: rotate(180deg);
        }

        .filter-body {
            padding: 16px;
            border-top: 1px solid #f0f0f0;
            transition: all 0.3s ease;
        }

        .filter-body.collapsed {
            display: none;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .filter-item {
            width: 100%;
        }

        .filter-label {
            font-size: 12px;
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
            display: block;
        }

        .filter-select,
        .filter-input {
            width: 100%;
            height: 40px;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 13px;
            transition: all 0.2s ease;
            background-color: white;
        }

        .filter-select:focus,
        .filter-input:focus {
            border-color: #ff6200;
            box-shadow: 0 0 0 2px rgba(255, 98, 0, 0.1);
            outline: none;
        }

        /* Select2 Customization */
        .select2-container--default .select2-selection--single {
            height: 40px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
            padding-left: 12px;
            font-size: 13px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
            right: 8px;
        }

        .select2-container--default .select2-selection--multiple {
            min-height: 40px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        /* Filter Actions - Horizontal scroll di mobile */
        .filter-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 5px;
            overflow-x: auto;
            flex-wrap: nowrap;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 4px;
        }

        .filter-actions::-webkit-scrollbar {
            display: none;
        }

        .btn-filter,
        .btn-reset {
            flex-shrink: 0;
            white-space: nowrap;
        }

        .btn-filter {
            background: #ff6200;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            height: 40px;
        }

        .btn-filter:hover {
            background: #ff7b00;
            transform: translateY(-1px);
        }

        .btn-reset {
            background: #f5f5f5;
            color: #666;
            border: 1px solid #ddd;
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            height: 40px;
        }

        .btn-reset:hover {
            background: #ff6200;
            border-color: #ff6200;
            color: white;
        }

        /* Loading Overlay */
        .ticket-loading {
            position: relative;
            min-height: 200px;
        }

        .ticket-loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            z-index: 10;
            border-radius: 12px;
        }

        .ticket-loading::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40px;
            height: 40px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #ff6200;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            z-index: 11;
        }

        @keyframes spin {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        /* ========== TICKET CARDS ========== */
        .ticket-list {
            background: transparent;
        }

        .ticket-card {
            background: white;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 12px;
            border: 1px solid #eaeaea;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .ticket-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255, 98, 0, 0.15);
            border-color: #ff6200;
        }

        .ticket-card:hover::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: #ff6200;
        }

        .ticket-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .ticket-number {
            font-size: 12px;
            color: #ff6200;
            font-weight: 600;
            background: #fff3e0;
            padding: 4px 10px;
            border-radius: 20px;
            display: inline-block;
        }

        .ticket-title {
            font-size: 15px;
            color: #333;
            font-weight: 500;
            margin: 10px 0;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .ticket-meta {
            font-size: 12px;
            color: #666;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 10px;
        }

        .ticket-meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .ticket-meta-item i {
            font-size: 12px;
            color: #999;
        }

        .ticket-footer {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            padding-top: 12px;
            border-top: 1px dashed #eee;
            margin-top: 8px;
        }

        /* Badges */
        .badge-sm {
            padding: 4px 8px;
            font-size: 11px;
            font-weight: 500;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .badge-assigned {
            background: #e3f2fd;
            color: #1565c0;
            border: 1px solid #bbdefb;
        }

        .badge-date {
            background: #fff3e0;
            color: #ef6c00;
            border: 1px solid #ffe0b2;
        }

        /* Status Badges */
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .status-open {
            background: #e3f2fd;
            color: #1565c0;
            border: 1px solid #bbdefb;
        }

        .status-received {
            background: #e3f2fd;
            color: #1565c0;
            border: 1px solid #bbdefb;
        }

        .status-pending_om {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-in_progress {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .status-pending_vr {
            background: #fff8e1;
            color: #ff8f00;
            border: 1px solid #ffe082;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-pending_gm {
            background: #e8f4fd;
            color: #0d47a1;
            border: 1px solid #bbdefb;
        }

        .status-ready_for_closure {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .status-closed {
            background: #e9ecef;
            color: #495057;
            border: 1px solid #dee2e6;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Priority Badges */
        .priority-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 10px;
            color: white !important;
            text-transform: uppercase;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 12px;
            border: 1px solid #eaeaea;
        }

        .empty-state-icon {
            font-size: 60px;
            color: #e0e0e0;
            margin-bottom: 20px;
        }

        .empty-state-title {
            font-size: 18px;
            color: #666;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .empty-state-text {
            font-size: 14px;
            color: #999;
            margin-bottom: 25px;
        }

        .empty-state-buttons {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* Pagination */
        .pagination-wrapper {
            margin-top: 25px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 12px;
            display: flex;
            justify-content: center;
        }

        .pagination {
            margin: 0;
            flex-wrap: wrap;
            gap: 5px;
        }

        .pagination .page-link {
            font-size: 13px;
            padding: 8px 14px;
            border-color: #ddd;
            color: #666;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .pagination .page-item.active .page-link {
            background: #ff6200;
            border-color: #ff6200;
            color: white;
        }

        .pagination .page-link:hover {
            background: #ff7b00;
            border-color: #ff7b00;
            color: white;
        }

        /* ========== MODAL TICKET INFO (SAMA KAYAK CALENDAR) ========== */
        .ticket-info-item {
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .ticket-info-item:last-child {
            border-bottom: none;
        }

        .ticket-info-label {
            font-size: 10px;
            color: #999;
            margin-bottom: 3px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .ticket-info-value {
            font-size: 13px;
            color: #333;
            font-weight: 500;
        }

        .modal-warning {
            background: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            padding: 10px;
            border-radius: 8px;
            margin-top: 12px;
            font-size: 11px;
        }

        .modal-header {
            background: #ff6200 !important;
            color: white !important;
            border-bottom: none;
        }

        .modal-header .modal-title {
            color: white !important;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-actions {
                flex-direction: column;
                align-items: flex-start;
            }

            .action-buttons {
                width: 100%;
            }

            .btn-modern {
                flex: 1;
                justify-content: center;
            }

            .search-form {
                flex-direction: row;
                gap: 8px;
            }

            .search-input-wrapper {
                flex: 1;
                padding: 0 12px;
            }

            .search-btn {
                padding: 10px 16px;
                white-space: nowrap;
            }

            .search-btn span {
                display: none;
            }

            .search-btn i {
                margin: 0;
            }

            .filter-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .ticket-meta {
                overflow-x: auto;
                flex-wrap: nowrap;
                padding-bottom: 4px;
            }

            .ticket-meta::-webkit-scrollbar {
                display: none;
            }

            .ticket-meta-item {
                flex-shrink: 0;
            }

            .fab-export {
                bottom: 70px;
                right: 16px;
            }

            .fab-button {
                width: 48px;
                height: 48px;
            }

            .fab-button i {
                font-size: 20px;
            }
        }

        @media (max-width: 480px) {
            .ticket-card {
                padding: 12px;
            }

            .ticket-title {
                font-size: 14px;
            }

            .ticket-meta {
                gap: 10px;
            }

            .ticket-meta-item {
                font-size: 11px;
            }

            .fab-export {
                bottom: 60px;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Header dengan Tombol Create -->
    <div class="header-actions">
        <div class="page-title-section">
            <h4><i class="fas fa-clipboard-list"></i>Maintenance Request</h4>
        </div>
        <div class="action-buttons">
            @if (in_array(auth()->user()->role, ['admin_eng', 'user', 'manager']))
                <a href="{{ route('tickets.create') }}" class="btn-modern btn-create">
                    <i class="fas fa-plus-circle"></i> New MR
                </a>
            @endif
        </div>
    </div>

    <!-- Search & Filter Section -->
    <div class="search-filter-wrapper">
        <!-- Search Bar -->
        <div class="search-bar">
            <form action="{{ route('tickets.index') }}" method="GET" id="searchForm" class="ajax-filter-form search-form">
                <div class="search-input-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="search-input"
                        placeholder="Search by MR number, title, user, or location..." value="{{ request('search') }}"
                        autocomplete="off">
                </div>
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i>
                    <span>Search</span>
                </button>
            </form>
        </div>

        <!-- Filter Header -->
        <div class="filter-header {{ request()->anyFilled(['status', 'category', 'priority', 'department', 'date_from', 'date_to']) ? '' : 'collapsed' }}"
            id="filterToggle">
            <div class="filter-header-left">
                <i class="fas fa-filter"></i>
                <span>Advanced Filters</span>
                @if (request()->anyFilled(['status', 'category', 'priority', 'department', 'date_from', 'date_to']))
                    <span class="badge bg-warning text-dark ms-2" style="font-size: 10px;">Active</span>
                @endif
            </div>
            <i class="fas fa-chevron-down filter-header-arrow"></i>
        </div>

        <!-- Filter Body -->
        <div class="filter-body {{ request()->anyFilled(['status', 'category', 'priority', 'department', 'date_from', 'date_to']) ? '' : 'collapsed' }}"
            id="filterBody">
            <form action="{{ route('tickets.index') }}" method="GET" id="filterForm" class="ajax-filter-form">
                <input type="hidden" name="search" value="{{ request('search') }}">

                <div class="filter-grid">
                    <div class="filter-item">
                        <label class="filter-label">Status</label>
                        <select name="status" class="filter-select">
                            <option value="">All Status</option>
                            @foreach ($statusOptions as $value => $label)
                                @if ($value)
                                    <option value="{{ $value }}"
                                        {{ request('status') == $value ? 'selected' : '' }}>
                                        {{ $value == 'pending_vr' ? 'PR Approval' : $label }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-item">
                        <label class="filter-label">Priority</label>
                        <select name="priority[]" class="filter-select select2-multiple" multiple="multiple">
                            @foreach ($priorities as $priority)
                                <option value="{{ $priority->id }}"
                                    {{ in_array($priority->id, (array) request('priority', [])) ? 'selected' : '' }}>
                                    {{ $priority->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-item">
                        <label class="filter-label">Category</label>
                        <select name="category[]" class="filter-select select2-multiple" multiple="multiple">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ in_array($category->id, (array) request('category', [])) ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-item">
                        <label class="filter-label">Department</label>
                        <select name="department[]" class="filter-select select2-multiple" multiple="multiple">
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}"
                                    {{ in_array($department->id, (array) request('department', [])) ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-item">
                        <label class="filter-label">Date From</label>
                        <input type="date" name="date_from" class="filter-input" value="{{ request('date_from') }}">
                    </div>

                    <div class="filter-item">
                        <label class="filter-label">Date To</label>
                        <input type="date" name="date_to" class="filter-input" value="{{ request('date_to') }}">
                    </div>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn-filter">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <button type="button" class="btn-reset" id="resetFiltersBtn">
                        <i class="fas fa-redo-alt"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TICKET CARDS CONTAINER -->
    <div id="ticket-list-container">
        @include('tickets.partials.ticket-cards', ['tickets' => $tickets])
    </div>

    <!-- FLOATING EXPORT BUTTON (FAB) -->
    <div class="fab-export">
        <button class="fab-button" id="fabExportBtn">
            <i class="fas fa-download"></i>
        </button>
        <div class="fab-menu">
            <a href="#" onclick="exportTickets('csv'); return false;">
                <i class="fas fa-file-csv"></i> Export CSV
            </a>
            <a href="#" onclick="exportTickets('pdf'); return false;">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- ==================== MODAL TICKET INFO (ACCESS DENIED) ==================== -->
    <div class="modal fade" id="ticketInfoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-tools me-2"></i>Maintenance Request Information
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="ticket-info-content"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2-multiple').select2({
                width: '100%',
                placeholder: 'Select options',
                allowClear: true,
                closeOnSelect: false
            });

            // Filter toggle
            const filterBody = $('#filterBody');
            const filterToggle = $('#filterToggle');

            filterToggle.on('click', function() {
                filterBody.toggleClass('collapsed');
                $(this).toggleClass('collapsed');
            });

            // Toastr config
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "3000"
            };

            // AJAX Filter
            $('.ajax-filter-form').on('submit', function(e) {
                e.preventDefault();
                applyFilters();
            });

            // Reset Filters
            $('#resetFiltersBtn').on('click', function() {
                $('#filterForm')[0].reset();
                $('.select2-multiple').val(null).trigger('change');
                $('input[name="date_from"]').val('');
                $('input[name="date_to"]').val('');
                applyFilters();
            });

            // Pagination
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');
                fetchTickets(url);
            });

            function applyFilters() {
                let formData = $('#filterForm').serialize();
                let searchData = $('#searchForm').serialize();
                let params = new URLSearchParams(formData + '&' + searchData);
                let url = '{{ route('tickets.index') }}?' + params.toString();
                fetchTickets(url);
            }

            function fetchTickets(url) {
                $('#ticket-list-container').addClass('ticket-loading');

                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        let tempDiv = $('<div>').html(response);
                        let newContent = tempDiv.find('#ticket-list-container').html();
                        if (newContent) {
                            $('#ticket-list-container').html(newContent);
                        } else {
                            $('#ticket-list-container').html(response);
                        }
                        window.history.pushState({}, '', url);
                        $('.select2-multiple').select2({
                            width: '100%',
                            placeholder: 'Select options',
                            allowClear: true,
                            closeOnSelect: false
                        });
                    },
                    error: function(xhr) {
                        toastr.error('Failed to load maintenance requests.');
                    },
                    complete: function() {
                        $('#ticket-list-container').removeClass('ticket-loading');
                    }
                });
            }

            window.onpopstate = function() {
                location.reload();
            };
        });

        // Export function
        function exportTickets(format) {
            let url = '{{ route('tickets.export') }}?export=' + format;
            let params = new URLSearchParams(window.location.search);
            params.delete('page');
            url += '&' + params.toString();
            window.location.href = url;
        }

        // Helper function untuk status badge color
        function getStatusBadgeColor(status) {
            const colors = {
                'open': 'primary',
                'received': 'info',
                'pending_om': 'warning',
                'in_progress': 'info',
                'pending_vr': 'warning',
                'completed': 'success',
                'pending_gm': 'warning',
                'ready_for_closure': 'info',
                'closed': 'dark',
                'cancelled': 'danger'
            };
            return colors[status?.toLowerCase()] || 'secondary';
        }

        // Helper function untuk status display name
        function getStatusDisplayName(status) {
            const names = {
                'open': 'Open',
                'received': 'Received',
                'pending_om': 'OM Approval',
                'in_progress': 'In Progress',
                'pending_vr': 'PR Approval',
                'completed': 'Completed',
                'pending_gm': 'GM Approval',
                'ready_for_closure': 'Ready for Closure',
                'closed': 'Closed',
                'cancelled': 'Cancelled'
            };
            return names[status] || status;
        }

        // Helper function untuk escape HTML
        function escapeHtml(text) {
            if (!text) return '';
            let div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Show modal info (access denied) - SAMA PERSIS KAYAK CALENDAR
        function showTicketInfoModal(ticketInfo) {
            let statusColor = getStatusBadgeColor(ticketInfo.status);
            let displayStatus = getStatusDisplayName(ticketInfo.status);
            let priorityColor = ticketInfo.priority_color || '#003366';

            let modalContent = `
                <div class="ticket-info-item">
                    <div class="ticket-info-label">Request Number</div>
                    <div class="ticket-info-value">
                        <strong>${escapeHtml(ticketInfo.number)}</strong>
                        <span class="badge bg-${statusColor} ms-2">${escapeHtml(displayStatus)}</span>
                    </div>
                </div>
                <div class="ticket-info-item">
                    <div class="ticket-info-label">Title</div>
                    <div class="ticket-info-value">${escapeHtml(ticketInfo.title)}</div>
                </div>
                <div class="ticket-info-item">
                    <div class="ticket-info-label">Priority</div>
                    <div class="ticket-info-value">
                        <span class="badge" style="background-color: ${priorityColor}; color: white">${escapeHtml(ticketInfo.priority || 'N/A')}</span>
                    </div>
                </div>
                <div class="ticket-info-item">
                    <div class="ticket-info-label">Created By</div>
                    <div class="ticket-info-value">${escapeHtml(ticketInfo.created_by)}</div>
                </div>
                <div class="ticket-info-item">
                    <div class="ticket-info-label">Department</div>
                    <div class="ticket-info-value">${escapeHtml(ticketInfo.department)}</div>
                </div>
                <div class="ticket-info-item">
                    <div class="ticket-info-label">Category</div>
                    <div class="ticket-info-value">${escapeHtml(ticketInfo.category)}</div>
                </div>
                <div class="ticket-info-item">
                    <div class="ticket-info-label">Created At</div>
                    <div class="ticket-info-value">${escapeHtml(ticketInfo.created_at)}</div>
                </div>
                <div class="modal-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Access Restricted:</strong> ${escapeHtml(ticketInfo.reason)}
                </div>
            `;

            $('.ticket-info-content').html(modalContent);
            $('#ticketInfoModal').modal('show');
        }

        // View ticket function
        function viewTicket(ticketId) {
            const checkAccessUrl = `{{ route('tickets.check-access', ':id') }}`.replace(':id', ticketId);

            fetch(checkAccessUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.type === 'redirect') {
                        window.location.href = data.url;
                    } else if (data.type === 'modal_info') {
                        showTicketInfoModal(data.ticket_info);
                    } else {
                        window.location.href = `{{ route('tickets.show', ':id') }}`.replace(':id', ticketId);
                    }
                })
                .catch(error => {
                    window.location.href = `{{ route('tickets.show', ':id') }}`.replace(':id', ticketId);
                });
        }
    </script>
@endpush
