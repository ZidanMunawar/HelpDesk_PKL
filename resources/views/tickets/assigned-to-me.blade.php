@extends('layouts.main')

@section('title', 'Assigned to Me | ' . config('app.name'))

@section('page-title', 'Tickets Assigned to Me')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'Tickets', 'url' => route('tickets.index')],
            ['title' => 'Assigned to Me', 'url' => 'javascript:void(0)'],
        ];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">

    <style>
        /* Sidebar Filters */
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

        /* Filter Links */
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
            background: var(--primary);
            color: rgb(255, 119, 0);
            font-weight: 500;
            border-left: 3px solid rgba(255, 255, 255, 0.3);
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

        /* Ticket Items */
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

        .ticket-card.urgent {
            background: #fff5f5;
            border-left: 4px solid #dc3545;
        }

        .ticket-card.urgent:hover::before {
            background: #dc3545;
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
            color: var(--primary);
            font-weight: 600;
            background: #e8f4ff;
            padding: 2px 8px;
            border-radius: 4px;
            display: inline-block;
        }

        .ticket-number .urgent-badge {
            background: #dc3545;
            color: white;
            font-size: 9px;
            padding: 1px 6px;
            border-radius: 3px;
            margin-left: 5px;
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

        /* Ticket Meta */
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

        /* Ticket Footer */
        .ticket-footer {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            align-items: center;
            padding-top: 8px;
            border-top: 1px dashed #eee;
            margin-top: 8px;
        }

        /* Badges */
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

        /* Status Badges */
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

        .status-in_progress {
            background: #fff8e1;
            color: #ff8f00;
        }

        .status-pending {
            background: #fff3e0;
            color: #ef6c00;
        }

        .status-resolved {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status-closed {
            background: #f5f5f5;
            color: #424242;
        }

        .status-cancelled {
            background: #ffebee;
            color: #c62828;
        }

        /* Approval Badge */
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

        /* Priority Badge */
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

        /* Stats Cards */
        .stats-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            border: 1px solid #eaeaea;
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .stats-number {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            line-height: 1;
            margin-bottom: 5px;
        }

        .stats-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }

        .empty-state-icon {
            font-size: 48px;
            color: #e0e0e0;
            margin-bottom: 16px;
        }

        .empty-state-title {
            font-size: 16px;
            color: #666;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .empty-state-text {
            font-size: 13px;
            color: #999;
            margin-bottom: 20px;
        }

        .empty-state-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        /* Buttons */
        .btn-new-ticket {
            background: #070342;
            border: none;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 500;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .btn-new-ticket:hover {
            background: #ff6200;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(255, 98, 0, 0.2);
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
            color: rgb(255, 255, 255);
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

        /* Search */
        .search-container {
            background: #080055;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 15px;
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
            background: #ff6200;
            color: white;
            transform: translateY(-1px);
        }

        /* Pagination */
        .pagination-container {
            margin-top: 15px;
            padding: 12px;
            border-top: 1px solid #eaeaea;
            background: #f9f9f9;
            border-radius: 0 0 8px 8px;
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
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        .pagination .page-link:hover {
            background: #ff6200;
            border-color: #ff6200;
            color: white;
        }

        /* Mobile Optimizations */
        @media (max-width: 768px) {
            .sidebar-filters {
                margin-bottom: 15px;
            }

            .filter-section-header {
                padding: 12px 15px;
                font-size: 13px;
            }

            .filter-link {
                padding: 8px 15px;
                font-size: 12px;
                min-height: 36px;
            }

            .ticket-card {
                padding: 12px;
                margin-bottom: 8px;
            }

            .ticket-title {
                font-size: 13px;
                min-height: 36px;
            }

            .ticket-meta {
                gap: 8px;
                font-size: 10px;
            }

            .ticket-meta-text {
                max-width: 100px;
            }

            .ticket-footer {
                gap: 6px;
            }

            .badge-sm {
                font-size: 9px;
                padding: 2px 6px;
            }

            .stats-card {
                padding: 12px 8px;
                margin-bottom: 10px;
            }

            .stats-number {
                font-size: 20px;
            }

            .stats-label {
                font-size: 11px;
            }

            .empty-state {
                padding: 30px 15px;
            }

            .empty-state-icon {
                font-size: 36px;
            }

            .empty-state-title {
                font-size: 14px;
            }

            .empty-state-text {
                font-size: 12px;
            }

            .btn-create-ticket,
            .btn-clear {
                padding: 6px 12px;
                font-size: 11px;
            }

            .search-container {
                padding: 10px;
                margin-bottom: 10px;
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
                flex-direction: column;
            }

            .search-btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .ticket-meta {
                flex-direction: column;
                gap: 4px;
            }

            .ticket-meta-item {
                width: 100%;
            }

            .ticket-meta-text {
                max-width: 100%;
            }

            .ticket-footer {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }

            .ticket-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .stats-card {
                margin-bottom: 8px;
            }

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
                <!-- New Ticket Button -->
                <div class="p-3 border-bottom">
                    <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-block btn-new-ticket">
                        <i class="fas fa-plus me-2"></i> New Ticket
                    </a>
                </div>

                <!-- Status Filters -->
                <div class="filter-section-header" data-toggle="filter-section" data-target="status-filter">
                    <span>Task Status</span>
                    <i class="fas fa-chevron-up"></i>
                </div>
                <div class="filter-section-content" id="status-filter">
                    <div>
                        <a href="{{ route('tickets.assigned') }}"
                            class="filter-link {{ !request('status') ? 'active' : '' }}">
                            <i class="fas fa-tasks"></i>
                            <span>All My Tasks</span>
                            <span class="badge bg-secondary">{{ $statusCounts['all'] }}</span>
                        </a>
                        <a href="{{ route('tickets.assigned', ['status' => 'open']) }}"
                            class="filter-link {{ request('status') == 'open' ? 'active' : '' }}">
                            <i class="fas fa-folder-open"></i>
                            <span>New Tasks</span>
                            <span class="badge bg-primary">{{ $statusCounts['open'] }}</span>
                        </a>
                        <a href="{{ route('tickets.assigned', ['status' => 'in_progress']) }}"
                            class="filter-link {{ request('status') == 'in_progress' ? 'active' : '' }}">
                            <i class="fas fa-spinner"></i>
                            <span>In Progress</span>
                            <span class="badge bg-info">{{ $statusCounts['in_progress'] }}</span>
                        </a>
                        <a href="{{ route('tickets.assigned', ['status' => 'pending']) }}"
                            class="filter-link {{ request('status') == 'pending' ? 'active' : '' }}">
                            <i class="fas fa-clock"></i>
                            <span>Pending</span>
                            <span class="badge bg-warning">{{ $statusCounts['pending'] }}</span>
                        </a>
                        <a href="{{ route('tickets.assigned', ['status' => 'resolved']) }}"
                            class="filter-link {{ request('status') == 'resolved' ? 'active' : '' }}">
                            <i class="fas fa-check-circle"></i>
                            <span>Resolved</span>
                            <span class="badge bg-success">{{ $statusCounts['resolved'] }}</span>
                        </a>
                        <a href="{{ route('tickets.assigned', ['status' => 'closed']) }}"
                            class="filter-link {{ request('status') == 'closed' ? 'active' : '' }}">
                            <i class="fas fa-times-circle"></i>
                            <span>Closed</span>
                            <span class="badge bg-dark">{{ $statusCounts['closed'] }}</span>
                        </a>
                    </div>
                </div>

                <!-- Approval Status Filters -->
                <div class="filter-section-header collapsed" data-toggle="filter-section" data-target="approval-filter">
                    <span>Approval Status</span>
                    <i class="fas fa-chevron-up"></i>
                </div>
                <div class="filter-section-content collapsed" id="approval-filter">
                    <div>
                        <a href="{{ route('tickets.assigned', ['approval_status' => '']) }}"
                            class="filter-link {{ !request('approval_status') ? 'active' : '' }}">
                            <i class="fas fa-circle"></i>
                            <span>All</span>
                        </a>
                        <a href="{{ route('tickets.assigned', ['approval_status' => 'pending_approval']) }}"
                            class="filter-link {{ request('approval_status') == 'pending_approval' ? 'active' : '' }}">
                            <i class="fas fa-clock text-warning"></i>
                            <span>Pending Approval</span>
                        </a>
                        <a href="{{ route('tickets.assigned', ['approval_status' => 'approved']) }}"
                            class="filter-link {{ request('approval_status') == 'approved' ? 'active' : '' }}">
                            <i class="fas fa-check-circle text-success"></i>
                            <span>Approved</span>
                        </a>
                        <a href="{{ route('tickets.assigned', ['approval_status' => 'rejected']) }}"
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
                        <a href="{{ route('tickets.assigned', ['category' => '']) }}"
                            class="filter-link {{ !request('category') ? 'active' : '' }}">
                            <i class="fas fa-folder"></i>
                            <span>All Categories</span>
                        </a>
                        @foreach ($categories as $category)
                            <a href="{{ route('tickets.assigned', ['category' => $category->id]) }}"
                                class="filter-link {{ request('category') == $category->id ? 'active' : '' }}">
                                <i class="fas fa-folder-open"></i>
                                <span class="d-flex align-items-center">
                                    {{ Str::limit($category->name, 18) }}
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
                        <a href="{{ route('tickets.assigned', ['priority' => '']) }}"
                            class="filter-link {{ !request('priority') ? 'active' : '' }}">
                            <i class="fas fa-flag"></i>
                            <span>All Priorities</span>
                        </a>
                        @foreach ($priorities as $priority)
                            <a href="{{ route('tickets.assigned', ['priority' => $priority->id]) }}"
                                class="filter-link {{ request('priority') == $priority->id ? 'active' : '' }}">
                                <i class="fas fa-flag" style="color: {{ $priority->color }}"></i>
                                <span>{{ $priority->name }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="filter-section-header collapsed" data-toggle="filter-section" data-target="quicklinks-filter">
                    <span>Quick Links</span>
                    <i class="fas fa-chevron-up"></i>
                </div>
                <div class="filter-section-content collapsed" id="quicklinks-filter">
                    <div>
                        <a href="{{ route('tickets.index') }}" class="filter-link">
                            <i class="fas fa-inbox"></i>
                            <span>All Tickets</span>
                        </a>
                        <a href="{{ route('tickets.my-tickets') }}" class="filter-link">
                            <i class="fas fa-file"></i>
                            <span>My Tickets</span>
                        </a>
                        @if (auth()->user()->role === 'admin')
                            <a href="{{ route('tickets.unassigned') }}" class="filter-link">
                                <i class="fas fa-folder"></i>
                                <span>Unassigned</span>
                            </a>
                        @endif
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
                        <form action="{{ route('tickets.assigned') }}" method="GET" id="searchForm">
                            <div class="search-input-group">
                                <input type="text" name="search" class="search-input"
                                    placeholder="Search assigned tickets by number, title, or reporter..."
                                    value="{{ request('search') }}" autocomplete="off">
                                <button type="submit" class="search-btn">
                                    <i class="fas fa-search"></i>
                                    <span class="d-none d-md-inline">Search</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Stats Cards -->
                    <div class="px-2 px-md-3 mb-3">
                        <div class="row">
                            <div class="col-6 col-md-3">
                                <div class="stats-card" style="border-left: 4px solid #007bff;">
                                    <div class="stats-number">{{ $statusCounts['open'] }}</div>
                                    <div class="stats-label">New Tasks</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="stats-card" style="border-left: 4px solid #17a2b8;">
                                    <div class="stats-number">{{ $statusCounts['in_progress'] }}</div>
                                    <div class="stats-label">In Progress</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="stats-card" style="border-left: 4px solid #ffc107;">
                                    <div class="stats-number">{{ $statusCounts['pending'] }}</div>
                                    <div class="stats-label">Pending</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="stats-card" style="border-left: 4px solid #28a745;">
                                    <div class="stats-number">{{ $statusCounts['resolved'] }}</div>
                                    <div class="stats-label">Resolved</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ticket List -->
                    <div class="px-2 px-md-3">
                        @forelse($tickets as $ticket)
                            @php
                                $isUrgent = $ticket->priority->level == 1;
                            @endphp
                            <div class="ticket-card {{ $isUrgent ? 'urgent' : '' }}"
                                onclick="window.location='{{ route('tickets.show', $ticket->id) }}'">
                                <div class="ticket-header">
                                    <div>
                                        <div class="ticket-number">
                                            #{{ $ticket->ticket_number }}
                                            @if ($isUrgent)
                                                <span class="urgent-badge">URGENT</span>
                                            @endif
                                        </div>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <span class="approval-badge approval-{{ $ticket->approval_status }}">
                                                <i
                                                    class="fas {{ $ticket->approval_status == 'approved' ? 'fa-check-circle' : ($ticket->approval_status == 'rejected' ? 'fa-times-circle' : 'fa-clock') }}"></i>
                                                {{ str_replace('_', ' ', $ticket->approval_status) }}
                                            </span>
                                            <span class="status-badge status-{{ $ticket->status }}">
                                                {{ str_replace('_', ' ', $ticket->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="ticket-title" title="{{ $ticket->title }}">
                                    {{ Str::limit($ticket->title, 80) }}
                                </div>

                                <div class="ticket-meta">
                                    <div class="ticket-meta-item">
                                        <i class="fas fa-user"></i>
                                        <span class="ticket-meta-text" title="Reported by {{ $ticket->user->name }}">
                                            {{ Str::limit($ticket->user->name, 20) }}
                                        </span>
                                    </div>

                                    <div class="ticket-meta-item">
                                        <i class="fas fa-folder"></i>
                                        <span class="ticket-meta-text" title="{{ $ticket->category->name }}">
                                            {{ Str::limit($ticket->category->name, 20) }}
                                        </span>
                                    </div>

                                    @if ($ticket->location)
                                        <div class="ticket-meta-item">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span class="ticket-meta-text" title="{{ $ticket->location->name }}">
                                                @if ($ticket->location->floor_number)
                                                    {{ Str::limit($ticket->location->name, 15) }}
                                                    (F{{ $ticket->location->floor_number }})
                                                @else
                                                    {{ Str::limit($ticket->location->name, 18) }}
                                                @endif
                                            </span>
                                        </div>
                                    @endif

                                    <div class="ticket-meta-item">
                                        <i class="fas fa-clock"></i>
                                        <span>{{ $ticket->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>

                                <div class="ticket-footer">
                                    @if ($ticket->estimated_cost)
                                        <span class="badge-sm badge-cost" title="Estimated Cost">
                                            <i class="fas fa-money-bill-wave"></i>
                                            Rp{{ number_format($ticket->estimated_cost, 0, ',', '.') }}
                                        </span>
                                    @endif

                                    @if ($ticket->due_date)
                                        <span class="badge-sm badge-date" title="Due Date">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ $ticket->due_date->format('M d, Y') }}
                                            @if ($ticket->isOverdue())
                                                <i class="fas fa-exclamation-triangle"></i>
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
                                    <i class="fas fa-clipboard-check"></i>
                                </div>
                                <h5 class="empty-state-title">No Tickets Assigned</h5>
                                <p class="empty-state-text">
                                    @if (request()->anyFilled(['search', 'status', 'category', 'priority', 'approval_status']))
                                        Try adjusting your search criteria.
                                    @else
                                        You don't have any tickets assigned to you at the moment.
                                    @endif
                                </p>
                                <div class="empty-state-buttons">
                                    @if (request()->anyFilled(['search', 'status', 'category', 'priority', 'approval_status']))
                                        <a href="{{ route('tickets.assigned') }}" class="btn-clear">
                                            <i class="fas fa-redo"></i>
                                            Clear Filters
                                        </a>
                                    @endif
                                    <a href="{{ route('tickets.index') }}" class="btn-create-ticket">
                                        <i class="fas fa-inbox"></i>
                                        View All Tickets
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
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

            // Mobile optimizations
            function optimizeForMobile() {
                if ($(window).width() < 768) {
                    // Collapse all sections on mobile except status
                    $('.filter-section-header').not('[data-target="status-filter"]').addClass('collapsed');
                    $('.filter-section-content').not('#status-filter').addClass('collapsed');
                    $('.filter-section-header').not('[data-target="status-filter"]').find('i')
                        .removeClass('fa-chevron-up').addClass('fa-chevron-down');
                }
            }

            // Initial optimization
            optimizeForMobile();

            // Debounced resize handler
            let resizeTimer;
            $(window).on('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(optimizeForMobile, 100);
            });
        });
    </script>
@endpush
