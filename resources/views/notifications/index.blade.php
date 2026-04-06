{{-- resources/views/notifications/index.blade.php --}}
@extends('layouts.main')

@section('title', 'Notifications | ' . config('app.name'))

@section('page-title', 'Notification Center')

@section('breadcrumb')
    @php
        $breadcrumb = [['title' => 'Notifications', 'url' => 'javascript:void(0)']];
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
        }

        body {
            background-color: #f4f7fc;
        }

        .notification-page {
            max-width: 1200px;
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

        .stats-icon.unread {
            background: var(--primary-orange);
            color: white;
        }

        .stats-icon.read {
            background: #2ecc71;
            color: white;
        }

        .stats-icon.today {
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

        /* Filter Bar - FIXED */
        .filter-bar {
            background: white;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .filter-group {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            flex: 1 1 auto;
        }

        .filter-select {
            min-width: 170px;
        }

        .filter-select .form-select {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 8px 30px 8px 12px;
            font-size: 13px;
            font-weight: 500;
            color: #495057;
            height: 40px;
            background-position: right 0.75rem center;
        }

        .filter-select .form-select:focus {
            border-color: var(--primary-orange);
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
        }

        .btn-filter {
            background: var(--primary-navy);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 500;
            height: 40px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            white-space: nowrap;
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

        /* Action Buttons - FIXED */
        .action-buttons {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-action {
            border: none;
            border-radius: 10px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 500;
            height: 40px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .btn-action.mark-read {
            background: #2ecc71;
            color: white;
        }

        .btn-action.mark-read:hover:not(:disabled) {
            background: #27ae60;
            transform: translateY(-1px);
        }

        .btn-action.refresh {
            background: #3498db;
            color: white;
        }

        .btn-action.refresh:hover {
            background: #2980b9;
            transform: translateY(-1px);
        }

        .btn-action.clear {
            background: #e74c3c;
            color: white;
        }

        .btn-action.clear:hover:not(:disabled) {
            background: #c0392b;
            transform: translateY(-1px);
        }

        .btn-action.broadcast {
            background: var(--primary-orange);
            color: white;
        }

        .btn-action.broadcast:hover {
            background: #e55a2b;
        }

        .btn-action:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Notifications Container */
        .notifications-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .notification-card {
            padding: 18px 20px;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .notification-card:last-child {
            border-bottom: none;
        }

        .notification-card:hover {
            background: #f8f9fa;
        }

        .notification-card.unread {
            background: #fff9f5;
            border-left: 4px solid var(--primary-orange);
        }

        .notification-content {
            display: flex;
            gap: 15px;
            align-items: flex-start;
        }

        .notification-icon-wrapper {
            flex-shrink: 0;
        }

        .notification-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .notification-icon.info {
            background: #e3f2fd;
            color: #1976d2;
        }

        .notification-icon.success {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .notification-icon.warning {
            background: #fff3e0;
            color: #f57c00;
        }

        .notification-icon.danger {
            background: #ffebee;
            color: #c62828;
        }

        .notification-icon.approval {
            background: #e8f0fe;
            color: var(--primary-navy);
        }

        .notification-icon.assignment {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .notification-icon.vr_request {
            background: #fff3e0;
            color: #f57c00;
        }

        .notification-icon.comment {
            background: #e3f2fd;
            color: #1976d2;
        }

        .notification-icon.broadcast {
            background: linear-gradient(135deg, var(--primary-navy), var(--primary-orange));
            color: white;
        }

        .notification-details {
            flex: 1;
            min-width: 0;
        }

        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 5px;
            gap: 10px;
            flex-wrap: wrap;
        }

        .notification-title {
            font-size: 15px;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .notification-time {
            font-size: 11px;
            color: #999;
            white-space: nowrap;
        }

        .notification-message {
            font-size: 13px;
            color: #666;
            margin: 4px 0;
            line-height: 1.5;
        }

        .notification-ticket-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 10px 12px;
            margin-top: 8px;
            border-left: 3px solid var(--primary-orange);
        }

        .ticket-number {
            font-weight: 600;
            color: var(--primary-navy);
            margin-bottom: 4px;
            font-size: 13px;
        }

        .ticket-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            margin-left: 8px;
        }

        .badge-category {
            background: #e3f2fd;
            color: #1976d2;
        }

        .notification-actions {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-top: 8px;
        }

        .btn-notification {
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 500;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.3s ease;
        }

        .btn-notification.mark-read {
            background: #2ecc71;
            color: white;
        }

        .btn-notification.view {
            background: var(--primary-navy);
            color: white;
        }

        .btn-notification.delete {
            background: #e74c3c;
            color: white;
        }

        .btn-notification:hover {
            transform: translateY(-1px);
            filter: brightness(0.9);
        }

        .unread-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--primary-orange);
            color: white;
            font-size: 9px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 10px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
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
            margin-bottom: 0;
            font-size: 14px;
        }

        /* Pagination - FIXED (copy dari location index) */
        .pagination-wrapper {
            padding: 20px;
            border-top: 1px solid #f0f0f0;
            background: white;
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
            border-radius: 0.75rem;
            font-size: 14px;
            font-weight: 500;
            color: #6e6e6e;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
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

        .pagination .page-item.disabled .page-link {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f8f9fa;
        }

        .pagination .page-item.disabled .page-link:hover {
            background: #f8f9fa;
            color: #6e6e6e;
            border-color: #f0f1f5;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .filter-select {
                min-width: 150px;
            }
        }

        @media (max-width: 992px) {
            .filter-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-group {
                width: 100%;
            }

            .filter-select {
                flex: 1;
            }

            .action-buttons {
                justify-content: flex-end;
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
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

            .filter-group {
                flex-wrap: wrap;
            }

            .filter-select {
                min-width: 100%;
            }

            .action-buttons {
                flex-wrap: wrap;
                gap: 6px;
            }

            .btn-action {
                flex: 1;
                min-width: 100px;
                padding: 8px 10px;
                font-size: 12px;
            }

            .notification-content {
                gap: 12px;
            }

            .notification-header {
                flex-direction: column;
                gap: 4px;
            }

            .notification-time {
                white-space: normal;
            }

            .unread-badge {
                position: relative;
                top: auto;
                right: auto;
                display: inline-block;
                margin-left: 8px;
            }

            .pagination .page-link {
                padding: 6px 12px;
                font-size: 13px;
            }
        }

        @media (max-width: 576px) {
            .stats-grid {
                gap: 8px;
            }

            .stats-card {
                padding: 12px;
                gap: 8px;
            }

            .stats-icon {
                width: 36px;
                height: 36px;
                font-size: 16px;
            }

            .stats-content h3 {
                font-size: 18px;
            }

            .stats-content p {
                font-size: 11px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-action {
                width: 100%;
            }

            .notification-content {
                flex-direction: column;
            }

            .notification-actions {
                justify-content: flex-end;
            }

            .btn-notification {
                flex: 1;
                justify-content: center;
            }

            .pagination {
                flex-wrap: wrap;
                justify-content: center;
                gap: 5px;
            }

            .pagination .page-item {
                margin: 0 2px;
            }

            .pagination .page-link {
                padding: 5px 10px;
                font-size: 12px;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'superadmin';
        $currentType = request('type', 'all');
        $currentDateFilter = request('date_filter', '');
    @endphp

    <div class="notification-page">
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stats-card">
                <div class="stats-icon total"><i class="fas fa-bell"></i></div>
                <div class="stats-content">
                    <h3>{{ number_format($stats['total']) }}</h3>
                    <p>Total</p>
                </div>
            </div>
            <div class="stats-card">
                <div class="stats-icon unread"><i class="fas fa-envelope"></i></div>
                <div class="stats-content">
                    <h3>{{ number_format($stats['unread']) }}</h3>
                    <p>Unread</p>
                </div>
            </div>
            <div class="stats-card">
                <div class="stats-icon read"><i class="fas fa-envelope-open"></i></div>
                <div class="stats-content">
                    <h3>{{ number_format($stats['read']) }}</h3>
                    <p>Read</p>
                </div>
            </div>
            <div class="stats-card">
                <div class="stats-icon today"><i class="fas fa-calendar-day"></i></div>
                <div class="stats-content">
                    <h3>{{ number_format($stats['today']) }}</h3>
                    <p>Today</p>
                </div>
            </div>
        </div>

        <!-- Filter Bar - FIXED -->
        <div class="filter-bar">
            <div class="filter-group">
                <div class="filter-select">
                    <select class="form-select" id="typeFilter">
                        <option value="all" {{ $currentType == 'all' ? 'selected' : '' }}>All Notifications</option>
                        <option value="unread" {{ $currentType == 'unread' ? 'selected' : '' }}>Unread</option>
                        <option value="read" {{ $currentType == 'read' ? 'selected' : '' }}>Read</option>
                        <option value="info" {{ $currentType == 'info' ? 'selected' : '' }}>Info</option>
                        <option value="success" {{ $currentType == 'success' ? 'selected' : '' }}>Success</option>
                        <option value="warning" {{ $currentType == 'warning' ? 'selected' : '' }}>Warning</option>
                        <option value="approval" {{ $currentType == 'approval' ? 'selected' : '' }}>Approval</option>
                        <option value="assignment" {{ $currentType == 'assignment' ? 'selected' : '' }}>Assignment</option>
                        <option value="comment" {{ $currentType == 'comment' ? 'selected' : '' }}>Comments</option>
                        <option value="vr_request" {{ $currentType == 'vr_request' ? 'selected' : '' }}>VR Requests
                        </option>
                        @if ($isSuperAdmin)
                            <option value="broadcast" {{ $currentType == 'broadcast' ? 'selected' : '' }}>Broadcast
                            </option>
                        @endif
                    </select>
                </div>

                <div class="filter-select">
                    <select class="form-select" id="dateFilter">
                        <option value="" {{ !$currentDateFilter ? 'selected' : '' }}>All Time</option>
                        <option value="today" {{ $currentDateFilter == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="yesterday" {{ $currentDateFilter == 'yesterday' ? 'selected' : '' }}>Yesterday
                        </option>
                        <option value="week" {{ $currentDateFilter == 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ $currentDateFilter == 'month' ? 'selected' : '' }}>This Month</option>
                    </select>
                </div>

                <button type="button" class="btn-filter" onclick="applyFilters()">
                    <i class="fas fa-filter me-1"></i>Apply
                </button>

                <button type="button" class="btn-filter reset" onclick="resetFilters()">
                    <i class="fas fa-times me-1"></i>Reset
                </button>
            </div>

            <div class="action-buttons">
                @if ($isSuperAdmin)
                    <button type="button" class="btn-action broadcast" onclick="openBroadcastModal()">
                        <i class="fas fa-bullhorn"></i> <span>Broadcast</span>
                    </button>
                @endif

                @if ($stats['total'] > 0)
                    @if ($stats['unread'] > 0)
                        <button type="button" class="btn-action mark-read" onclick="markAllAsRead()">
                            <i class="fas fa-check-double"></i> <span>Mark All Read</span>
                        </button>
                    @endif

                    <button type="button" class="btn-action refresh" onclick="refreshNotifications()">
                        <i class="fas fa-sync-alt"></i> <span>Refresh</span>
                    </button>

                    <button type="button" class="btn-action clear" onclick="clearAllNotifications()">
                        <i class="fas fa-trash-alt"></i> <span>Clear All</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Notifications List -->
        <div class="notifications-container">
            @if ($notifications->count() > 0)
                @foreach ($notifications as $notification)
                    <div class="notification-card {{ $notification->is_read ? 'read' : 'unread' }}"
                        data-id="{{ $notification->id }}">
                        @if (!$notification->is_read)
                            <span class="unread-badge">NEW</span>
                        @endif

                        <div class="notification-content">
                            <div class="notification-icon-wrapper">
                                <div class="notification-icon {{ $notification->type }}">
                                    @switch($notification->type)
                                        @case('info')
                                            <i class="fas fa-info-circle"></i>
                                        @break

                                        @case('success')
                                            <i class="fas fa-check-circle"></i>
                                        @break

                                        @case('warning')
                                            <i class="fas fa-exclamation-triangle"></i>
                                        @break

                                        @case('error')
                                        @case('danger')
                                            <i class="fas fa-times-circle"></i>
                                        @break

                                        @case('approval')
                                            <i class="fas fa-clipboard-check"></i>
                                        @break

                                        @case('assignment')
                                            <i class="fas fa-user-plus"></i>
                                        @break

                                        @case('vr_request')
                                            <i class="fas fa-file-invoice-dollar"></i>
                                        @break

                                        @case('comment')
                                            <i class="fas fa-comment"></i>
                                        @break

                                        @case('broadcast')
                                            <i class="fas fa-bullhorn"></i>
                                        @break

                                        @default
                                            <i class="fas fa-bell"></i>
                                    @endswitch
                                </div>
                            </div>

                            <div class="notification-details">
                                <div class="notification-header">
                                    <h5 class="notification-title">
                                        {{ $notification->title }}
                                        @if ($notification->type === 'broadcast')
                                            <span class="badge bg-warning ms-1">Broadcast</span>
                                        @endif
                                    </h5>
                                    <span class="notification-time">
                                        <i
                                            class="far fa-clock me-1"></i>{{ $notification->created_at->format('d M Y, H:i') }}
                                    </span>
                                </div>

                                <p class="notification-message">{{ $notification->message }}</p>

                                @if ($notification->ticket)
                                    <div class="notification-ticket-info">
                                        <div class="ticket-number">
                                            <i class="fas fa-ticket-alt me-1"></i>
                                            #{{ $notification->ticket->ticket_number }}
                                            <span class="ticket-badge badge-category">
                                                {{ $notification->ticket->category->name ?? 'N/A' }}
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-1">
                                            <span class="badge status-{{ $notification->ticket->status }}">
                                                {{ str_replace('_', ' ', $notification->ticket->status) }}
                                            </span>
                                            <small><i
                                                    class="fas fa-user me-1"></i>{{ $notification->ticket->user->name ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                @endif

                                <div class="notification-actions">
                                    @if (!$notification->is_read)
                                        <button type="button" class="btn-notification mark-read"
                                            onclick="markAsRead({{ $notification->id }})">
                                            <i class="fas fa-check"></i> Mark Read
                                        </button>
                                    @endif
                                    @if ($notification->ticket_id)
                                        <a href="{{ route('tickets.show', $notification->ticket_id) }}"
                                            class="btn-notification view">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    @endif
                                    <button type="button" class="btn-notification delete"
                                        onclick="deleteNotification({{ $notification->id }})">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Pagination - FIXED (copy dari location index) -->
                <div class="pagination-wrapper">
                    @if ($notifications->hasPages())
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                {{-- Previous Page Link --}}
                                @if ($notifications->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="fa fa-angle-left"></i></span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $notifications->previousPageUrl() }}"
                                            rel="prev">
                                            <i class="fa fa-angle-left"></i>
                                        </a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($notifications->getUrlRange(1, $notifications->lastPage()) as $page => $url)
                                    @if ($page == $notifications->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item"><a class="page-link"
                                                href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if ($notifications->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $notifications->nextPageUrl() }}" rel="next">
                                            <i class="fa fa-angle-right"></i>
                                        </a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="fa fa-angle-right"></i></span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    @endif
                </div>
            @else
                <!-- Empty State - TANPA BUTTON GO TO DASHBOARD -->
                <div class="empty-state">
                    <i class="fas fa-bell-slash"></i>
                    <h4>No Notifications Found</h4>
                    <p>You're all caught up! No notifications match your current filters.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Broadcast Modal -->
    @if ($isSuperAdmin)
        <div class="modal fade" id="broadcastModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header" style="background: var(--primary-navy); color: white;">
                        <h5 class="modal-title"><i class="fas fa-bullhorn me-2"></i>Send Broadcast</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="broadcastForm">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Send To</label>
                                <select class="form-select" id="recipientType" name="recipient_type" required>
                                    <option value="all">All Users</option>
                                    <option value="role">Specific Role</option>
                                    <option value="department">Specific Department</option>
                                </select>
                            </div>

                            <div class="mb-3 d-none" id="roleSelect">
                                <label class="form-label">Select Role</label>
                                <select class="form-select" name="role">
                                    @foreach (['user', 'technician', 'admin_eng', 'manager', 'om', 'gm', 'superadmin'] as $role)
                                        <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3 d-none" id="departmentSelect">
                                <label class="form-label">Select Department</label>
                                <select class="form-select" name="department_id">
                                    @foreach (\App\Models\Department::where('status', 'active')->get() as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Title</label>
                                <input type="text" class="form-control" name="title"
                                    placeholder="e.g., System Maintenance" required maxlength="100">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Message</label>
                                <textarea class="form-control" name="message" rows="4" placeholder="Type your message..." required
                                    maxlength="500"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Priority</label>
                                <select class="form-select" name="priority">
                                    <option value="info">Info (Blue)</option>
                                    <option value="success">Success (Green)</option>
                                    <option value="warning">Warning (Orange)</option>
                                    <option value="danger">Urgent (Red)</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn"
                                style="background: var(--primary-orange); color: white;">
                                <i class="fas fa-paper-plane me-2"></i>Send Broadcast
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
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

        // Apply filters
        function applyFilters() {
            const type = $('#typeFilter').val();
            const dateFilter = $('#dateFilter').val();
            let url = "{{ route('notifications.index') }}";
            let params = [];
            if (type !== 'all') params.push('type=' + type);
            if (dateFilter) params.push('date_filter=' + dateFilter);
            if (params.length > 0) url += '?' + params.join('&');
            window.location.href = url;
        }

        // Reset filters
        function resetFilters() {
            window.location.href = "{{ route('notifications.index') }}";
        }

        // Refresh
        function refreshNotifications() {
            window.location.reload();
        }

        // Mark as read
        function markAsRead(id) {
            $.ajax({
                url: "{{ url('notifications') }}/" + id + "/mark-as-read",
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        toastr.success('Notification marked as read');
                        $(`.notification-card[data-id="${id}"]`).removeClass('unread').addClass('read')
                            .find('.unread-badge, .btn-notification.mark-read').remove();
                        updateNotificationStats();
                    }
                },
                error: function() {
                    toastr.error('Failed');
                }
            });
        }

        // Mark all as read
        function markAllAsRead() {
            Swal.fire({
                title: 'Mark All as Read?',
                text: "This will mark all unread notifications as read.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2ecc71',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, mark all!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('notifications.mark-all-read') }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success('All notifications marked as read');
                                setTimeout(() => window.location.reload(), 1000);
                            }
                        },
                        error: function() {
                            toastr.error('Failed');
                        }
                    });
                }
            });
        }

        // Delete notification
        function deleteNotification(id) {
            Swal.fire({
                title: 'Delete Notification?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('notifications') }}/" + id,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success('Notification deleted');
                                $(`.notification-card[data-id="${id}"]`).fadeOut(300, function() {
                                    $(this).remove();
                                    if ($('.notification-card').length === 0) window.location
                                        .reload();
                                });
                                updateNotificationStats();
                            }
                        },
                        error: function() {
                            toastr.error('Failed');
                        }
                    });
                }
            });
        }

        // Clear all
        function clearAllNotifications() {
            Swal.fire({
                title: 'Clear All Notifications?',
                text: "This will delete ALL your notifications!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                confirmButtonText: 'Yes, clear all!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('notifications.clear-all') }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success('All notifications cleared');
                                setTimeout(() => window.location.reload(), 1000);
                            }
                        },
                        error: function() {
                            toastr.error('Failed');
                        }
                    });
                }
            });
        }

        // Update stats
        function updateNotificationStats() {
            $.ajax({
                url: "{{ route('notifications.unread-count') }}",
                type: 'GET',
                success: function(response) {
                    const $badge = $('#notificationBadge');
                    if (response.count > 0) {
                        if ($badge.length) $badge.text(response.count);
                        else $('.notification-link').append(
                            '<span id="notificationBadge" class="badge badge-danger badge-sm">' + response
                            .count + '</span>');
                    } else $badge.remove();
                }
            });
        }

        @if ($isSuperAdmin)
            function openBroadcastModal() {
                $('#broadcastModal').modal('show');
            }
            $('#recipientType').on('change', function() {
                $('#roleSelect, #departmentSelect').addClass('d-none');
                if ($(this).val() === 'role') $('#roleSelect').removeClass('d-none');
                else if ($(this).val() === 'department') $('#departmentSelect').removeClass('d-none');
            });
            $('#broadcastForm').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#sendBroadcastBtn');
                const originalText = $btn.html();
                $btn.html('<i class="fas fa-spinner fa-spin me-2"></i>Sending...').prop('disabled', true);
                $.ajax({
                    url: "{{ route('notifications.broadcast') }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#broadcastModal').modal('hide');
                        $('#broadcastForm')[0].reset();
                        Swal.fire({
                            icon: 'success',
                            title: 'Broadcast Sent!',
                            text: `Sent to ${response.recipient_count} users.`,
                            confirmButtonColor: '#1a2b4c'
                        }).then(() => refreshNotifications());
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Failed'
                        });
                    },
                    complete: function() {
                        $btn.html(originalText).prop('disabled', false);
                    }
                });
            });
        @endif

        // Auto-mark as read
        $(document).ready(function() {
            $('.notification-card').on('click', function(e) {
                if ($(e.target).closest('button, a').length) return;
                if ($(this).hasClass('unread')) markAsRead($(this).data('id'));
            });
            updateNotificationStats();
        });
    </script>
@endpush

<style>
    .status-open {
        background-color: #17a2b8;
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 10px;
    }

    .status-received {
        background-color: #007bff;
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 10px;
    }

    .status-pending_om,
    .status-pending_vr,
    .status-pending_gm {
        background-color: #ffc107;
        color: #212529;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 10px;
    }

    .status-in_progress {
        background-color: #28a745;
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 10px;
    }

    .status-completed,
    .status-ready_for_closure {
        background-color: #20c997;
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 10px;
    }

    .status-closed {
        background-color: #6c757d;
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 10px;
    }

    .status-cancelled {
        background-color: #dc3545;
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 10px;
    }
</style>
