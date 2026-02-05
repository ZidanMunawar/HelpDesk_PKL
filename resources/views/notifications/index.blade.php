@extends('layouts.main')

@section('title', 'Notifications | ' . config('app.name'))

@section('page-title', 'Notifications')

@section('breadcrumb')
    @php
        $breadcrumb = [['title' => 'Notifications', 'url' => 'javascript:void(0)']];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

@push('styles')
    <style>
        :root {
            --primary-color: #ff6200;
            --secondary-color: #ff7b00;
        }

        .notification-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .notification-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 10px 10px 0 0;
            padding: 20px;
        }

        .notification-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            position: relative;
        }

        .notification-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .notification-card.unread {
            border-left: 4px solid var(--primary-color);
            background: #f8f9fa;
        }

        .notification-card.read {
            opacity: 0.9;
            background: #fff;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
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
            background: #e3f2fd;
            color: #1565c0;
        }

        .notification-icon.assignment {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .notification-icon.comment {
            background: #fff3e0;
            color: #ef6c00;
        }

        .notification-icon.vr {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        .notification-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 10px;
        }

        .notification-time {
            font-size: 12px;
            color: #6c757d;
        }

        .notification-action-btn {
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 15px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }

        .notification-action-btn:hover {
            transform: scale(1.05);
        }

        .stats-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .stats-icon.total {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .stats-icon.unread {
            background: linear-gradient(135deg, #f093fb, #f5576c);
            color: white;
        }

        .stats-icon.read {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            color: white;
        }

        .stats-icon.today {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            color: white;
        }

        .filter-badge {
            cursor: pointer;
            transition: all 0.3s;
        }

        .filter-badge:hover {
            transform: scale(1.05);
        }

        .filter-badge.active {
            background: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }

        .empty-state {
            padding: 50px 20px;
            text-align: center;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 60px;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .notification-ticket-info {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 10px;
            margin-top: 10px;
            border-left: 3px solid #dee2e6;
        }

        .notification-ticket-info strong {
            color: #495057;
        }

        .btn-action-sm {
            padding: 5px 12px;
            font-size: 12px;
            border-radius: 6px;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .notification-header {
                padding: 15px;
            }

            .notification-card {
                margin-bottom: 10px;
            }

            .notification-icon {
                width: 35px;
                height: 35px;
                font-size: 16px;
            }

            .stats-card {
                padding: 12px;
                margin-bottom: 10px;
            }

            .stats-icon {
                width: 40px;
                height: 40px;
                font-size: 20px;
            }

            .btn-action-sm {
                padding: 4px 10px;
                font-size: 11px;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $user = auth()->user();
        $filterType = request()->get('type', 'all');
        $dateFilter = request()->get('date_filter', '');
    @endphp

    <div class="row">
        <!-- Statistics -->
        <div class="col-lg-3 col-md-12 mb-4">
            <div class="notification-container">
                <div class="p-3">
                    <h5 class="mb-3"><i class="fas fa-chart-bar me-2"></i> Overview</h5>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="stats-card text-center">
                                <div class="stats-icon total mx-auto">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <h4 class="mb-1">{{ $stats['total'] }}</h4>
                                <p class="mb-0 text-muted small">Total</p>
                            </div>
                        </div>

                        <div class="col-6 mb-3">
                            <div class="stats-card text-center">
                                <div class="stats-icon unread mx-auto">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <h4 class="mb-1">{{ $stats['unread'] }}</h4>
                                <p class="mb-0 text-muted small">Unread</p>
                            </div>
                        </div>

                        <div class="col-6 mb-3">
                            <div class="stats-card text-center">
                                <div class="stats-icon read mx-auto">
                                    <i class="fas fa-envelope-open"></i>
                                </div>
                                <h4 class="mb-1">{{ $stats['read'] }}</h4>
                                <p class="mb-0 text-muted small">Read</p>
                            </div>
                        </div>

                        <div class="col-6 mb-3">
                            <div class="stats-card text-center">
                                <div class="stats-icon today mx-auto">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                                <h4 class="mb-1">{{ $stats['today'] }}</h4>
                                <p class="mb-0 text-muted small">Today</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mt-4">
                        <h6 class="mb-3"><i class="fas fa-bolt me-2"></i> Quick Actions</h6>
                        <div class="d-grid gap-2">
                            @if ($stats['unread'] > 0)
                                <a href="{{ route('notifications.index', ['mark_all_read' => true]) }}"
                                    class="btn btn-success btn-sm">
                                    <i class="fas fa-check-double me-1"></i> Mark All as Read
                                </a>
                            @endif

                            <button type="button" class="btn btn-warning btn-sm" onclick="refreshNotifications()">
                                <i class="fas fa-sync-alt me-1"></i> Refresh
                            </button>

                            @if ($stats['total'] > 0)
                                <button type="button" class="btn btn-danger btn-sm" onclick="clearAllNotifications()">
                                    <i class="fas fa-trash-alt me-1"></i> Clear All
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="mt-4">
                        <h6 class="mb-3"><i class="fas fa-filter me-2"></i> Filter by Type</h6>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span
                                class="filter-badge badge {{ $filterType === 'all' ? 'bg-primary active' : 'bg-light text-dark' }}"
                                onclick="filterNotifications('all')">
                                All
                            </span>
                            <span
                                class="filter-badge badge {{ $filterType === 'unread' ? 'bg-danger active' : 'bg-light text-dark' }}"
                                onclick="filterNotifications('unread')">
                                Unread
                            </span>
                            <span
                                class="filter-badge badge {{ $filterType === 'read' ? 'bg-success active' : 'bg-light text-dark' }}"
                                onclick="filterNotifications('read')">
                                Read
                            </span>
                            <span
                                class="filter-badge badge {{ $filterType === 'approval' ? 'bg-info active' : 'bg-light text-dark' }}"
                                onclick="filterNotifications('approval')">
                                Approval
                            </span>
                            <span
                                class="filter-badge badge {{ $filterType === 'comment' ? 'bg-warning active' : 'bg-light text-dark' }}"
                                onclick="filterNotifications('comment')">
                                Comment
                            </span>
                        </div>

                        <h6 class="mb-3"><i class="fas fa-calendar me-2"></i> Filter by Date</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <span
                                class="filter-badge badge {{ $dateFilter === '' ? 'bg-secondary active' : 'bg-light text-dark' }}"
                                onclick="filterByDate('')">
                                All Time
                            </span>
                            <span
                                class="filter-badge badge {{ $dateFilter === 'today' ? 'bg-secondary active' : 'bg-light text-dark' }}"
                                onclick="filterByDate('today')">
                                Today
                            </span>
                            <span
                                class="filter-badge badge {{ $dateFilter === 'yesterday' ? 'bg-secondary active' : 'bg-light text-dark' }}"
                                onclick="filterByDate('yesterday')">
                                Yesterday
                            </span>
                            <span
                                class="filter-badge badge {{ $dateFilter === 'week' ? 'bg-secondary active' : 'bg-light text-dark' }}"
                                onclick="filterByDate('week')">
                                This Week
                            </span>
                            <span
                                class="filter-badge badge {{ $dateFilter === 'month' ? 'bg-secondary active' : 'bg-light text-dark' }}"
                                onclick="filterByDate('month')">
                                This Month
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="col-lg-9 col-md-12">
            <div class="notification-container">
                <div class="notification-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0"><i class="fas fa-bell me-2"></i> Notifications</h4>
                            <p class="mb-0 opacity-75">{{ $stats['total'] }} total notifications</p>
                        </div>
                        <div>
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-circle text-{{ $stats['unread'] > 0 ? 'danger' : 'success' }} me-1"></i>
                                {{ $stats['unread'] }} unread
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-3">
                    @if ($notifications->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach ($notifications as $notification)
                                <div class="notification-card list-group-item list-group-item-action {{ $notification->is_read ? 'read' : 'unread' }} p-3"
                                    data-notification-id="{{ $notification->id }}">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
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

                                                    @case('danger')
                                                        <i class="fas fa-times-circle"></i>
                                                    @break

                                                    @case('approval')
                                                        <i class="fas fa-clipboard-check"></i>
                                                    @break

                                                    @case('assignment')
                                                        <i class="fas fa-user-plus"></i>
                                                    @break

                                                    @case('comment')
                                                        <i class="fas fa-comment"></i>
                                                    @break

                                                    @case('vr')
                                                    @case('vr_request')

                                                    @case('vr_approval')
                                                        <i class="fas fa-file-invoice-dollar"></i>
                                                    @break

                                                    @default
                                                        <i class="fas fa-bell"></i>
                                                @endswitch
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">{{ $notification->title }}</h6>
                                                    <p class="mb-1">{{ $notification->message }}</p>

                                                    @if ($notification->ticket)
                                                        <div class="notification-ticket-info">
                                                            <strong>Ticket:</strong>
                                                            #{{ $notification->ticket->ticket_number }}
                                                            <span class="badge bg-secondary ms-2">
                                                                {{ $notification->ticket->category->name ?? 'N/A' }}
                                                            </span>
                                                            <span
                                                                class="badge status-{{ $notification->ticket->status }}">
                                                                {{ str_replace('_', ' ', $notification->ticket->status) }}
                                                            </span>
                                                        </div>
                                                    @endif

                                                    <div class="notification-time">
                                                        <i class="far fa-clock me-1"></i>
                                                        {{ $notification->created_at->format('d M Y, H:i') }}
                                                        ({{ $notification->created_at->diffForHumans() }})
                                                    </div>
                                                </div>

                                                @if (!$notification->is_read)
                                                    <span class="notification-badge badge bg-danger">NEW</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-auto">
                                            <div class="d-flex flex-column gap-2">
                                                @if (!$notification->is_read)
                                                    <a href="{{ route('notifications.mark-as-read', $notification->id) }}"
                                                        class="btn-action-sm btn btn-success" title="Mark as read">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                @endif

                                                @if ($notification->ticket_id)
                                                    <a href="{{ route('tickets.show', $notification->ticket_id) }}"
                                                        class="btn-action-sm btn btn-primary" title="View Ticket">
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                @endif

                                                <button type="button" class="btn-action-sm btn btn-danger"
                                                    onclick="deleteNotification({{ $notification->id }})" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $notifications->withQueryString()->links() }}
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="empty-state">
                            <i class="fas fa-bell-slash"></i>
                            <h4>No notifications found</h4>
                            <p class="mb-4">You're all caught up! No notifications at the moment.</p>
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                <i class="fas fa-home me-2"></i> Go to Dashboard
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-trash-alt me-2 text-danger"></i> Delete Notification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this notification? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Filter notifications by type
        function filterNotifications(type) {
            window.location.href = "{{ route('notifications.index') }}?type=" + type;
        }

        // Filter by date
        function filterByDate(dateFilter) {
            let url = "{{ route('notifications.index') }}?type={{ $filterType }}";
            if (dateFilter) {
                url += "&date_filter=" + dateFilter;
            }
            window.location.href = url;
        }

        // Delete notification
        function deleteNotification(id) {
            $('#deleteForm').attr('action', '{{ url('notifications') }}/' + id);
            $('#deleteModal').modal('show');
        }

        // Clear all notifications
        function clearAllNotifications() {
            Swal.fire({
                title: 'Clear All Notifications?',
                text: "This will delete all your notifications. This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, clear all!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('notifications.clear-all') }}";
                }
            });
        }

        // Refresh notifications
        function refreshNotifications() {
            window.location.reload();
        }

        // Auto-mark as read when clicked
        $(document).ready(function() {
            $('.notification-card').on('click', function() {
                const notificationId = $(this).data('notification-id');
                const $card = $(this);

                // AJAX mark as read
                $.ajax({
                    url: "{{ route('notifications.ajax-mark-read') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: notificationId
                    },
                    success: function(response) {
                        if (response.success) {
                            $card.removeClass('unread').addClass('read');
                            $card.find('.notification-badge').remove();

                            // Update badge count in navbar
                            updateNotificationCount();
                        }
                    }
                });
            });

            // Update notification count in navbar
            function updateNotificationCount() {
                $.ajax({
                    url: "{{ route('notifications.unread-count') }}",
                    type: 'GET',
                    success: function(response) {
                        const $badge = $('#notificationBadge');
                        if (response.count > 0) {
                            if ($badge.length) {
                                $badge.text(response.count);
                            } else {
                                // Create badge if doesn't exist
                                $('.notification-link').append(
                                    '<span id="notificationBadge" class="badge badge-danger badge-sm">' +
                                    response.count + '</span>'
                                );
                            }
                        } else {
                            $badge.remove();
                        }
                    }
                });
            }

            // Update count on page load
            updateNotificationCount();
        });
    </script>
@endpush

@php
    // Helper function for status badge CSS
    function getStatusBadgeClass($status)
    {
        $classes = [
            'open' => 'bg-info',
            'received' => 'bg-primary',
            'pending_om' => 'bg-warning',
            'in_progress' => 'bg-success',
            'pending_vr' => 'bg-warning',
            'completed' => 'bg-success',
            'pending_gm' => 'bg-warning',
            'ready_for_closure' => 'bg-success',
            'closed' => 'bg-secondary',
            'cancelled' => 'bg-danger',
        ];

        return $classes[$status] ?? 'bg-secondary';
    }
@endphp

<style>
    .status-open {
        background-color: #17a2b8 !important;
    }

    .status-received {
        background-color: #007bff !important;
    }

    .status-pending_om {
        background-color: #ffc107 !important;
        color: #212529 !important;
    }

    .status-in_progress {
        background-color: #28a745 !important;
    }

    .status-pending_vr {
        background-color: #fd7e14 !important;
    }

    .status-completed {
        background-color: #20c997 !important;
    }

    .status-pending_gm {
        background-color: #6f42c1 !important;
    }

    .status-ready_for_closure {
        background-color: #20c997 !important;
    }

    .status-closed {
        background-color: #6c757d !important;
    }

    .status-cancelled {
        background-color: #dc3545 !important;
    }
</style>
