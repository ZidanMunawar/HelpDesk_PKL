{{-- resources/views/activity-logs/index.blade.php --}}
@extends('layouts.main')

@section('title', 'Activity Logs | ' . config('app.name'))

@section('page-title', 'Activity Logs')

@section('breadcrumb')
    @php
        $breadcrumb = [['title' => 'Activity Logs', 'url' => 'javascript:void(0)']];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">

    <style>
        :root {
            --primary-navy: #1a2b4c;
            --primary-orange: #ff6b35;
            --primary-orange-light: #ff8c5a;
        }

        body {
            background-color: #f4f7fc;
        }

        .activity-log-page {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 18px 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .stats-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .stats-icon.total {
            background: var(--primary-navy);
            color: white;
        }

        .stats-icon.today {
            background: var(--primary-orange);
            color: white;
        }

        .stats-icon.week {
            background: #2ecc71;
            color: white;
        }

        .stats-icon.month {
            background: #3498db;
            color: white;
        }

        .stats-content {
            min-width: 0;
        }

        .stats-content h3 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            color: #333;
            line-height: 1.2;
        }

        .stats-content p {
            margin: 3px 0 0;
            color: #666;
            font-size: 13px;
            font-weight: 500;
            white-space: nowrap;
        }

        /* Filter Card */
        .filter-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .filter-card .form-label {
            font-size: 12px;
            font-weight: 600;
            color: #666;
            margin-bottom: 4px;
        }

        .filter-card .form-select,
        .filter-card .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 13px;
            height: 38px;
        }

        .filter-card .form-select:focus,
        .filter-card .form-control:focus {
            border-color: var(--primary-orange);
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
        }

        .btn-filter {
            background: var(--primary-navy);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 500;
            height: 38px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
        }

        .btn-filter:hover {
            background: #2a3b5c;
            transform: translateY(-1px);
        }

        .btn-filter.reset {
            background: #e74c3c;
        }

        .btn-filter.reset:hover {
            background: #c0392b;
        }

        /* Sidebar Cards */
        .sidebar-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .sidebar-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--primary-navy);
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }

        .sidebar-title i {
            color: var(--primary-orange);
            margin-right: 8px;
        }

        .quick-action-btn {
            width: 100%;
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: 500;
            color: #495057;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quick-action-btn i {
            width: 20px;
            color: var(--primary-orange);
        }

        .quick-action-btn:hover {
            border-color: var(--primary-orange);
            background: #fff9f5;
            transform: translateY(-1px);
        }

        .quick-action-btn.danger:hover {
            border-color: #e74c3c;
            background: #fee;
        }

        .user-avatar-sm {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 13px;
            color: white;
            flex-shrink: 0;
        }

        .user-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px dashed #f0f0f0;
        }

        .user-item:last-child {
            border-bottom: none;
        }

        .user-info {
            flex: 1;
        }

        .user-name {
            font-size: 13px;
            font-weight: 600;
            color: #333;
        }

        .user-role {
            font-size: 11px;
            color: #999;
        }

        .user-count {
            background: #f0f0f0;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            color: #666;
        }

        .action-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            font-size: 13px;
        }

        .action-name {
            color: #666;
        }

        .action-name i {
            color: var(--primary-orange);
            width: 18px;
            margin-right: 6px;
        }

        .action-count {
            background: var(--primary-navy);
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        /* Log Cards */
        .logs-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .log-header {
            background: linear-gradient(135deg, var(--primary-navy), #2a3b5c);
            color: white;
            padding: 20px;
        }

        .log-card {
            padding: 18px 20px;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s ease;
            position: relative;
        }

        .log-card:last-child {
            border-bottom: none;
        }

        .log-card:hover {
            background: #f8f9fa;
        }

        .log-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #e9ecef;
        }

        .log-card.created::before {
            background: #2ecc71;
        }

        .log-card.updated::before {
            background: #3498db;
        }

        .log-card.deleted::before {
            background: #e74c3c;
        }

        .log-card.login::before {
            background: #3498db;
        }

        .log-card.logout::before {
            background: #95a5a6;
        }

        .log-card.received::before {
            background: #3498db;
        }

        .log-card.assigned::before {
            background: var(--primary-navy);
        }

        .log-card.approved::before {
            background: #2ecc71;
        }

        .log-card.rejected::before {
            background: #e74c3c;
        }

        .log-card.commented::before {
            background: #f39c12;
        }

        .log-card.completed::before {
            background: #2ecc71;
        }

        .log-card.cancelled::before {
            background: #95a5a6;
        }

        .log-card.closed::before {
            background: #34495e;
        }

        .log-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .log-icon.created {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .log-icon.updated {
            background: #e3f2fd;
            color: #1976d2;
        }

        .log-icon.deleted {
            background: #ffebee;
            color: #c62828;
        }

        .log-icon.login {
            background: #e3f2fd;
            color: #1976d2;
        }

        .log-icon.logout {
            background: #f5f5f5;
            color: #616161;
        }

        .log-icon.received {
            background: #e3f2fd;
            color: #1976d2;
        }

        .log-icon.assigned {
            background: #e8f0fe;
            color: var(--primary-navy);
        }

        .log-icon.approved {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .log-icon.rejected {
            background: #ffebee;
            color: #c62828;
        }

        .log-icon.commented {
            background: #fff3e0;
            color: #f57c00;
        }

        .log-icon.completed {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .log-icon.cancelled {
            background: #f5f5f5;
            color: #616161;
        }

        .log-icon.closed {
            background: #e8eaf6;
            color: #283593;
        }

        .log-time {
            font-size: 12px;
            color: #999;
        }

        .log-relative-time {
            font-size: 11px;
            color: #bbb;
        }

        .action-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .action-badge.created {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .action-badge.updated {
            background: #e3f2fd;
            color: #1976d2;
        }

        .action-badge.deleted {
            background: #ffebee;
            color: #c62828;
        }

        .action-badge.login {
            background: #e3f2fd;
            color: #1976d2;
        }

        .action-badge.logout {
            background: #f5f5f5;
            color: #616161;
        }

        .action-badge.received {
            background: #e3f2fd;
            color: #1976d2;
        }

        .action-badge.assigned {
            background: #e8f0fe;
            color: var(--primary-navy);
        }

        .action-badge.approved {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .action-badge.rejected {
            background: #ffebee;
            color: #c62828;
        }

        .action-badge.commented {
            background: #fff3e0;
            color: #f57c00;
        }

        .action-badge.completed {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .action-badge.cancelled {
            background: #f5f5f5;
            color: #616161;
        }

        .action-badge.closed {
            background: #e8eaf6;
            color: #283593;
        }

        .ticket-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 10px 12px;
            margin-top: 8px;
            border-left: 3px solid var(--primary-orange);
        }

        .btn-log-action {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-log-action.info {
            background: var(--primary-navy);
        }

        .btn-log-action.danger {
            background: #e74c3c;
        }

        .btn-log-action:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }

        .changes-preview {
            margin-top: 10px;
        }

        .changes-toggle {
            color: var(--primary-orange);
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
        }

        .changes-toggle:hover {
            text-decoration: underline;
        }

        .changes-content {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
            font-family: monospace;
            font-size: 12px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 70px;
            color: #ddd;
            margin-bottom: 15px;
        }

        .empty-state h4 {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .empty-state p {
            color: #666;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .btn-navy {
            background: var(--primary-navy);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-navy:hover {
            background: #2a3b5c;
            transform: translateY(-2px);
        }

        /* Pagination */
        .pagination-wrapper {
            padding: 20px;
            border-top: 1px solid #f0f0f0;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .pagination .page-item {
            margin: 0 3px;
        }

        .pagination .page-link {
            padding: 8px 16px;
            border: 1px solid #f0f1f5;
            background: #fff;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #6e6e6e;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .pagination .page-link:hover {
            background: var(--primary-navy);
            color: white;
            border-color: var(--primary-navy);
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-navy);
            color: white;
            border-color: var(--primary-navy);
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 992px) {
            .filter-card .btn-group {
                margin-top: 10px;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                gap: 12px;
            }

            .stats-card {
                padding: 15px;
            }

            .stats-icon {
                width: 42px;
                height: 42px;
                font-size: 18px;
            }

            .stats-content h3 {
                font-size: 20px;
            }

            .log-card .row {
                flex-direction: column;
            }

            .log-card .col-auto:last-child {
                margin-top: 10px;
                text-align: right;
            }

            .pagination .page-link {
                padding: 6px 12px;
                font-size: 13px;
            }
        }

        @media (max-width: 576px) {
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .stats-card {
                padding: 12px;
            }

            .stats-icon {
                width: 36px;
                height: 36px;
                font-size: 16px;
            }

            .stats-content h3 {
                font-size: 18px;
            }

            .filter-card .row>div {
                margin-bottom: 10px;
            }

            .btn-filter {
                width: 100%;
                justify-content: center;
            }

            .log-card .d-flex {
                flex-direction: column;
                gap: 8px;
            }

            .log-time {
                text-align: left !important;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $user = auth()->user();
        $currentType = request('type', 'all');
        $currentDateFilter = request('date_filter', '');

        // Helper function untuk warna avatar
        function stringToColor($str)
        {
            if (!$str) {
                return '#6c757d';
            }
            $hash = 0;
            for ($i = 0; $i < strlen($str); $i++) {
                $hash = ord($str[$i]) + (($hash << 5) - $hash);
            }
            $color = '#';
            for ($i = 0; $i < 3; $i++) {
                $value = ($hash >> $i * 8) & 0xff;
                $color .= str_pad(dechex($value), 2, '0', STR_PAD_LEFT);
            }
            return $color;
        }

        // Helper function untuk icon action
        function getActionIcon($action)
        {
            $icons = [
                'created' => 'plus-circle',
                'updated' => 'edit',
                'deleted' => 'trash',
                'received' => 'check-circle',
                'assigned' => 'user-plus',
                'approved' => 'thumbs-up',
                'rejected' => 'thumbs-down',
                'completed' => 'check-double',
                'commented' => 'comment',
                'cancelled' => 'ban',
                'closed' => 'lock',
                'login' => 'sign-in-alt',
                'logout' => 'sign-out-alt',
                'vr_created' => 'file-invoice',
                'vr_approved' => 'check-circle',
                'vr_rejected' => 'times-circle',
                'vr_paid' => 'money-check',
                'broadcast_sent' => 'bullhorn',
                'notification_read' => 'envelope-open',
                'notifications_marked_read' => 'check-double',
                'notification_deleted' => 'trash',
                'notifications_cleared' => 'trash-alt',
            ];
            return $icons[$action] ?? 'info-circle';
        }
    @endphp

    <div class="activity-log-page">
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stats-card">
                <div class="stats-icon total"><i class="fas fa-history"></i></div>
                <div class="stats-content">
                    <h3>{{ number_format($stats['total']) }}</h3>
                    <p>Total Logs</p>
                </div>
            </div>
            <div class="stats-card">
                <div class="stats-icon today"><i class="fas fa-calendar-day"></i></div>
                <div class="stats-content">
                    <h3>{{ $stats['today'] }}</h3>
                    <p>Today</p>
                </div>
            </div>
            <div class="stats-card">
                <div class="stats-icon week"><i class="fas fa-calendar-week"></i></div>
                <div class="stats-content">
                    <h3>{{ $stats['last_7_days'] }}</h3>
                    <p>Last 7 Days</p>
                </div>
            </div>
            <div class="stats-card">
                <div class="stats-icon month"><i class="fas fa-calendar-alt"></i></div>
                <div class="stats-content">
                    <h3>{{ $stats['last_30_days'] }}</h3>
                    <p>Last 30 Days</p>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <!-- Quick Actions -->
                <div class="sidebar-card">
                    <h6 class="sidebar-title"><i class="fas fa-bolt"></i> Quick Actions</h6>

                    <button type="button" class="quick-action-btn" onclick="clearOldLogs()">
                        <i class="fas fa-trash-alt"></i>
                        <span>Clear Logs (90+ days)</span>
                    </button>

                    <a href="{{ route('activity-logs.export', request()->query()) }}" class="quick-action-btn">
                        <i class="fas fa-file-export"></i>
                        <span>Export to CSV</span>
                    </a>

                    <button type="button" class="quick-action-btn" onclick="refreshPage()">
                        <i class="fas fa-sync-alt"></i>
                        <span>Refresh</span>
                    </button>
                </div>

                <!-- Top Active Users -->
                <div class="sidebar-card">
                    <h6 class="sidebar-title"><i class="fas fa-users"></i> Top Active Users</h6>
                    @foreach ($activityByUser as $activity)
                        @if ($activity->user)
                            <div class="user-item">
                                <div class="user-avatar-sm"
                                    style="background-color: {{ stringToColor($activity->user->name) }}">
                                    {{ substr($activity->user->name, 0, 1) }}
                                </div>
                                <div class="user-info">
                                    <div class="user-name">{{ $activity->user->name }}</div>
                                    <div class="user-role">{{ ucfirst($activity->user->role) }}</div>
                                </div>
                                <span class="user-count">{{ $activity->count }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>

                <!-- Activity by Action -->
                <div class="sidebar-card">
                    <h6 class="sidebar-title"><i class="fas fa-chart-pie"></i> Activity by Action</h6>
                    @foreach ($activityByAction as $activity)
                        <div class="action-item">
                            <span class="action-name">
                                <i class="fas fa-{{ getActionIcon($activity->action) }}"></i>
                                {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                            </span>
                            <span class="action-count">{{ $activity->count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <!-- Filter Card -->
                <div class="filter-card">
                    <form method="GET" action="{{ route('activity-logs.index') }}" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">User</label>
                                <select name="user_id" class="form-select">
                                    <option value="">All Users</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ ucfirst($user->role) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Ticket</label>
                                <select name="ticket_id" class="form-select">
                                    <option value="">All Tickets</option>
                                    @foreach ($tickets as $ticket)
                                        <option value="{{ $ticket->id }}"
                                            {{ request('ticket_id') == $ticket->id ? 'selected' : '' }}>
                                            #{{ $ticket->ticket_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Action</label>
                                <select name="action" class="form-select">
                                    <option value="">All Actions</option>
                                    @foreach ($actions as $action)
                                        <option value="{{ $action }}"
                                            {{ request('action') == $action ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $action)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">From</label>
                                <input type="date" name="date_from" class="form-control"
                                    value="{{ request('date_from') }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">To</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Search</label>
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search description, user, ticket..." value="{{ request('search') }}">
                                    <button class="btn-filter" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-4 d-flex align-items-end">
                                <div class="btn-group w-100">
                                    <button type="submit" class="btn-filter w-100">
                                        <i class="fas fa-filter me-1"></i> Apply Filters
                                    </button>
                                    <a href="{{ route('activity-logs.index') }}" class="btn-filter reset w-100">
                                        <i class="fas fa-redo me-1"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Logs List -->
                <div class="logs-container">
                    <div class="log-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0"><i class="fas fa-history me-2"></i> Activity Logs</h4>
                                <p class="mb-0 opacity-75">System activity tracking and audit trail</p>
                            </div>
                            <div>
                                <span class="badge bg-light text-dark">{{ $logs->total() }} entries</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-3">
                        @if ($logs->count() > 0)
                            @foreach ($logs as $log)
                                <div class="log-card {{ $log->action }}">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <div class="log-icon {{ $log->action }}">
                                                <i class="fas fa-{{ getActionIcon($log->action) }}"></i>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <span class="action-badge {{ $log->action }}">
                                                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                                    </span>
                                                    @if ($log->user)
                                                        <span class="ms-2 fw-bold">{{ $log->user->name }}</span>
                                                        <span
                                                            class="text-muted small">({{ ucfirst($log->user->role) }})</span>
                                                    @endif
                                                </div>
                                                <div class="text-end">
                                                    <div class="log-time">
                                                        <i class="far fa-clock me-1"></i>
                                                        {{ $log->created_at->format('d M Y, H:i') }}
                                                    </div>
                                                    <div class="log-relative-time">
                                                        {{ $log->created_at->diffForHumans() }}
                                                    </div>
                                                </div>
                                            </div>

                                            <p class="mb-2">{{ $log->description }}</p>

                                            @if ($log->ticket)
                                                <div class="ticket-info">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <span>
                                                            <i class="fas fa-ticket-alt me-1 text-orange"></i>
                                                            <strong>#{{ $log->ticket->ticket_number }}</strong>
                                                        </span>
                                                        <span class="badge status-{{ $log->ticket->status }}">
                                                            {{ str_replace('_', ' ', $log->ticket->status) }}
                                                        </span>
                                                        <span class="small text-muted">{{ $log->ticket->title }}</span>
                                                    </div>
                                                </div>
                                            @endif

                                            @if ($log->ip_address || $log->user_agent)
                                                <div class="mt-2 small text-muted">
                                                    @if ($log->ip_address)
                                                        <span class="me-3">
                                                            <i class="fas fa-globe me-1"></i> {{ $log->ip_address }}
                                                        </span>
                                                    @endif
                                                    @if ($log->user_agent)
                                                        <span>
                                                            <i class="fas fa-desktop me-1"></i>
                                                            {{ \Illuminate\Support\Str::limit($log->user_agent, 40) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif

                                            @if ($log->old_values || $log->new_values)
                                                <div class="changes-preview">
                                                    <a class="changes-toggle" data-bs-toggle="collapse"
                                                        href="#changes-{{ $log->id }}" role="button">
                                                        <i class="fas fa-exchange-alt me-1"></i> View Changes
                                                    </a>
                                                    <div class="collapse" id="changes-{{ $log->id }}">
                                                        <div class="changes-content">
                                                            <div class="row">
                                                                @if ($log->old_values)
                                                                    <div class="col-md-6">
                                                                        <strong class="text-danger">Old Values:</strong>
                                                                        <pre class="mb-0 mt-1"><code>{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</code></pre>
                                                                    </div>
                                                                @endif
                                                                @if ($log->new_values)
                                                                    <div class="col-md-6">
                                                                        <strong class="text-success">New Values:</strong>
                                                                        <pre class="mb-0 mt-1"><code>{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</code></pre>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="col-auto">
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn-log-action info"
                                                    onclick="viewLogDetails({{ $log->id }})" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn-log-action danger"
                                                    onclick="deleteLog({{ $log->id }})" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Pagination -->
                            <div class="pagination-wrapper">
                                {{ $logs->withQueryString()->links() }}
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="empty-state">
                                <i class="fas fa-search"></i>
                                <h4>No Activity Logs Found</h4>
                                <p>No logs match your search criteria. Try adjusting your filters.</p>
                                <a href="{{ route('activity-logs.index') }}" class="btn-navy">
                                    <i class="fas fa-redo me-2"></i>Reset Filters
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Log Details Modal -->
    <div class="modal fade" id="logDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: var(--primary-navy); color: white;">
                    <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i> Log Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="logDetailsContent">
                    <!-- Loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

    <script>
        // Toastr config
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };

        // View log details
        function viewLogDetails(id) {
            $.ajax({
                url: "{{ url('activity-logs') }}/" + id,
                type: 'GET',
                success: function(response) {
                    $('#logDetailsContent').html(response);
                    $('#logDetailsModal').modal('show');
                },
                error: function() {
                    toastr.error('Failed to load log details');
                }
            });
        }

        // Delete log
        function deleteLog(id) {
            Swal.fire({
                title: 'Delete Log?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('activity-logs') }}/" + id,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success('Log deleted successfully');
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            }
                        },
                        error: function() {
                            toastr.error('Failed to delete log');
                        }
                    });
                }
            });
        }

        // Clear old logs
        function clearOldLogs() {
            Swal.fire({
                title: 'Clear Old Logs?',
                text: "This will delete all logs older than 90 days. This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, clear them!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('activity-logs.clear-old') }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Cleared!',
                                    text: response.message,
                                    confirmButtonColor: '#1a2b4c'
                                }).then(() => {
                                    window.location.reload();
                                });
                            }
                        },
                        error: function() {
                            toastr.error('Failed to clear old logs');
                        }
                    });
                }
            });
        }

        // Refresh page
        function refreshPage() {
            window.location.reload();
        }

        // Auto-submit on select change
        $('select[name="user_id"], select[name="ticket_id"], select[name="action"]').on('change', function() {
            $('#filterForm').submit();
        });
    </script>
@endpush

<style>
    .status-open {
        background-color: #17a2b8;
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 11px;
    }

    .status-received {
        background-color: #007bff;
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 11px;
    }

    .status-pending_om,
    .status-pending_vr,
    .status-pending_gm {
        background-color: #ffc107;
        color: #212529;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 11px;
    }

    .status-in_progress {
        background-color: #28a745;
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 11px;
    }

    .status-completed,
    .status-ready_for_closure {
        background-color: #20c997;
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 11px;
    }

    .status-closed {
        background-color: #6c757d;
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 11px;
    }

    .status-cancelled {
        background-color: #dc3545;
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 11px;
    }

    .text-orange {
        color: var(--primary-orange);
    }
</style>
