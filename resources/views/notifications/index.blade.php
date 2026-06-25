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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

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
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .stat-icon.total {
            background: var(--primary-navy);
            color: white;
        }

        .stat-icon.unread {
            background: var(--primary-orange);
            color: white;
        }

        .stat-icon.read {
            background: #2ecc71;
            color: white;
        }

        .stat-icon.today {
            background: #3498db;
            color: white;
        }

        .stat-content h3 {
            font-size: 22px;
            font-weight: 700;
            margin: 0;
            color: #333;
        }

        .stat-content p {
            margin: 2px 0 0;
            color: #666;
            font-size: 12px;
        }

        /* Filter Accordion */
        .filter-accordion {
            background: white;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .filter-accordion .filter-accordion-header {
            padding: 15px 20px;
            background: white;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .filter-accordion .filter-accordion-header:hover {
            background: #f8f9fa;
        }

        .filter-accordion .filter-accordion-header h5 {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            color: var(--primary-navy);
        }

        .filter-accordion .filter-accordion-header i {
            transition: transform 0.3s ease;
        }

        .filter-accordion .filter-accordion-header.active i {
            transform: rotate(180deg);
        }

        .filter-accordion-content {
            display: none;
            padding: 15px 20px;
            border-top: 1px solid #f0f0f0;
            background: #fafafa;
        }

        .filter-accordion-content.show {
            display: block;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .btn-action {
            border: none;
            border-radius: 10px;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-action.mark-read {
            background: #2ecc71;
            color: white;
        }

        .btn-action.refresh {
            background: #3498db;
            color: white;
        }

        .btn-action.clear {
            background: #e74c3c;
            color: white;
        }

        .btn-action.export {
            background: #27ae60;
            color: white;
        }

        .btn-action.broadcast {
            background: var(--primary-orange);
            color: white;
        }

        .btn-action:hover {
            transform: translateY(-1px);
            filter: brightness(0.95);
        }

        .btn-action:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* Date Filter Row */
        .date-filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: flex-end;
        }

        .date-filter-item {
            flex: 1;
            min-width: 140px;
        }

        .date-filter-item label {
            font-size: 11px;
            color: #666;
            margin-bottom: 4px;
            display: block;
        }

        .date-filter-item input {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 12px;
        }

        .type-filter-select {
            flex: 1;
            min-width: 150px;
        }

        .type-filter-select select {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 12px;
            background: white;
        }

        .filter-actions {
            display: flex;
            gap: 8px;
            align-items: flex-end;
        }

        .btn-filter {
            background: var(--primary-navy);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 12px;
            cursor: pointer;
        }

        .btn-filter.reset {
            background: #95a5a6;
        }

        /* Notifications Container */
        .notifications-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        /* Date Accordion */
        .date-accordion {
            border-bottom: 1px solid #f0f0f0;
        }

        .date-accordion:last-child {
            border-bottom: none;
        }

        .date-accordion .date-accordion-header {
            padding: 15px 20px;
            background: #f8f9fa;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .date-accordion .date-accordion-header:hover {
            background: #f0f0f0;
        }

        .date-accordion .date-accordion-header h4 {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            color: var(--primary-navy);
        }

        .date-accordion .date-accordion-header .badge-count {
            background: var(--primary-orange);
            color: white;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 11px;
            margin-left: 10px;
        }

        .date-accordion .date-accordion-header i {
            transition: transform 0.3s ease;
            color: #999;
        }

        .date-accordion .date-accordion-header.active i {
            transform: rotate(180deg);
        }

        .date-accordion-content {
            display: none;
        }

        .date-accordion-content.show {
            display: block;
        }

        /* Notification Card */
        .notification-card {
            padding: 15px 20px;
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
            border-left: 3px solid var(--primary-orange);
        }

        .notification-content {
            display: flex;
            flex-direction: row;
            gap: 15px;
            align-items: flex-start;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .bg-info-light {
            background: #e3f2fd;
            color: #1976d2;
        }

        .bg-success-light {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .bg-warning-light {
            background: #fff3e0;
            color: #f57c00;
        }

        .bg-danger-light {
            background: #ffebee;
            color: #c62828;
        }

        .bg-primary-light {
            background: #e8f0fe;
            color: var(--primary-navy);
        }

        .bg-secondary-light {
            background: #f5f5f5;
            color: #757575;
        }

        .bg-gradient {
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
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 5px;
        }

        .notification-title {
            font-size: 14px;
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
            font-size: 12px;
            color: #666;
            margin: 4px 0;
            line-height: 1.4;
        }

        .notification-ticket-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 8px 12px;
            margin-top: 8px;
            border-left: 2px solid var(--primary-orange);
        }

        .ticket-number {
            font-weight: 600;
            color: var(--primary-navy);
            font-size: 12px;
        }

        .notification-actions {
            display: flex;
            gap: 8px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .btn-notification {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-notification.mark-read {
            background: #2ecc71;
            color: white;
        }

        .btn-notification.view {
            background: var(--primary-navy);
            color: white;
            text-decoration: none;
            display: inline-block;
        }

        .btn-notification.delete {
            background: #e74c3c;
            color: white;
        }

        .btn-notification:hover {
            opacity: 0.9;
        }

        /* Pagination Info */
        .pagination-info {
            padding: 10px 15px;
            background: #f8f9fa;
            border-radius: 8px;
            margin: 15px 0;
            text-align: center;
        }

        .pagination-info small {
            font-size: 13px;
            font-weight: 500;
            color: #666;
        }

        /* Pagination Styling */
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
            display: flex;
            list-style: none;
            padding-left: 0;
        }

        .pagination .page-item {
            margin: 0;
        }

        .pagination .page-link {
            font-size: 13px;
            padding: 8px 14px;
            border: 1px solid #ddd;
            background: #fff;
            color: #666;
            border-radius: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        .pagination .page-link i {
            font-size: 12px;
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

        .pagination .page-item.disabled .page-link {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f8f9fa;
            color: #999;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
        }

        .empty-state i {
            font-size: 60px;
            color: #ddd;
            margin-bottom: 15px;
        }

        .empty-state h4 {
            font-size: 18px;
            color: #333;
            margin-bottom: 8px;
        }

        .empty-state p {
            color: #666;
            font-size: 13px;
        }

        /* Loading Overlay */
        .ajax-loading {
            position: relative;
            min-height: 200px;
        }

        .ajax-loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 12px;
            z-index: 10;
        }

        .ajax-loading::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40px;
            height: 40px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--primary-orange);
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

        /* Responsive */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .stat-icon {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }

            .stat-content h3 {
                font-size: 18px;
            }

            .date-filter-row {
                flex-direction: column;
            }

            .date-filter-item,
            .type-filter-select {
                width: 100%;
            }

            .notification-header {
                flex-direction: column;
                gap: 4px;
            }

            .notification-time {
                white-space: normal;
            }

            .notification-content {
                flex-direction: row !important;
            }
        }

        @media (max-width: 576px) {
            .stats-grid {
                gap: 8px;
            }

            .stat-card {
                padding: 10px;
            }

            .notification-icon {
                width: 35px;
                height: 35px;
                font-size: 14px;
            }

            .pagination .page-link {
                padding: 5px 8px;
                font-size: 11px;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $user = auth()->user();
        $canBroadcast = in_array($user->role, ['superadmin', 'admin_eng']);
    @endphp

    <div class="notification-page">
        <!-- Stats Cards -->
        <div class="stats-grid">
            {{-- <div class="stat-card">
                <div class="stat-icon total"><i class="fas fa-bell"></i></div>
                <div class="stat-content">
                    <h3 id="statTotal">{{ number_format($stats['total']) }}</h3>
                    <p>Total</p>
                </div>
            </div> --}}
            <div class="stat-card">
                <div class="stat-icon unread"><i class="fas fa-envelope"></i></div>
                <div class="stat-content">
                    <h3 id="statUnread">{{ number_format($stats['unread']) }}</h3>
                    <p>Unread</p>
                </div>
            </div>
            {{-- <div class="stat-card">
                <div class="stat-icon read"><i class="fas fa-envelope-open"></i></div>
                <div class="stat-content">
                    <h3 id="statRead">{{ number_format($stats['read']) }}</h3>
                    <p>Read</p>
                </div>
            </div> --}}
            <div class="stat-card">
                <div class="stat-icon today"><i class="fas fa-calendar-day"></i></div>
                <div class="stat-content">
                    <h3 id="statToday">{{ number_format($stats['today']) }}</h3>
                    <p>Today</p>
                </div>
            </div>
        </div>

        <!-- Filter Accordion -->
        <div class="filter-accordion">
            <div class="filter-accordion-header" id="filterAccordionHeader">
                <h5><i class="fas fa-filter me-2"></i> Filter Notifications</h5>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="filter-accordion-content" id="filterAccordionContent">
                <div class="date-filter-row">
                    {{-- <div class="type-filter-select">
                        <label><i class="fas fa-tag"></i> Type</label>
                        <select name="type" id="filterType" class="form-select">
                            @foreach ($notificationTypes as $key => $label)
                                <option value="{{ $key }}"
                                    {{ ($currentFilters['type'] ?? 'all') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div> --}}
                    <div class="date-filter-item">
                        <label><i class="fas fa-calendar-alt"></i> Start Date</label>
                        <input type="text" name="start_date" id="startDateFilter" class="flatpickr-input"
                            value="{{ $currentFilters['start_date'] ?? '' }}" placeholder="YYYY-MM-DD">
                    </div>
                    <div class="date-filter-item">
                        <label><i class="fas fa-calendar-alt"></i> End Date</label>
                        <input type="text" name="end_date" id="endDateFilter" class="flatpickr-input"
                            value="{{ $currentFilters['end_date'] ?? '' }}" placeholder="YYYY-MM-DD">
                    </div>
                    <div class="filter-actions">
                        <button type="button" class="btn-filter" id="applyFiltersBtn"><i class="fas fa-check"></i>
                            Apply</button>
                        <button type="button" class="btn-filter reset" id="resetFiltersBtn"><i class="fas fa-redo"></i>
                            Reset</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            @if ($stats['unread'] > 0)
                <button class="btn-action mark-read" id="markAllReadBtn">
                    <i class="fas fa-check-double"></i> Mark All Read
                </button>
            @endif
            <button class="btn-action export" id="exportBtn">
                <i class="fas fa-file-csv"></i> Export CSV
            </button>
            @if ($canBroadcast)
                <button class="btn-action broadcast" id="broadcastBtn">
                    <i class="fas fa-bullhorn"></i> Broadcast
                </button>
            @endif
            @if ($stats['total'] > 0)
                <button class="btn-action clear" id="clearAllBtn">
                    <i class="fas fa-trash-alt"></i> Clear All
                </button>
            @endif
        </div>

        <!-- Notifications Container -->
        <div id="notifications-container">
            @include('notifications.partials.notification-list', [
                'notifications' => $notifications,
                'groupedData' => $groupedData,
                'totalNotifications' => $totalNotifications,
            ])
        </div>
    </div>

    <!-- Broadcast Modal -->
    @if ($canBroadcast)
        <div class="modal fade" id="broadcastModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header" style="background: var(--primary-navy); color: white;">
                        <h5 class="modal-title" style="color: white"><i class="fas fa-bullhorn me-2"
                                style="color: white;"></i>Send Broadcast</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
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
                                    @foreach ($departments as $dept)
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Toastr config
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };

        // Initialize Flatpickr
        flatpickr("#startDateFilter", {
            dateFormat: "Y-m-d",
            allowInput: true
        });
        flatpickr("#endDateFilter", {
            dateFormat: "Y-m-d",
            allowInput: true
        });

        // =============================================
        // UPDATE STATS (LANGSUNG PAKAI DARI SERVER)
        // =============================================
        function updateStats(stats) {
            $('#statTotal').text(stats.total);
            $('#statUnread').text(stats.unread);
            $('#statRead').text(stats.read);
            $('#statToday').text(stats.today);

            // Update tombol Mark All Read
            if (stats.unread > 0) {
                if ($('#markAllReadBtn').length === 0) {
                    $('.action-buttons').prepend(`
                    <button class="btn-action mark-read" id="markAllReadBtn">
                        <i class="fas fa-check-double"></i> Mark All Read
                    </button>
                `);
                    // Re-attach event listener
                    attachMarkAllReadEvent();
                }
            } else {
                $('#markAllReadBtn').remove();
            }

            // Update tombol Clear All
            if (stats.total > 0) {
                if ($('#clearAllBtn').length === 0) {
                    $('.action-buttons').append(`
                    <button class="btn-action clear" id="clearAllBtn">
                        <i class="fas fa-trash-alt"></i> Clear All
                    </button>
                `);
                    // Re-attach event listener
                    attachClearAllEvent();
                }
            } else {
                $('#clearAllBtn').remove();
            }
        }

        // =============================================
        // FUNGSI UPDATE HEADER (SINKRONISASI)
        // =============================================
        function updateHeaderBadge() {
            $.ajax({
                url: "{{ route('notifications.unread-count') }}",
                method: 'GET',
                success: function(response) {
                    const $badge = $('#headerNotificationBadge');

                    if (response.count > 0) {
                        if ($badge.length) {
                            $badge.text(response.count);
                        } else {
                            $('.notification_dropdown .nav-link').append(
                                '<span class="badge light text-white bg-danger rounded-circle" id="headerNotificationBadge">' +
                                response.count + '</span>'
                            );
                        }
                    } else {
                        $badge.remove();
                        $('button[onclick="markAllNotificationsRead(event)"]').remove();
                    }
                }
            });
        }

        function refreshHeaderNotificationList() {
            $.ajax({
                url: "{{ route('notifications.latest') }}",
                method: 'GET',
                success: function(notifications) {
                    const $list = $('#headerNotificationList');

                    if (!notifications || notifications.length === 0) {
                        $list.html(`
                        <li class="text-center py-4">
                            <div class="mb-3">
                                <i class="fas fa-bell-slash fa-3x" style="color: #ddd;"></i>
                            </div>
                            <p class="text-muted mb-0" style="font-size: 13px;">No notifications yet</p>
                            <small class="text-muted">We'll notify you when something arrives</small>
                        </li>
                    `);
                        return;
                    }

                    let html = '';
                    notifications.forEach(notif => {
                        const isUnread = !notif.is_read;
                        const iconMap = {
                            info: {
                                icon: 'info-circle',
                                color: '#3498db'
                            },
                            success: {
                                icon: 'check-circle',
                                color: '#2ecc71'
                            },
                            warning: {
                                icon: 'exclamation-triangle',
                                color: '#f39c12'
                            },
                            danger: {
                                icon: 'times-circle',
                                color: '#e74c3c'
                            },
                            approval: {
                                icon: 'clipboard-check',
                                color: '#1a2b4c'
                            },
                            assignment: {
                                icon: 'user-plus',
                                color: '#27ae60'
                            },
                            vr_request: {
                                icon: 'file-invoice-dollar',
                                color: '#e67e22'
                            },
                            closure: {
                                icon: 'check-circle',
                                color: '#27ae60'
                            },
                            comment: {
                                icon: 'comment',
                                color: '#3498db'
                            },
                            broadcast: {
                                icon: 'bullhorn',
                                color: '#FF7B00'
                            }
                        };
                        const iconData = iconMap[notif.type] || {
                            icon: 'bell',
                            color: '#95a5a6'
                        };

                        html += `
                        <li class="notification-item ${isUnread ? 'unread' : ''}"
                            data-id="${notif.id}"
                            style="${isUnread ? 'background: #fff9f5; border-left: 3px solid #FF7B00;' : ''} padding: 10px; border-radius: 8px; margin-bottom: 8px; cursor: pointer;">
                            <div class="timeline-panel d-flex">
                                <div class="me-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px; background: ${iconData.color}15;">
                                        <i class="fas fa-${iconData.icon}"
                                            style="color: ${iconData.color}; font-size: 18px;"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h6 class="mb-1 fw-semibold" style="font-size: 13px; color: #333;">
                                            ${escapeHtml(notif.title)}</h6>
                                        ${isUnread ? '<span class="badge badge-xs" style="background: #FF7B00; color: white; font-size: 9px;">NEW</span>' : ''}
                                    </div>
                                    <p class="mb-1 text-muted" style="font-size: 11px; line-height: 1.4;">
                                        ${escapeHtml(notif.message.substring(0, 60))}${notif.message.length > 60 ? '...' : ''}
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <small class="text-muted" style="font-size: 10px;">
                                            <i class="far fa-clock me-1"></i>${notif.time}
                                        </small>
                                        <div>
                                            ${notif.ticket_id ? `<a href="{{ url('tickets') }}/${notif.ticket_id}" class="badge badge-sm text-decoration-none" style="background: #1a2b4c; color: white; font-size: 10px; padding: 4px 8px;" onclick="event.stopPropagation();">
                                                                <i class="fas fa-ticket-alt me-1"></i>View Ticket
                                                            </a>` : ''}
                                            ${isUnread ? `<button class="badge badge-sm border-0 ms-1 mark-single-read" onclick="markSingleNotificationReadFromHeader(event, ${notif.id})" style="background: #2ecc71; color: white; font-size: 10px; padding: 4px 8px; cursor: pointer;">
                                                                <i class="fas fa-check me-1"></i>Read
                                                            </button>` : ''}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    `;
                    });

                    $list.html(html);

                    // Tambahin event click ke seluruh notification item
                    $('.notification-item').off('click').on('click', function(e) {
                        if ($(e.target).closest('a, button').length) return;
                        window.location.href = "{{ route('notifications.index') }}";
                    });
                }
            });
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function syncHeaderAfterAction() {
            updateHeaderBadge();
            refreshHeaderNotificationList();
        }

        // =============================================
        // FUNGSI UNTUK HEADER (biar bisa dipanggil dari halaman)
        // =============================================
        window.markSingleNotificationReadFromHeader = function(event, id) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            const $item = $(event.target).closest('.notification-item');

            $.ajax({
                url: "{{ url('notifications') }}/mark-read/" + id,
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        $item.css({
                            'background': 'transparent',
                            'border-left': 'none'
                        });
                        $item.find('.badge-danger, span[style*="background: #FF7B00"]').remove();
                        $item.find('.mark-single-read').remove();

                        const $badge = $('#headerNotificationBadge');
                        if ($badge.length) {
                            const count = parseInt($badge.text()) - 1;
                            if (count > 0) {
                                $badge.text(count);
                            } else {
                                $badge.remove();
                                $('button[onclick="markAllNotificationsRead(event)"]').remove();
                            }
                        }

                        refreshHeaderNotificationList();

                        // Refresh halaman notifikasi jika terbuka
                        if (typeof loadNotifications === 'function') {
                            let url = "{{ route('notifications.index') }}?";
                            const type = $('#filterType').val();
                            const startDate = $('#startDateFilter').val();
                            const endDate = $('#endDateFilter').val();
                            const currentPage = $('.pagination .active .ajax-page').data('page') || 1;

                            if (type && type !== 'all') url += "type=" + type + "&";
                            if (startDate) url += "start_date=" + startDate + "&";
                            if (endDate) url += "end_date=" + endDate + "&";
                            url += "page=" + currentPage;

                            loadNotifications(url);
                        }

                        toastr.success('Marked as read');
                    }
                },
                error: function() {
                    toastr.error('Failed to mark as read');
                }
            });
        };

        // =============================================
        // LOAD NOTIFICATIONS AJAX
        // =============================================
        function loadNotifications(url) {
            const container = $('#notifications-container');
            container.addClass('ajax-loading');

            $.ajax({
                url: url,
                method: 'GET',
                data: {
                    ajax: true
                },
                success: function(response) {
                    container.removeClass('ajax-loading');
                    container.html(response.html);
                    reinitDateAccordions();

                    if (response.stats) {
                        updateStats(response.stats);
                    }

                    // SINKRONISASI HEADER
                    syncHeaderAfterAction();
                },
                error: function(xhr) {
                    container.removeClass('ajax-loading');
                    console.error(xhr);
                    toastr.error('Failed to load notifications');
                }
            });
        }

        // =============================================
        // FILTER ACCORDION
        // =============================================
        $('#filterAccordionHeader').off('click.filterAccordion').on('click.filterAccordion', function(e) {
            e.stopPropagation();
            $(this).toggleClass('active');
            $('#filterAccordionContent').toggleClass('show');
        });

        // =============================================
        // REINITIALIZE DATE ACCORDIONS
        // =============================================
        function reinitDateAccordions() {
            $('.date-accordion .date-accordion-header').off('click.dateAccordion');
            $('.date-accordion .date-accordion-header').on('click.dateAccordion', function(e) {
                e.stopPropagation();
                var $header = $(this);
                var $content = $header.next('.date-accordion-content');
                $header.toggleClass('active');
                $content.toggleClass('show');
            });
        }

        // =============================================
        // APPLY FILTERS
        // =============================================
        $('#applyFiltersBtn').on('click', function() {
            let url = "{{ route('notifications.index') }}?";
            const type = $('#filterType').val();
            const startDate = $('#startDateFilter').val();
            const endDate = $('#endDateFilter').val();

            if (type && type !== 'all') url += "type=" + type + "&";
            if (startDate) url += "start_date=" + startDate + "&";
            if (endDate) url += "end_date=" + endDate + "&";

            loadNotifications(url);
        });

        // =============================================
        // RESET FILTERS
        // =============================================
        $('#resetFiltersBtn').on('click', function() {
            $('#filterType').val('all');
            $('#startDateFilter').val('');
            $('#endDateFilter').val('');
            loadNotifications("{{ route('notifications.index') }}");
        });

        // =============================================
        // PAGINATION AJAX
        // =============================================
        $(document).on('click', '.ajax-page', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            let url = "{{ route('notifications.index') }}?page=" + page;

            const type = $('#filterType').val();
            const startDate = $('#startDateFilter').val();
            const endDate = $('#endDateFilter').val();

            if (type && type !== 'all') url += "&type=" + type;
            if (startDate) url += "&start_date=" + startDate;
            if (endDate) url += "&end_date=" + endDate;

            loadNotifications(url);
        });

        // =============================================
        // MARK SINGLE AS READ (DARI HALAMAN NOTIF)
        // =============================================
        window.markSingleAsRead = function(id, event) {
            event.stopPropagation();

            $.ajax({
                url: "{{ url('notifications') }}/mark-read/" + id,
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        const $card = $(`.notification-card[data-id="${id}"]`);
                        $card.removeClass('unread');
                        $card.find('.mark-read').remove();

                        // SINKRONISASI HEADER
                        syncHeaderAfterAction();

                        // Reload current page
                        let url = "{{ route('notifications.index') }}?";
                        const type = $('#filterType').val();
                        const startDate = $('#startDateFilter').val();
                        const endDate = $('#endDateFilter').val();
                        const currentPage = $('.pagination .active .ajax-page').data('page') || 1;

                        if (type && type !== 'all') url += "type=" + type + "&";
                        if (startDate) url += "start_date=" + startDate + "&";
                        if (endDate) url += "end_date=" + endDate + "&";
                        url += "page=" + currentPage;

                        loadNotifications(url);
                        toastr.success('Marked as read');
                    }
                },
                error: function() {
                    toastr.error('Failed to mark as read');
                }
            });
        };

        // =============================================
        // MARK ALL AS READ EVENT
        // =============================================
        function attachMarkAllReadEvent() {
            $('#markAllReadBtn').off('click').on('click', function() {
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
                            method: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                if (response.success) {
                                    // SINKRONISASI HEADER
                                    syncHeaderAfterAction();

                                    // Reload current page
                                    let url = "{{ route('notifications.index') }}?";
                                    const type = $('#filterType').val();
                                    const startDate = $('#startDateFilter').val();
                                    const endDate = $('#endDateFilter').val();

                                    if (type && type !== 'all') url += "type=" + type + "&";
                                    if (startDate) url += "start_date=" + startDate + "&";
                                    if (endDate) url += "end_date=" + endDate + "&";

                                    loadNotifications(url);
                                    toastr.success('All notifications marked as read');
                                }
                            },
                            error: function() {
                                toastr.error('Failed');
                            }
                        });
                    }
                });
            });
        }

        // =============================================
        // DELETE SINGLE NOTIFICATION
        // =============================================
        window.deleteNotification = function(id, event) {
            event.stopPropagation();

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
                        method: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                // SINKRONISASI HEADER
                                syncHeaderAfterAction();

                                // Reload current page
                                let url = "{{ route('notifications.index') }}?";
                                const type = $('#filterType').val();
                                const startDate = $('#startDateFilter').val();
                                const endDate = $('#endDateFilter').val();
                                const currentPage = $('.pagination .active .ajax-page').data(
                                    'page') || 1;

                                if (type && type !== 'all') url += "type=" + type + "&";
                                if (startDate) url += "start_date=" + startDate + "&";
                                if (endDate) url += "end_date=" + endDate + "&";
                                url += "page=" + currentPage;

                                loadNotifications(url);
                                toastr.success('Notification deleted');
                            }
                        },
                        error: function() {
                            toastr.error('Failed');
                        }
                    });
                }
            });
        };

        // =============================================
        // CLEAR ALL EVENT
        // =============================================
        function attachClearAllEvent() {
            $('#clearAllBtn').off('click').on('click', function() {
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
                            method: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                if (response.success) {
                                    // SINKRONISASI HEADER
                                    syncHeaderAfterAction();

                                    loadNotifications("{{ route('notifications.index') }}");
                                    toastr.success('All notifications cleared');
                                }
                            },
                            error: function(xhr) {
                                console.log(xhr);
                                toastr.error('Failed to clear notifications');
                            }
                        });
                    }
                });
            });
        }

        // =============================================
        // EXPORT CSV
        // =============================================
        $('#exportBtn').on('click', function() {
            let url = "{{ route('notifications.export') }}?";
            url += "type=" + ($('#filterType').val() || 'all');
            if ($('#startDateFilter').val()) url += "&start_date=" + $('#startDateFilter').val();
            if ($('#endDateFilter').val()) url += "&end_date=" + $('#endDateFilter').val();
            window.open(url, '_blank');
        });

        // =============================================
        // AUTO MARK AS READ (KLIK CARD)
        // =============================================
        $(document).on('click', '.notification-card', function(e) {
            if ($(e.target).closest('button, a').length) return;

            const $card = $(this);
            const id = $card.data('id');
            const ticketId = $card.data('ticket-id');

            if ($card.hasClass('unread')) {
                $.ajax({
                    url: "{{ url('notifications') }}/mark-read/" + id,
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    async: false
                });

                // SINKRONISASI HEADER
                syncHeaderAfterAction();

                // Reload to update stats
                let url = "{{ route('notifications.index') }}?";
                const type = $('#filterType').val();
                const startDate = $('#startDateFilter').val();
                const endDate = $('#endDateFilter').val();
                const currentPage = $('.pagination .active .ajax-page').data('page') || 1;

                if (type && type !== 'all') url += "type=" + type + "&";
                if (startDate) url += "start_date=" + startDate + "&";
                if (endDate) url += "end_date=" + endDate + "&";
                url += "page=" + currentPage;

                loadNotifications(url);
            }

            if (ticketId) {
                window.location.href = "{{ url('tickets') }}/" + ticketId;
            }
        });

        // =============================================
        // BROADCAST FUNCTIONALITY
        // =============================================
        @if ($canBroadcast)
            $('#broadcastBtn').on('click', function() {
                $('#broadcastModal').modal('show');
            });

            $('#recipientType').on('change', function() {
                $('#roleSelect, #departmentSelect').addClass('d-none');
                if ($(this).val() === 'role') $('#roleSelect').removeClass('d-none');
                else if ($(this).val() === 'department') $('#departmentSelect').removeClass('d-none');
            });

            $('#broadcastForm').on('submit', function(e) {
                e.preventDefault();
                const $btn = $(this).find('button[type="submit"]');
                const originalText = $btn.html();
                $btn.html('<i class="fas fa-spinner fa-spin me-2"></i>Sending...').prop('disabled', true);

                $.ajax({
                    url: "{{ route('notifications.broadcast') }}",
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#broadcastModal').modal('hide');
                        $('#broadcastForm')[0].reset();
                        $('#roleSelect, #departmentSelect').addClass('d-none');

                        // SINKRONISASI HEADER
                        syncHeaderAfterAction();

                        Swal.fire({
                            icon: 'success',
                            title: 'Broadcast Sent!',
                            text: `Sent to ${response.recipient_count} users.`,
                            confirmButtonColor: '#1a2b4c'
                        });
                    },
                    error: function(xhr) {
                        let errorMsg = 'Failed to send broadcast';
                        if (xhr.responseJSON && xhr.responseJSON.message) errorMsg = xhr.responseJSON
                            .message;
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg
                        });
                    },
                    complete: function() {
                        $btn.html(originalText).prop('disabled', false);
                    }
                });
            });
        @endif

        // =============================================
        // INITIAL SETUP
        // =============================================
        $(document).ready(function() {
            reinitDateAccordions();
            attachMarkAllReadEvent();
            attachClearAllEvent();

            // Initial sync header
            syncHeaderAfterAction();
        });
    </script>
@endpush
