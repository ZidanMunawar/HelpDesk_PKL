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

    <style>
        /* Sidebar Filters - FIXED */
        .sidebar-filters {
            background: #fff;
            border-radius: 10px;
            border: 1px solid #eaeaea;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .filter-section-header {
            padding: 14px 18px;
            background: #f8f9fa;
            border-bottom: 1px solid #eaeaea;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
            font-size: 14px;
            color: #333;
        }

        .filter-section-header:hover {
            background: #ff6200;
            color: white;
        }

        .filter-section-header i {
            transition: transform 0.3s ease;
            font-size: 12px;
            color: #666;
        }

        .filter-section-header:hover i {
            color: white;
        }

        .filter-section-header.collapsed i {
            transform: rotate(-90deg);
        }

        .filter-section-content {
            padding: 0;
            max-height: 300px;
            overflow-y: auto;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .filter-section-content.collapsed {
            max-height: 0;
            padding: 0;
            overflow: hidden;
        }

        /* Filter Links - FIXED */
        .filter-link {
            padding: 10px 18px;
            border-bottom: 1px solid #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #555;
            font-size: 13px;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .filter-link:hover {
            background: #ff6200;
            color: white;
            padding-left: 22px;
        }

        .filter-link.active {
            background: #ff6200;
            color: white;
            font-weight: 500;
        }

        .filter-link.active:hover {
            background: #ff6200;
            color: white;
        }

        .filter-link i {
            margin-right: 8px;
            font-size: 13px;
            width: 18px;
            text-align: center;
        }

        .filter-link .badge {
            font-size: 10px;
            padding: 2px 6px;
            min-width: 20px;
            text-align: center;
        }

        /* Ticket Items - IMPROVED */
        .ticket-card {
            background: white;
            border-radius: 8px;
            padding: 14px;
            margin-bottom: 10px;
            border: 1px solid #eaeaea;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .ticket-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 98, 0, 0.15);
            border-color: #ff6200;
        }

        .ticket-card:hover::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
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
            padding: 2px 8px;
            border-radius: 4px;
            display: inline-block;
        }

        .ticket-title {
            font-size: 14px;
            color: #333;
            font-weight: 500;
            margin: 8px 0;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            word-break: break-word;
        }

        /* Ticket Meta - IMPROVED for Android */
        .ticket-meta {
            font-size: 11px;
            color: #666;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 8px;
        }

        .ticket-meta-item {
            display: flex;
            align-items: center;
            gap: 4px;
            flex-shrink: 0;
        }

        .ticket-meta-item i {
            font-size: 10px;
            color: #999;
            flex-shrink: 0;
        }

        .ticket-meta-text {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 120px;
        }

        /* Ticket Footer - COMPACT */
        .ticket-footer {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            align-items: center;
            padding-top: 8px;
            border-top: 1px dashed #eee;
            margin-top: 8px;
        }

        /* Badges - COMPACT */
        .badge-sm {
            padding: 3px 6px;
            font-size: 10px;
            font-weight: 500;
            border-radius: 3px;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            line-height: 1;
            white-space: nowrap;
        }

        .badge-cost {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
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

        .badge-user {
            background: #f3e5f5;
            color: #7b1fa2;
            border: 1px solid #e1bee7;
        }

        /* Status Badges - COMPACT */
        .status-badge {
            padding: 3px 8px;
            font-size: 10px;
            font-weight: 600;
            border-radius: 12px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        .status-open {
            background: #e3f2fd;
            color: #1565c0;
        }

        .status-received {
            background: #e3f2fd;
            color: #1565c0;
        }

        .status-pending_om {
            background: #fff3cd;
            color: #856404;
        }

        .status-in_progress {
            background: #fff8e1;
            color: #ff8f00;
        }

        .status-pending_vr {
            background: #e7f3ff;
            color: #0056b3;
        }

        .status-pending {
            background: #fff3e0;
            color: #ef6c00;
        }

        .status-completed {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status-pending_gm {
            background: #fff3e0;
            color: #ef6c00;
        }

        .status-closed {
            background: #f5f5f5;
            color: #424242;
        }

        .status-cancelled {
            background: #ffebee;
            color: #c62828;
        }

        /* Approval Badge - COMPACT */
        .approval-badge {
            padding: 3px 6px;
            font-size: 9px;
            border-radius: 10px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            white-space: nowrap;
        }

        .approval-pending {
            background: #fff3cd;
            color: #856404;
        }

        .approval-approved {
            background: #d4edda;
            color: #155724;
        }

        .approval-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        /* Priority Badge - COMPACT */
        .priority-badge {
            padding: 3px 8px;
            font-size: 10px;
            border-radius: 3px;
            color: white;
            font-weight: 600;
            min-width: 50px;
            text-align: center;
            white-space: nowrap;
        }

        /* Empty State - BUTTONS FIXED */
        .empty-state {
            text-align: center;
            padding: 30px 15px;
        }

        .empty-state-icon {
            font-size: 40px;
            color: #e0e0e0;
            margin-bottom: 12px;
        }

        .empty-state-title {
            font-size: 15px;
            color: #666;
            margin-bottom: 6px;
            font-weight: 500;
        }

        .empty-state-text {
            font-size: 12px;
            color: #999;
            margin-bottom: 15px;
        }

        .empty-state-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* Buttons - USING BOOTSTRAP DEFAULT */
        .btn-new-ticket {
            background: #ff6200;
            border: none;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 500;
            border-radius: 6px;
            transition: all 0.3s ease;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-new-ticket:hover {
            background: #ff7b00;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(255, 98, 0, 0.2);
            color: white;
        }

        .btn-create-ticket {
            background: linear-gradient(135deg, #ff7b00, #ff6200);
            border: none;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 500;
            border-radius: 6px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: white;
            text-decoration: none;
        }

        .btn-create-ticket:hover {
            background: linear-gradient(135deg, #ff6200, #ff7b00);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(255, 98, 0, 0.2);
            color: white;
        }

        .btn-clear {
            background: #f5f5f5;
            border: 1px solid #ddd;
            padding: 7px 14px;
            font-size: 12px;
            border-radius: 6px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: #666;
            text-decoration: none;
        }

        .btn-clear:hover {
            background: #ff6200;
            border-color: #ff6200;
            color: white;
        }

        /* Search - FIXED */
        .search-container {
            background: #fff;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 12px;
            border: 1px solid #eaeaea;
        }

        .search-input-group {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .search-input {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 13px;
            transition: all 0.3s ease;
            background: white;
        }

        .search-input:focus {
            border-color: #ff6200;
            box-shadow: 0 0 0 2px rgba(255, 98, 0, 0.1);
            outline: none;
        }

        .search-btn {
            background: #ff6200;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 500;
        }

        .search-btn:hover {
            background: #ff7b00;
            transform: translateY(-1px);
        }

        /* Pagination - IMPROVED */
        .pagination-container {
            margin-top: 15px;
            padding: 12px;
            border-top: 1px solid #eee;
            background: #f9f9f9;
            border-radius: 0 0 12px 12px;
        }

        .pagination .page-link {
            font-size: 12px;
            padding: 5px 10px;
            border-color: #ddd;
            color: #666;
            min-width: 32px;
            text-align: center;
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

        /* Ticket Info Modal */
        .ticket-info-item {
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .ticket-info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .ticket-info-item strong {
            display: block;
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }

        .ticket-info-item div {
            font-size: 14px;
            color: #333;
        }

        /* CRITICAL Android/Mobile Optimizations */
        @media (max-width: 768px) {

            /* Compact spacing */
            .sidebar-filters {
                margin-bottom: 10px;
            }

            .filter-section-header {
                padding: 10px 12px;
                font-size: 12px;
            }

            .filter-link {
                padding: 8px 12px;
                font-size: 11px;
                min-height: 36px;
            }

            /* Ticket card mobile optimization */
            .ticket-card {
                padding: 10px;
                margin-bottom: 8px;
                border-radius: 6px;
            }

            .ticket-title {
                font-size: 13px;
                -webkit-line-clamp: 2;
                line-height: 1.3;
                margin: 6px 0;
            }

            /* Meta items - HORIZONTAL SCROLL for Android */
            .ticket-meta {
                display: flex;
                overflow-x: auto;
                overflow-y: hidden;
                gap: 8px;
                padding-bottom: 4px;
                margin-bottom: 6px;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
            }

            .ticket-meta::-webkit-scrollbar {
                display: none;
            }

            .ticket-meta-item {
                flex: 0 0 auto;
                white-space: nowrap;
            }

            .ticket-meta-text {
                max-width: 100px;
            }

            /* Footer - horizontal layout */
            .ticket-footer {
                gap: 4px;
                padding-top: 6px;
                margin-top: 6px;
            }

            /* Badges - smaller */
            .badge-sm {
                font-size: 9px;
                padding: 2px 5px;
            }

            .status-badge,
            .approval-badge {
                font-size: 9px;
                padding: 2px 6px;
            }

            .priority-badge {
                font-size: 9px;
                padding: 2px 6px;
                min-width: 40px;
            }

            /* Empty state mobile */
            .empty-state {
                padding: 20px 10px;
            }

            .empty-state-icon {
                font-size: 32px;
            }

            .empty-state-title {
                font-size: 13px;
            }

            .empty-state-text {
                font-size: 11px;
                margin-bottom: 12px;
            }

            .empty-state-buttons {
                gap: 6px;
            }

            .btn-create-ticket,
            .btn-clear {
                padding: 6px 12px;
                font-size: 11px;
            }

            /* Search mobile */
            .search-container {
                padding: 10px;
                margin-bottom: 8px;
            }

            .search-input {
                padding: 8px 10px;
                font-size: 12px;
            }

            .search-btn {
                padding: 8px 12px;
                font-size: 12px;
                min-width: 60px;
            }

            .search-input-group {
                gap: 6px;
            }
        }

        @media (max-width: 576px) {

            /* Extra small screens */
            .ticket-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 6px;
            }

            .ticket-meta-text {
                max-width: 80px;
            }

            .ticket-footer {
                justify-content: flex-start;
            }

            /* Stack buttons on very small screens */
            .empty-state-buttons {
                flex-direction: column;
                width: 100%;
                max-width: 200px;
                margin: 0 auto;
            }

            .empty-state-buttons .btn {
                width: 100%;
                justify-content: center;
            }

            /* Single column on very small */
            .col-lg-3,
            .col-lg-9 {
                width: 100%;
                padding-left: 8px;
                padding-right: 8px;
            }
        }

        /* Performance optimizations */
        .ticket-card {
            will-change: transform;
            backface-visibility: hidden;
            -webkit-font-smoothing: antialiased;
        }

        .filter-link {
            will-change: background-color;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="sidebar-filters">
                <!-- New Ticket Button - FIXED (Bootstrap Button) -->
                <div class="p-3 border-bottom">
                    @if (in_array(auth()->user()->role, ['admin_eng', 'user']))
                        <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-block btn-new-ticket">
                            <i class="fas fa-plus me-2"></i> New Ticket
                        </a>
                    @else
                        <div class="alert alert-info py-2 mb-0" style="font-size: 12px;">
                            <i class="fas fa-info-circle me-1"></i>
                            Only Admin Eng and Users can create tickets
                        </div>
                    @endif
                </div>

                <!-- Status Filters -->
                <div class="filter-section-header" data-toggle="filter-section" data-target="status-filter">
                    <span>Ticket Status</span>
                    <i class="fas fa-chevron-up"></i>
                </div>
                <div class="filter-section-content" id="status-filter">
                    <div>
                        <a href="{{ route('tickets.index') }}" class="filter-link {{ !request('status') ? 'active' : '' }}">
                            <i class="fas fa-inbox"></i>
                            <span>All Tickets</span>
                            <span class="badge bg-secondary">{{ $statusCounts['all'] }}</span>
                        </a>

                        <!-- ==================== STATUS UNTUK USER ROLE ==================== -->
                        @if (auth()->user()->role === 'user')
                            <!-- Untuk USER: Hide pending_om dan pending_gm -->
                            <a href="{{ route('tickets.index', ['status' => 'open']) }}"
                                class="filter-link {{ request('status') == 'open' ? 'active' : '' }}">
                                <i class="fas fa-folder-open"></i>
                                <span>Open</span>
                                <span class="badge bg-primary">{{ $statusCounts['open'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('tickets.index', ['status' => 'received']) }}"
                                class="filter-link {{ request('status') == 'received' ? 'active' : '' }}">
                                <i class="fas fa-inbox"></i>
                                <span>Received</span>
                                <span class="badge bg-info">{{ $statusCounts['received'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('tickets.index', ['status' => 'in_progress']) }}"
                                class="filter-link {{ request('status') == 'in_progress' ? 'active' : '' }}">
                                <i class="fas fa-spinner"></i>
                                <span>In Progress</span>
                                <span class="badge bg-info">{{ $statusCounts['in_progress'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('tickets.index', ['status' => 'pending_vr']) }}"
                                class="filter-link {{ request('status') == 'pending_vr' ? 'active' : '' }}">
                                <i class="fas fa-file-invoice-dollar"></i>
                                <span>Pending VR</span>
                                <span class="badge bg-warning">{{ $statusCounts['pending_vr'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('tickets.index', ['status' => 'completed']) }}"
                                class="filter-link {{ request('status') == 'completed' ? 'active' : '' }}">
                                <i class="fas fa-check-circle"></i>
                                <span>Completed</span>
                                <span class="badge bg-success">{{ $statusCounts['completed'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('tickets.index', ['status' => 'closed']) }}"
                                class="filter-link {{ request('status') == 'closed' ? 'active' : '' }}">
                                <i class="fas fa-times-circle"></i>
                                <span>Closed</span>
                                <span class="badge bg-dark">{{ $statusCounts['closed'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('tickets.index', ['status' => 'cancelled']) }}"
                                class="filter-link {{ request('status') == 'cancelled' ? 'active' : '' }}">
                                <i class="fas fa-ban"></i>
                                <span>Cancelled</span>
                                <span class="badge bg-danger">{{ $statusCounts['cancelled'] ?? 0 }}</span>
                            </a>
                        @else
                            <!-- Untuk ADMIN/MANAGER/TECHNICIAN/OM/GM: Show semua status -->
                            <a href="{{ route('tickets.index', ['status' => 'open']) }}"
                                class="filter-link {{ request('status') == 'open' ? 'active' : '' }}">
                                <i class="fas fa-folder-open"></i>
                                <span>Open</span>
                                <span class="badge bg-primary">{{ $statusCounts['open'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('tickets.index', ['status' => 'received']) }}"
                                class="filter-link {{ request('status') == 'received' ? 'active' : '' }}">
                                <i class="fas fa-inbox"></i>
                                <span>Received</span>
                                <span class="badge bg-info">{{ $statusCounts['received'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('tickets.index', ['status' => 'pending_om']) }}"
                                class="filter-link {{ request('status') == 'pending_om' ? 'active' : '' }}">
                                <i class="fas fa-clock"></i>
                                <span>Pending OM</span>
                                <span class="badge bg-warning">{{ $statusCounts['pending_om'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('tickets.index', ['status' => 'in_progress']) }}"
                                class="filter-link {{ request('status') == 'in_progress' ? 'active' : '' }}">
                                <i class="fas fa-spinner"></i>
                                <span>In Progress</span>
                                <span class="badge bg-info">{{ $statusCounts['in_progress'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('tickets.index', ['status' => 'pending_vr']) }}"
                                class="filter-link {{ request('status') == 'pending_vr' ? 'active' : '' }}">
                                <i class="fas fa-file-invoice-dollar"></i>
                                <span>Pending VR</span>
                                <span class="badge bg-warning">{{ $statusCounts['pending_vr'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('tickets.index', ['status' => 'completed']) }}"
                                class="filter-link {{ request('status') == 'completed' ? 'active' : '' }}">
                                <i class="fas fa-check-circle"></i>
                                <span>Completed</span>
                                <span class="badge bg-success">{{ $statusCounts['completed'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('tickets.index', ['status' => 'pending_gm']) }}"
                                class="filter-link {{ request('status') == 'pending_gm' ? 'active' : '' }}">
                                <i class="fas fa-user-tie"></i>
                                <span>Pending GM</span>
                                <span class="badge bg-warning">{{ $statusCounts['pending_gm'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('tickets.index', ['status' => 'closed']) }}"
                                class="filter-link {{ request('status') == 'closed' ? 'active' : '' }}">
                                <i class="fas fa-times-circle"></i>
                                <span>Closed</span>
                                <span class="badge bg-dark">{{ $statusCounts['closed'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('tickets.index', ['status' => 'cancelled']) }}"
                                class="filter-link {{ request('status') == 'cancelled' ? 'active' : '' }}">
                                <i class="fas fa-ban"></i>
                                <span>Cancelled</span>
                                <span class="badge bg-danger">{{ $statusCounts['cancelled'] ?? 0 }}</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- My Tickets Filter - HANYA UNTUK USER -->
                @if (auth()->user()->role === 'user')
                    <div class="filter-section-header collapsed" data-toggle="filter-section"
                        data-target="my-tickets-filter">
                        <span>My Tickets</span>
                        <i class="fas fa-chevron-up"></i>
                    </div>
                    <div class="filter-section-content collapsed" id="my-tickets-filter">
                        <div>
                            <a href="{{ route('tickets.index', ['my_tickets' => '1']) }}"
                                class="filter-link {{ request('my_tickets') == '1' ? 'active' : '' }}">
                                <i class="fas fa-user"></i>
                                <span>My Tickets Only</span>
                            </a>
                            <a href="{{ route('tickets.index') }}"
                                class="filter-link {{ !request('my_tickets') ? 'active' : '' }}">
                                <i class="fas fa-users"></i>
                                <span>All Tickets</span>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Approval Status Filters -->
                <div class="filter-section-header collapsed" data-toggle="filter-section" data-target="approval-filter">
                    <span>Approval Status</span>
                    <i class="fas fa-chevron-up"></i>
                </div>
                <div class="filter-section-content collapsed" id="approval-filter">
                    <div>
                        <a href="{{ route('tickets.index', ['approval_status' => '']) }}"
                            class="filter-link {{ !request('approval_status') ? 'active' : '' }}">
                            <i class="fas fa-circle"></i>
                            <span>All</span>
                        </a>
                        <a href="{{ route('tickets.index', ['approval_status' => 'pending_approval']) }}"
                            class="filter-link {{ request('approval_status') == 'pending_approval' ? 'active' : '' }}">
                            <i class="fas fa-clock text-warning"></i>
                            <span>Pending Approval</span>
                        </a>
                        <a href="{{ route('tickets.index', ['approval_status' => 'approved']) }}"
                            class="filter-link {{ request('approval_status') == 'approved' ? 'active' : '' }}">
                            <i class="fas fa-check-circle text-success"></i>
                            <span>Approved</span>
                        </a>
                        <a href="{{ route('tickets.index', ['approval_status' => 'rejected']) }}"
                            class="filter-link {{ request('approval_status') == 'rejected' ? 'active' : '' }}">
                            <i class="fas fa-times-circle text-danger"></i>
                            <span>Rejected</span>
                        </a>
                    </div>
                </div>

                <!-- Category Filters -->
                <div class="filter-section-header collapsed" data-toggle="filter-section" data-target="category-filter">
                    <span>Categories</span>
                    <i class="fas fa-chevron-up"></i>
                </div>
                <div class="filter-section-content collapsed" id="category-filter">
                    <div>
                        <a href="{{ route('tickets.index', ['category' => '']) }}"
                            class="filter-link {{ !request('category') ? 'active' : '' }}">
                            <i class="fas fa-folder"></i>
                            <span>All Categories</span>
                        </a>
                        @foreach ($categories as $category)
                            <a href="{{ route('tickets.index', ['category' => $category->id]) }}"
                                class="filter-link {{ request('category') == $category->id ? 'active' : '' }}">
                                <i class="fas fa-folder-open"></i>
                                <span class="d-flex align-items-center">
                                    {{ Str::limit($category->name, 18) }}
                                    @if ($category->department)
                                        <small
                                            class="text-muted ms-1">({{ Str::limit($category->department->name, 8) }})</small>
                                    @endif
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Priority Filters -->
                <div class="filter-section-header collapsed" data-toggle="filter-section" data-target="priority-filter">
                    <span>Priorities</span>
                    <i class="fas fa-chevron-up"></i>
                </div>
                <div class="filter-section-content collapsed" id="priority-filter">
                    <div>
                        <a href="{{ route('tickets.index', ['priority' => '']) }}"
                            class="filter-link {{ !request('priority') ? 'active' : '' }}">
                            <i class="fas fa-flag"></i>
                            <span>All Priorities</span>
                        </a>
                        @foreach ($priorities as $priority)
                            <a href="{{ route('tickets.index', ['priority' => $priority->id]) }}"
                                class="filter-link {{ request('priority') == $priority->id ? 'active' : '' }}">
                                <i class="fas fa-flag" style="color: {{ $priority->color }}"></i>
                                <span>{{ $priority->name }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Department Filters -->
                <div class="filter-section-header collapsed" data-toggle="filter-section"
                    data-target="department-filter">
                    <span>Departments</span>
                    <i class="fas fa-chevron-up"></i>
                </div>
                <div class="filter-section-content collapsed" id="department-filter">
                    <div>
                        <a href="{{ route('tickets.index', ['department' => '']) }}"
                            class="filter-link {{ !request('department') ? 'active' : '' }}">
                            <i class="fas fa-building"></i>
                            <span>All Departments</span>
                        </a>
                        @foreach ($departments as $department)
                            <a href="{{ route('tickets.index', ['department' => $department->id]) }}"
                                class="filter-link {{ request('department') == $department->id ? 'active' : '' }}">
                                <i class="fas fa-building"></i>
                                <span>{{ Str::limit($department->name, 18) }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9 col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <!-- Search Bar -->
                    <div class="search-container">
                        <form action="{{ route('tickets.index') }}" method="GET" id="searchForm">
                            <div class="search-input-group">
                                <input type="text" name="search" class="search-input"
                                    placeholder="Search tickets by number, title, user, or location..."
                                    value="{{ request('search') }}" autocomplete="off">
                                <button type="submit" class="search-btn">
                                    <i class="fas fa-search"></i>
                                    <span class="d-none d-md-inline">Search</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Ticket List -->
                    <div class="px-2 px-md-3">
                        @forelse($tickets as $ticket)
                            <div class="ticket-card" onclick="viewTicket({{ $ticket->id }})">
                                <div class="ticket-header">
                                    <div class="ticket-number">#{{ $ticket->ticket_number }}</div>
                                    <div class="d-flex align-items-center gap-2">
                                        @if (auth()->user()->role !== 'user')
                                            <!-- Approval badge hanya untuk NON-USER -->
                                            <span class="approval-badge approval-{{ $ticket->approval_status }}">
                                                <i
                                                    class="fas {{ $ticket->approval_status == 'approved' ? 'fa-check-circle' : ($ticket->approval_status == 'rejected' ? 'fa-times-circle' : 'fa-clock') }}"></i>
                                                {{ str_replace('_', ' ', $ticket->approval_status) }}
                                            </span>
                                        @endif

                                        <!-- Status badge dengan mapping untuk USER -->
                                        @php
                                            $displayStatus = $ticket->status;
                                            if (auth()->user()->role === 'user') {
                                                // User: map pending_om dan pending_gm
                                                if ($displayStatus === 'pending_om') {
                                                    $displayStatus = 'in_progress';
                                                } elseif ($displayStatus === 'pending_gm') {
                                                    $displayStatus = 'completed';
                                                }
                                            }
                                        @endphp
                                        <span class="status-badge status-{{ $displayStatus }}">
                                            @if (auth()->user()->role === 'user' && in_array($ticket->status, ['pending_om', 'pending_gm']))
                                                {{ str_replace('_', ' ', $displayStatus) }}
                                            @else
                                                {{ str_replace('_', ' ', $ticket->status) }}
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                <div class="ticket-title" title="{{ $ticket->title }}">
                                    {{ Str::limit($ticket->title, 80) }}
                                </div>

                                <div class="ticket-meta">
                                    <!-- User Information -->
                                    <div class="ticket-meta-item">
                                        <i class="fas fa-user"></i>
                                        <span class="ticket-meta-text" title="{{ $ticket->user->name }}">
                                            {{ Str::limit($ticket->user->name, 20) }}
                                        </span>
                                    </div>

                                    <div class="ticket-meta-item">
                                        <i class="fas fa-folder"></i>
                                        <span class="ticket-meta-text" title="{{ $ticket->category->name }}">
                                            {{ Str::limit($ticket->category->name, 20) }}
                                        </span>
                                    </div>

                                    @if ($ticket->department)
                                        <div class="ticket-meta-item">
                                            <i class="fas fa-building"></i>
                                            <span class="ticket-meta-text" title="{{ $ticket->department->name }}">
                                                {{ Str::limit($ticket->department->name, 18) }}
                                            </span>
                                        </div>
                                    @endif

                                    <div class="ticket-meta-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span class="ticket-meta-text">
                                            @if ($ticket->location)
                                                {{ Str::limit($ticket->location->name, 18) }}
                                                @if ($ticket->location->floor_number)
                                                    (F{{ $ticket->location->floor_number }})
                                                @endif
                                            @elseif($ticket->location_manual)
                                                {{ Str::limit($ticket->location_manual, 18) }}
                                            @else
                                                N/A
                                            @endif
                                        </span>
                                    </div>

                                    <div class="ticket-meta-item">
                                        <i class="fas fa-clock"></i>
                                        <span>{{ $ticket->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>

                                <div class="ticket-footer">
                                    @if ($ticket->assigned_to)
                                        <span class="badge-sm badge-assigned" title="Assigned To">
                                            <i class="fas fa-user-tag"></i>
                                            {{ Str::limit($ticket->assignedUser->name ?? 'N/A', 12) }}
                                        </span>
                                    @endif

                                    @if ($ticket->due_date)
                                        <span class="badge-sm badge-date" title="Due Date">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ \Carbon\Carbon::parse($ticket->due_date)->format('M d, Y') }}
                                            @if ($ticket->due_date < now())
                                                <i class="fas fa-exclamation-triangle text-danger"></i>
                                            @endif
                                        </span>
                                    @endif

                                    <span class="priority-badge"
                                        style="background-color: {{ $ticket->priority->color }}">
                                        {{ $ticket->priority->name }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-inbox"></i>
                                </div>
                                <h5 class="empty-state-title">No Tickets Found</h5>
                                <p class="empty-state-text">
                                    @if (request()->anyFilled(['search', 'status', 'category', 'priority', 'department', 'approval_status']))
                                        Try adjusting your search criteria or create a new ticket.
                                    @else
                                        There are no tickets in the system yet.
                                    @endif
                                </p>
                                <div class="empty-state-buttons">
                                    @if (request()->anyFilled(['search', 'status', 'category', 'priority', 'department', 'approval_status']))
                                        <a href="{{ route('tickets.index') }}" class="btn-clear">
                                            <i class="fas fa-redo"></i>
                                            Clear Filters
                                        </a>
                                    @endif
                                    @if (in_array(auth()->user()->role, ['admin_eng', 'user']))
                                        <a href="{{ route('tickets.create') }}" class="btn-create-ticket">
                                            <i class="fas fa-plus"></i>
                                            Create New Ticket
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if ($tickets->hasPages())
                        <div class="pagination-container">
                            <div class="d-flex justify-content-center">
                                {{ $tickets->withQueryString()->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk User saat akses ticket orang lain -->
    <div class="modal fade" id="ticketInfoModal" tabindex="-1" aria-labelledby="ticketInfoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ticketInfoModalLabel">Ticket Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="ticket-info-content">
                        <!-- Content akan diisi oleh JavaScript -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script>
        // Helper untuk status badge color
        function getStatusBadgeColor(status) {
            const colors = {
                'open': 'primary',
                'received': 'info',
                'pending_om': 'warning',
                'in_progress': 'info',
                'pending_vr': 'warning',
                'completed': 'success',
                'pending_gm': 'warning',
                'closed': 'dark',
                'cancelled': 'danger',
                'pending': 'warning',
                'resolved': 'success'
            };

            // Normalize status
            const normalizedStatus = status.toLowerCase().replace(/ /g, '_');
            return colors[normalizedStatus] || 'secondary';
        }

        // Helper untuk status display name dengan mapping untuk USER
        function getStatusDisplayName(status, isUser = false) {
            // Jika user dan status pending_om/pending_gm, map ke yang lain
            if (isUser) {
                if (status === 'pending_om' || status === 'pending_om') {
                    return 'In Progress';
                } else if (status === 'pending_gm' || status === 'pending_gm') {
                    return 'Completed';
                }
            }

            const displayNames = {
                'open': 'Open',
                'received': 'Received',
                'pending_om': 'Pending OM',
                'in_progress': 'In Progress',
                'pending_vr': 'Pending VR',
                'completed': 'Completed',
                'pending_gm': 'Pending GM',
                'closed': 'Closed',
                'cancelled': 'Cancelled'
            };

            // Normalize status
            const normalizedStatus = status.toLowerCase().replace(/ /g, '_');
            return displayNames[normalizedStatus] || status;
        }

        // Fungsi view ticket dengan permission check menggunakan API endpoint
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
                        // Fallback ke URL langsung
                        window.location.href = `{{ route('tickets.show', ':id') }}`.replace(':id', ticketId);
                    }
                })
                .catch(error => {
                    console.error('Error checking access:', error);
                    // Fallback: direct redirect
                    window.location.href = `{{ route('tickets.show', ':id') }}`.replace(':id', ticketId);
                });
        }

        // Fungsi tampilkan modal info ticket
        function showTicketInfoModal(ticketInfo) {
            const isUser = {{ auth()->user()->role === 'user' ? 'true' : 'false' }};
            const displayStatus = getStatusDisplayName(ticketInfo.status, isUser);

            const modalContent = `
                <div class="ticket-info-item">
                    <strong>Ticket Number:</strong>
                    <div>#${ticketInfo.number}</div>
                </div>
                <div class="ticket-info-item">
                    <strong>Title:</strong>
                    <div>${ticketInfo.title}</div>
                </div>
                <div class="ticket-info-item">
                    <strong>Status:</strong>
                    <div>
                        <span class="badge bg-${getStatusBadgeColor(ticketInfo.status)}">
                            ${displayStatus}
                        </span>
                    </div>
                </div>
                <div class="ticket-info-item">
                    <strong>Created By:</strong>
                    <div>${ticketInfo.created_by}</div>
                </div>
                <div class="ticket-info-item">
                    <strong>Department:</strong>
                    <div>${ticketInfo.department}</div>
                </div>
                <div class="ticket-info-item">
                    <strong>Category:</strong>
                    <div>${ticketInfo.category}</div>
                </div>
                <div class="ticket-info-item">
                    <strong>Created At:</strong>
                    <div>${ticketInfo.created_at}</div>
                </div>
                <div class="alert alert-warning mt-3 mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Access Restricted:</strong> ${ticketInfo.reason}
                </div>
            `;

            document.querySelector('.ticket-info-content').innerHTML = modalContent;
            const modal = new bootstrap.Modal(document.getElementById('ticketInfoModal'));
            modal.show();
        }

        $(document).ready(function() {
            // Toastr configuration
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "3000"
            };

            // Filter section toggle
            $('[data-toggle="filter-section"]').on('click', function(e) {
                e.stopPropagation();
                const target = $(this).data('target');
                const $content = $('#' + target);
                const $icon = $(this).find('i');

                if ($content.hasClass('collapsed')) {
                    $content.removeClass('collapsed');
                    $(this).removeClass('collapsed');
                    $icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                } else {
                    $content.addClass('collapsed');
                    $(this).addClass('collapsed');
                    $icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                }
            });

            // Auto submit on filter click
            $('.filter-link').on('click', function(e) {
                const href = $(this).attr('href');
                if (href && href !== 'javascript:void(0)') {
                    e.preventDefault();
                    window.location.href = href;
                }
            });

            // Search form submit on enter
            $('.search-input').on('keypress', function(e) {
                if (e.which === 13) {
                    $('#searchForm').submit();
                    return false;
                }
            });

            // Optimize for mobile
            function optimizeForMobile() {
                if ($(window).width() < 768) {
                    $('.filter-section-header').not('[data-target="status-filter"]').addClass('collapsed');
                    $('.filter-section-content').not('#status-filter').addClass('collapsed');
                    $('.filter-section-header').not('[data-target="status-filter"]').find('i')
                        .removeClass('fa-chevron-up').addClass('fa-chevron-down');
                }
            }

            optimizeForMobile();

            let resizeTimer;
            $(window).on('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(optimizeForMobile, 100);
            });
        });
    </script>
@endpush
