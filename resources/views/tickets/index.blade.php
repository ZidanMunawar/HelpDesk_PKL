@extends('layouts.main')

@section('title', 'Tickets | ' . config('app.name'))

@section('page-title', 'Tickets')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'Tickets', 'url' => 'javascript:void(0)'],
            ['title' => 'All Tickets', 'url' => 'javascript:void(0)'],
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

        /* Export Button Group */
        .btn-export-group {
            position: relative;
            display: inline-block;
        }

        .btn-export {
            background: linear-gradient(135deg, #1e3c2c, #2ecc71);
            color: white;
            box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3);
        }

        .btn-export:hover {
            background: linear-gradient(135deg, #2ecc71, #1e3c2c);
            box-shadow: 0 6px 20px rgba(46, 204, 113, 0.4);
            transform: translateY(-2px);
        }

        .btn-export i {
            font-size: 14px;
        }

        .export-dropdown {
            position: absolute;
            right: 0;
            top: 120%;
            min-width: 200px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .btn-export-group:hover .export-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .export-dropdown a {
            color: #333;
            padding: 12px 20px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            transition: all 0.2s ease;
            border-bottom: 1px solid #f0f0f0;
        }

        .export-dropdown a:last-child {
            border-bottom: none;
        }

        .export-dropdown a:hover {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding-left: 25px;
        }

        .export-dropdown a i {
            width: 20px;
            text-align: center;
            font-size: 16px;
        }

        .export-dropdown a:nth-child(1) i {
            color: #27ae60;
        }

        .export-dropdown a:nth-child(2) i {
            color: #217346;
        }

        .export-dropdown a:nth-child(3) i {
            color: #e74c3c;
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

        .btn-create i {
            font-size: 14px;
        }

        /* Disabled Button State */
        .btn-modern:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        /* ========== SEARCH BAR ========== */
        .search-section {
            margin-bottom: 15px;
        }

        .search-wrapper {
            display: flex;
            gap: 10px;
            align-items: center;
            background: white;
            border: 1px solid #eaeaea;
            border-radius: 10px;
            padding: 2px 2px 2px 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .search-wrapper:focus-within {
            border-color: #ff6200;
            box-shadow: 0 0 0 3px rgba(255, 98, 0, 0.1);
        }

        .search-input {
            flex: 1;
            border: none;
            padding: 12px 0;
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
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .search-btn:hover {
            background: #ff7b00;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(255, 98, 0, 0.3);
        }

        /* ========== FILTER SECTION ========== */
        .filter-section {
            background: white;
            border-radius: 10px;
            border: 1px solid #eaeaea;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .filter-header {
            padding: 14px 18px;
            background: #f8f9fa;
            border-bottom: 1px solid #eaeaea;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.2s ease;
            font-size: 14px;
            color: #333;
        }

        .filter-header:hover {
            background: #f0f0f0;
        }

        .filter-header-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-header-left i {
            color: #ff6200;
            font-size: 14px;
        }

        .filter-header-arrow {
            transition: transform 0.3s ease;
        }

        .filter-header.collapsed .filter-header-arrow {
            transform: rotate(0deg);
        }

        .filter-header:not(.collapsed) .filter-header-arrow {
            transform: rotate(180deg);
        }

        .filter-body {
            padding: 18px;
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

        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #ff6200;
            box-shadow: 0 0 0 2px rgba(255, 98, 0, 0.1);
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 5px;
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
            box-shadow: 0 4px 10px rgba(255, 98, 0, 0.3);
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
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(255, 98, 0, 0.3);
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
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10;
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
        .ticket-card {
            background: white;
            border-radius: 10px;
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
            align-items: flex-start;
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
            text-overflow: ellipsis;
            word-break: break-word;
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
            flex-shrink: 0;
        }

        .ticket-meta-item i {
            font-size: 12px;
            color: #999;
            flex-shrink: 0;
        }

        .ticket-meta-text {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 120px;
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
            line-height: 1;
            white-space: nowrap;
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

        /* Status Badges - Sama seperti di show blade */
        .status-badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
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
            background: #f8f9fa;
            color: #495057;
            border: 1px solid #e9ecef;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Priority Badges - Sama seperti di show blade */
        .priority-badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 11px;
            color: white !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 10px;
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
            border-radius: 10px;
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
            margin: 0 2px;
            transition: all 0.2s ease;
        }

        .pagination .page-item.active .page-link {
            background: #ff6200;
            border-color: #ff6200;
            color: white;
            box-shadow: 0 4px 10px rgba(255, 98, 0, 0.3);
        }

        .pagination .page-link:hover {
            background: #ff7b00;
            border-color: #ff7b00;
            color: white;
            transform: translateY(-1px);
        }

        /* Modal */
        .ticket-info-item {
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .ticket-info-item:last-child {
            border-bottom: none;
        }

        .ticket-info-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .ticket-info-value {
            font-size: 16px;
            color: #333;
            font-weight: 500;
        }

        .modal-warning {
            background: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-actions {
                flex-direction: column;
                align-items: flex-start;
            }

            .action-buttons {
                width: 100%;
                gap: 10px;
            }

            .btn-modern {
                flex: 1;
                padding: 10px 15px;
                font-size: 13px;
            }

            .btn-export-group {
                flex: 1;
            }

            .btn-export {
                width: 100%;
            }

            .btn-create {
                flex: 1;
            }

            .search-wrapper {
                flex-direction: column;
                align-items: stretch;
                padding: 10px;
            }

            .search-input {
                width: 100%;
                padding: 10px 0;
            }

            .search-btn {
                width: 100%;
                justify-content: center;
            }

            .filter-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .filter-actions {
                flex-direction: column;
            }

            .btn-filter,
            .btn-reset {
                width: 100%;
                justify-content: center;
            }

            .ticket-meta {
                overflow-x: auto;
                flex-wrap: nowrap;
                -webkit-overflow-scrolling: touch;
                padding-bottom: 8px;
            }

            .ticket-meta::-webkit-scrollbar {
                display: none;
            }

            .ticket-meta-item {
                flex: 0 0 auto;
            }

            .ticket-meta-text {
                max-width: 100px;
            }

            .export-dropdown {
                width: 100%;
                right: auto;
                left: 0;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Header dengan Tombol Create & Export -->
    <div class="header-actions">
        <div class="page-title-section">
            <h4><i class="fas fa-ticket-alt"></i>Ticket Management</h4>
        </div>
        <div class="action-buttons">
            <!-- Export Dropdown with Modern Button -->
            <div class="btn-export-group">
                <button class="btn-modern btn-export">
                    <i class="fas fa-download"></i> Export
                    <i class="fas fa-file" style="font-size: 12px;"></i>
                </button>
                <div class="export-dropdown">
                    <a href="#" onclick="alert('Export CSV feature coming soon!')">
                        <i class="fas fa-file-csv"></i> Export as CSV
                    </a>
                    <a href="#" onclick="alert('Export Excel feature coming soon!')">
                        <i class="fas fa-file-excel"></i> Export as Excel
                    </a>
                    <a href="#" onclick="alert('Export PDF feature coming soon!')">
                        <i class="fas fa-file-pdf"></i> Export as PDF
                    </a>
                </div>
            </div>

            <!-- Create Ticket Button - Modern -->
            @if (in_array(auth()->user()->role, ['admin_eng', 'user', 'manager']))
                <a href="{{ route('tickets.create') }}" class="btn-modern btn-create">
                    <i class="fas fa-plus-circle"></i> New Ticket
                </a>
            @endif
        </div>
    </div>

    <!-- SEARCH BAR -->
    <div class="search-section">
        <form action="{{ route('tickets.index') }}" method="GET" id="searchForm" class="ajax-filter-form">
            <!-- Preserve existing filters -->
            @if (request()->filled('status') &&
                    !in_array(request('status'), ['open', 'pending_om', 'pending_vr', 'ready_for_closure']))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            @if (request()->filled('my_tickets'))
                <input type="hidden" name="my_tickets" value="{{ request('my_tickets') }}">
            @endif
            @if (request()->filled('assigned'))
                <input type="hidden" name="assigned" value="{{ request('assigned') }}">
            @endif
            @if (request()->filled('department_filter'))
                <input type="hidden" name="department_filter" value="{{ request('department_filter') }}">
            @endif
            @if (request()->filled('stage'))
                <input type="hidden" name="stage" value="{{ request('stage') }}">
            @endif
            @if (request()->filled('unassigned'))
                <input type="hidden" name="unassigned" value="{{ request('unassigned') }}">
            @endif

            <div class="search-wrapper">
                <input type="text" name="search" class="search-input"
                    placeholder="Search by ticket number, title, user, or location..." value="{{ request('search') }}"
                    autocomplete="off">
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </form>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="filter-header {{ request()->anyFilled(['status', 'category', 'priority', 'department', 'date_from', 'date_to']) ? '' : 'collapsed' }}"
            id="filterToggle">
            <div class="filter-header-left">
                <i class="fas fa-filter"></i>
                <span>Advanced Filters</span>
            </div>
            <i class="fas fa-chevron-down filter-header-arrow"></i>
        </div>
        <div class="filter-body {{ request()->anyFilled(['status', 'category', 'priority', 'department', 'date_from', 'date_to']) ? '' : 'collapsed' }}"
            id="filterBody">
            <form action="{{ route('tickets.index') }}" method="GET" id="filterForm" class="ajax-filter-form">
                <!-- Preserve special filters -->
                @if (request()->filled('my_tickets'))
                    <input type="hidden" name="my_tickets" value="{{ request('my_tickets') }}">
                @endif
                @if (request()->filled('assigned'))
                    <input type="hidden" name="assigned" value="{{ request('assigned') }}">
                @endif
                @if (request()->filled('department_filter'))
                    <input type="hidden" name="department_filter" value="{{ request('department_filter') }}">
                @endif
                @if (request()->filled('stage'))
                    <input type="hidden" name="stage" value="{{ request('stage') }}">
                @endif
                @if (request()->filled('unassigned'))
                    <input type="hidden" name="unassigned" value="{{ request('unassigned') }}">
                @endif

                <input type="hidden" name="search" value="{{ request('search') }}">

                <div class="filter-grid">
                    <!-- Status Filter -->
                    <div class="filter-item">
                        <label class="filter-label">Status</label>
                        <select name="status" class="filter-select">
                            <option value="">All Status</option>
                            @foreach ($statusOptions as $value => $label)
                                @if ($value)
                                    <option value="{{ $value }}"
                                        {{ request('status') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <!-- Priority Filter - MULTIPLE -->
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

                    <!-- Category Filter - MULTIPLE -->
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

                    <!-- Department Filter - MULTIPLE -->
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

                    <!-- Date From -->
                    <div class="filter-item">
                        <label class="filter-label">Date From</label>
                        <input type="date" name="date_from" class="filter-input" value="{{ request('date_from') }}">
                    </div>

                    <!-- Date To -->
                    <div class="filter-item">
                        <label class="filter-label">Date To</label>
                        <input type="date" name="date_to" class="filter-input" value="{{ request('date_to') }}">
                    </div>
                </div>

                <!-- Filter Actions -->
                <div class="filter-actions">
                    <button type="submit" class="btn-filter">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <button type="button" class="btn-reset" id="resetFiltersBtn">
                        <i class="fas fa-redo-alt"></i> Reset Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TICKET CARDS CONTAINER -->
    <div id="ticket-list-container">
        @include('tickets.partials.ticket-cards', ['tickets' => $tickets])
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2 for multiple selects
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

            // Toastr configuration
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "3000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            // AJAX Filter
            $('.ajax-filter-form').on('submit', function(e) {
                e.preventDefault();
                applyFilters();
            });

            // Reset Filters
            $('#resetFiltersBtn').on('click', function() {
                // Reset all form fields
                $('#filterForm')[0].reset();

                // Reset Select2
                $('.select2-multiple').val(null).trigger('change');

                // Clear date inputs
                $('input[name="date_from"]').val('');
                $('input[name="date_to"]').val('');

                // Apply filters with empty values
                applyFilters();
            });

            // Handle pagination links
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');
                fetchTickets(url);
            });

            function applyFilters() {
                let formData = $('#filterForm').serialize();
                let searchData = $('#searchForm').serialize();

                // Combine both forms data
                let params = new URLSearchParams(formData + '&' + searchData);
                let url = '{{ route('tickets.index') }}?' + params.toString();

                fetchTickets(url);
            }

            function fetchTickets(url) {
                // Show loading
                $('#ticket-list-container').addClass('ticket-loading');

                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        // Extract only the ticket cards content
                        let tempDiv = $('<div>').html(response);
                        let newContent = tempDiv.find('#ticket-list-container').html();

                        if (newContent) {
                            $('#ticket-list-container').html(newContent);
                        } else {
                            $('#ticket-list-container').html(response);
                        }

                        // Update browser URL without refresh
                        window.history.pushState({}, '', url);

                        // Reinitialize Select2 for any new selects
                        $('.select2-multiple').select2({
                            width: '100%',
                            placeholder: 'Select options',
                            allowClear: true,
                            closeOnSelect: false
                        });
                    },
                    error: function(xhr) {
                        toastr.error('Failed to load tickets. Please try again.');
                        console.error('Error:', xhr);
                    },
                    complete: function() {
                        $('#ticket-list-container').removeClass('ticket-loading');
                    }
                });
            }

            // Handle browser back/forward buttons
            window.onpopstate = function() {
                location.reload();
            };
        });

        // Helper functions
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
            return colors[status] || 'secondary';
        }

        function getStatusDisplayName(status) {
            const displayNames = {
                'open': 'Open',
                'received': 'Received',
                'pending_om': 'OM Approval',
                'in_progress': 'In Progress',
                'pending_vr': 'VR Approval',
                'completed': 'Completed',
                'pending_gm': 'GM Approval',
                'ready_for_closure': 'Ready for Closure',
                'closed': 'Closed',
                'cancelled': 'Cancelled'
            };
            return displayNames[status] || status;
        }

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
                    if (data.type === 'modal_info') {
                        showTicketInfoModal(data.ticket_info);
                    } else if (data.type === 'redirect') {
                        window.location.href = data.url;
                    } else {
                        window.location.href = `{{ route('tickets.show', ':id') }}`.replace(':id', ticketId);
                    }
                })
                .catch(error => {
                    console.error('Error checking access:', error);
                    window.location.href = `{{ route('tickets.show', ':id') }}`.replace(':id', ticketId);
                });
        }

        function showTicketInfoModal(ticketInfo) {
            const statusColor = getStatusBadgeColor(ticketInfo.status);
            const displayStatus = getStatusDisplayName(ticketInfo.status);

            const modalContent = `
                <div class="ticket-info-item">
                    <div class="ticket-info-label">Ticket Number</div>
                    <div class="ticket-info-value">#${ticketInfo.number}</div>
                </div>
                <div class="ticket-info-item">
                    <div class="ticket-info-label">Title</div>
                    <div class="ticket-info-value">${ticketInfo.title}</div>
                </div>
                <div class="ticket-info-item">
                    <div class="ticket-info-label">Status</div>
                    <div class="ticket-info-value">
                        <span class="badge bg-${statusColor}">${displayStatus}</span>
                    </div>
                </div>
                <div class="ticket-info-item">
                    <div class="ticket-info-label">Created By</div>
                    <div class="ticket-info-value">${ticketInfo.created_by}</div>
                </div>
                <div class="ticket-info-item">
                    <div class="ticket-info-label">Department</div>
                    <div class="ticket-info-value">${ticketInfo.department}</div>
                </div>
                <div class="ticket-info-item">
                    <div class="ticket-info-label">Category</div>
                    <div class="ticket-info-value">${ticketInfo.category}</div>
                </div>
                <div class="ticket-info-item">
                    <div class="ticket-info-label">Created At</div>
                    <div class="ticket-info-value">${ticketInfo.created_at}</div>
                </div>
                <div class="modal-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Access Restricted:</strong> ${ticketInfo.reason}
                </div>
            `;

            $('.ticket-info-content').html(modalContent);
            $('#ticketInfoModal').modal('show');
        }
    </script>
@endpush
