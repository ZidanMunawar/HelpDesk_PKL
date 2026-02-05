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
    <style>
        :root {
            --primary-color: #ff6200;
            --secondary-color: #ff7b00;
        }

        .activity-log-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .log-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 10px 10px 0 0;
            padding: 20px;
        }

        .log-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            position: relative;
        }

        .log-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .log-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            border-radius: 8px 0 0 8px;
        }

        .log-card.created {
            border-left-color: #28a745;
        }

        .log-card.received {
            border-left-color: #17a2b8;
        }

        .log-card.approved {
            border-left-color: #20c997;
        }

        .log-card.rejected {
            border-left-color: #dc3545;
        }

        .log-card.completed {
            border-left-color: #28a745;
        }

        .log-card.commented {
            border-left-color: #ffc107;
        }

        .log-card.assigned {
            border-left-color: #007bff;
        }

        .log-card.cancelled {
            border-left-color: #6c757d;
        }

        .log-card.closed {
            border-left-color: #343a40;
        }

        .log-card.vr {
            border-left-color: #6f42c1;
        }

        .log-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .log-icon.created {
            background: #d4edda;
            color: #155724;
        }

        .log-icon.received {
            background: #d1ecf1;
            color: #0c5460;
        }

        .log-icon.approved {
            background: #d4edda;
            color: #155724;
        }

        .log-icon.rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .log-icon.completed {
            background: #d4edda;
            color: #155724;
        }

        .log-icon.commented {
            background: #fff3cd;
            color: #856404;
        }

        .log-icon.assigned {
            background: #cce5ff;
            color: #004085;
        }

        .log-icon.cancelled {
            background: #e2e3e5;
            color: #383d41;
        }

        .log-icon.closed {
            background: #d6d8d9;
            color: #1b1e21;
        }

        .log-icon.vr {
            background: #e2d9f3;
            color: #4a2b6d;
        }

        .log-badge {
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: 600;
        }

        .log-time {
            font-size: 12px;
            color: #6c757d;
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

        .stats-icon.today {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            color: white;
        }

        .stats-icon.week {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            color: white;
        }

        .stats-icon.month {
            background: linear-gradient(135deg, #fa709a, #fee140);
            color: white;
        }

        .filter-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            border: 1px solid #e0e0e0;
            margin-bottom: 20px;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            color: white;
        }

        .action-badge {
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 12px;
            font-weight: 600;
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

        .btn-action-sm {
            padding: 5px 12px;
            font-size: 12px;
            border-radius: 6px;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .log-header {
                padding: 15px;
            }

            .log-card {
                margin-bottom: 10px;
            }

            .log-icon {
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
                'received' => 'check-circle',
                'assigned' => 'user-plus',
                'om_approved' => 'thumbs-up',
                'om_rejected' => 'thumbs-down',
                'completed' => 'check-double',
                'vr_requested' => 'file-invoice-dollar',
                'accepted' => 'clipboard-check',
                'rejected' => 'times-circle',
                'gm_approved' => 'gavel',
                'gm_rejected' => 'ban',
                'commented' => 'comment',
                'cancelled' => 'ban',
                'admin_closed' => 'lock',
                'login' => 'sign-in-alt',
                'logout' => 'sign-out-alt',
                'admin_eng_approved_quick' => 'bolt',
                'om_approved_quick' => 'bolt',
                'gm_approved_quick' => 'bolt',
                'vr_created' => 'file-invoice',
                'vr_approved' => 'check-circle',
                'vr_rejected' => 'times-circle',
                'vr_paid' => 'money-check',
            ];

            return $icons[$action] ?? 'info-circle';
        }

        // Helper function untuk badge color
        function getActionBadgeColor($action)
        {
            $colors = [
                'created' => 'success',
                'received' => 'info',
                'assigned' => 'primary',
                'om_approved' => 'success',
                'om_rejected' => 'danger',
                'completed' => 'success',
                'vr_requested' => 'warning',
                'accepted' => 'success',
                'rejected' => 'danger',
                'gm_approved' => 'success',
                'gm_rejected' => 'danger',
                'commented' => 'warning',
                'cancelled' => 'secondary',
                'admin_closed' => 'dark',
                'login' => 'info',
                'logout' => 'secondary',
                'admin_eng_approved_quick' => 'success',
                'om_approved_quick' => 'success',
                'gm_approved_quick' => 'success',
                'vr_created' => 'info',
                'vr_approved' => 'success',
                'vr_rejected' => 'danger',
                'vr_paid' => 'success',
            ];

            return $colors[$action] ?? 'secondary';
        }
    @endphp

    <div class="row">
        <!-- Statistics -->
        <div class="col-lg-3 col-md-12 mb-4">
            <div class="activity-log-container">
                <div class="p-3">
                    <h5 class="mb-3"><i class="fas fa-chart-bar me-2"></i> Statistics</h5>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="stats-card text-center">
                                <div class="stats-icon total mx-auto">
                                    <i class="fas fa-history"></i>
                                </div>
                                <h4 class="mb-1">{{ number_format($stats['total']) }}</h4>
                                <p class="mb-0 text-muted small">Total Logs</p>
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

                        <div class="col-6 mb-3">
                            <div class="stats-card text-center">
                                <div class="stats-icon week mx-auto">
                                    <i class="fas fa-calendar-week"></i>
                                </div>
                                <h4 class="mb-1">{{ $stats['last_7_days'] }}</h4>
                                <p class="mb-0 text-muted small">Last 7 Days</p>
                            </div>
                        </div>

                        <div class="col-6 mb-3">
                            <div class="stats-card text-center">
                                <div class="stats-icon month mx-auto">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <h4 class="mb-1">{{ $stats['last_30_days'] }}</h4>
                                <p class="mb-0 text-muted small">Last 30 Days</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mt-4">
                        <h6 class="mb-3"><i class="fas fa-bolt me-2"></i> Quick Actions</h6>
                        <div class="d-grid gap-2">
                            @if (auth()->user()->role === 'superadmin')
                                <button type="button" class="btn btn-danger btn-sm" onclick="clearOldLogs()">
                                    <i class="fas fa-trash-alt me-1"></i> Clear Old Logs (90+ days)
                                </button>
                            @endif

                            <a href="{{ route('activity-logs.export', request()->query()) }}"
                                class="btn btn-success btn-sm">
                                <i class="fas fa-file-export me-1"></i> Export to CSV
                            </a>

                            <button type="button" class="btn btn-warning btn-sm" onclick="refreshPage()">
                                <i class="fas fa-sync-alt me-1"></i> Refresh
                            </button>
                        </div>
                    </div>

                    <!-- Top Users -->
                    <div class="mt-4">
                        <h6 class="mb-3"><i class="fas fa-users me-2"></i> Top Active Users</h6>
                        <div class="list-group list-group-flush">
                            @foreach ($activityByUser as $activity)
                                @if ($activity->user)
                                    <div class="list-group-item list-group-item-action d-flex align-items-center p-2">
                                        <div class="user-avatar me-2"
                                            style="background-color: {{ stringToColor($activity->user->name) }}">
                                            {{ substr($activity->user->name, 0, 1) }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between">
                                                <strong class="small">{{ $activity->user->name }}</strong>
                                                <span class="badge bg-primary">{{ $activity->count }}</span>
                                            </div>
                                            <small class="text-muted">{{ ucfirst($activity->user->role) }}</small>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Activity by Action -->
                    <div class="mt-4">
                        <h6 class="mb-3"><i class="fas fa-chart-pie me-2"></i> Activity by Action</h6>
                        <div class="list-group list-group-flush">
                            @foreach ($activityByAction as $activity)
                                <div
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-2">
                                    <span class="small">
                                        <i class="fas fa-{{ getActionIcon($activity->action) }} me-1"></i>
                                        {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                                    </span>
                                    <span class="badge bg-secondary">{{ $activity->count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9 col-md-12">
            <div class="activity-log-container">
                <div class="log-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0"><i class="fas fa-history me-2"></i> Activity Logs</h4>
                            <p class="mb-0 opacity-75">System activity tracking and audit trail</p>
                        </div>
                        <div>
                            <span class="badge bg-light text-dark">
                                {{ $logs->total() }} total entries
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Filter Card -->
                <div class="filter-card">
                    <form method="GET" action="{{ route('activity-logs.index') }}" id="filterForm">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label small">User</label>
                                <select name="user_id" class="form-select form-select-sm">
                                    <option value="">All Users</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ ucfirst($user->role) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label small">Ticket</label>
                                <select name="ticket_id" class="form-select form-select-sm">
                                    <option value="">All Tickets</option>
                                    @foreach ($tickets as $ticket)
                                        <option value="{{ $ticket->id }}"
                                            {{ request('ticket_id') == $ticket->id ? 'selected' : '' }}>
                                            #{{ $ticket->ticket_number }} - {{ Str::limit($ticket->title, 30) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label class="form-label small">Action</label>
                                <select name="action" class="form-select form-select-sm">
                                    <option value="">All Actions</option>
                                    @foreach ($actions as $action)
                                        <option value="{{ $action }}"
                                            {{ request('action') == $action ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $action)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label class="form-label small">Date From</label>
                                <input type="date" name="date_from" class="form-control form-control-sm"
                                    value="{{ request('date_from') }}">
                            </div>

                            <div class="col-md-2 mb-3">
                                <label class="form-label small">Date To</label>
                                <input type="date" name="date_to" class="form-control form-control-sm"
                                    value="{{ request('date_to') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label small">Search</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search description, user, ticket..."
                                        value="{{ request('search') }}">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6 d-flex align-items-end">
                                <div class="btn-group w-100">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-filter me-1"></i> Apply Filters
                                    </button>
                                    <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-redo me-1"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Logs List -->
                <div class="p-3">
                    @if ($logs->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach ($logs as $log)
                                <div class="log-card list-group-item list-group-item-action p-3 {{ $log->action }}">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <div class="log-icon {{ $log->action }}">
                                                <i class="fas fa-{{ getActionIcon($log->action) }}"></i>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <div>
                                                    <span
                                                        class="badge action-badge bg-{{ getActionBadgeColor($log->action) }}">
                                                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                                    </span>

                                                    @if ($log->user)
                                                        <span class="ms-2">
                                                            <strong>{{ $log->user->name }}</strong>
                                                            <small
                                                                class="text-muted">({{ ucfirst($log->user->role) }})</small>
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="log-time text-end">
                                                    <i class="far fa-clock me-1"></i>
                                                    {{ $log->created_at->format('d M Y, H:i') }}
                                                    <br>
                                                    <small>({{ $log->created_at->diffForHumans() }})</small>
                                                </div>
                                            </div>

                                            <p class="mb-1">{{ $log->description }}</p>

                                            @if ($log->ticket)
                                                <div class="mt-2 p-2 bg-light rounded">
                                                    <strong>Ticket:</strong>
                                                    <a href="{{ route('tickets.show', $log->ticket_id) }}"
                                                        class="text-primary ms-2">
                                                        #{{ $log->ticket->ticket_number }}
                                                    </a>
                                                    <span class="ms-2">{{ $log->ticket->title }}</span>
                                                    <span class="badge bg-secondary ms-2">
                                                        {{ $log->ticket->category->name ?? 'N/A' }}
                                                    </span>
                                                    <span class="badge bg-info ms-2">
                                                        {{ ucfirst($log->ticket->priority->name ?? 'N/A') }}
                                                    </span>
                                                </div>
                                            @endif

                                            @if ($log->ip_address || $log->user_agent)
                                                <div class="mt-2 small text-muted">
                                                    @if ($log->ip_address)
                                                        <span class="me-3">
                                                            <i class="fas fa-globe me-1"></i> IP: {{ $log->ip_address }}
                                                        </span>
                                                    @endif
                                                    @if ($log->user_agent)
                                                        <span>
                                                            <i class="fas fa-desktop me-1"></i>
                                                            {{ Str::limit($log->user_agent, 50) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif

                                            @if ($log->old_values || $log->new_values)
                                                <div class="mt-2">
                                                    <button class="btn btn-sm btn-outline-info" type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#changes-{{ $log->id }}">
                                                        <i class="fas fa-exchange-alt me-1"></i> View Changes
                                                    </button>

                                                    <div class="collapse mt-2" id="changes-{{ $log->id }}">
                                                        <div class="card card-body">
                                                            <div class="row">
                                                                @if ($log->old_values)
                                                                    <div class="col-md-6">
                                                                        <h6 class="text-danger">Old Values</h6>
                                                                        <pre class="small mb-0">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                                                    </div>
                                                                @endif
                                                                @if ($log->new_values)
                                                                    <div class="col-md-6">
                                                                        <h6 class="text-success">New Values</h6>
                                                                        <pre class="small mb-0">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="col-auto">
                                            <div class="d-flex flex-column gap-2">
                                                <a href="javascript:void(0)" class="btn-action-sm btn btn-info"
                                                    title="View Details" onclick="viewLogDetails({{ $log->id }})">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                @if (auth()->user()->role === 'superadmin')
                                                    <button type="button" class="btn-action-sm btn btn-danger"
                                                        onclick="deleteLog({{ $log->id }})" title="Delete">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $logs->withQueryString()->links() }}
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="empty-state">
                            <i class="fas fa-search"></i>
                            <h4>No activity logs found</h4>
                            <p class="mb-4">No activity logs match your search criteria.</p>
                            <a href="{{ route('activity-logs.index') }}" class="btn btn-primary">
                                <i class="fas fa-redo me-2"></i> Reset Filters
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Log Details Modal -->
    <div class="modal fade" id="logDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i> Log Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="logDetailsContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    @if (auth()->user()->role === 'superadmin')
        <div class="modal fade" id="deleteLogModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-trash-alt me-2 text-danger"></i> Delete Log</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this activity log? This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <form id="deleteLogForm" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        // View log details
        function viewLogDetails(logId) {
            $.ajax({
                url: "{{ url('activity-logs') }}/" + logId,
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

        // Delete log (superadmin only)
        function deleteLog(logId) {
            $('#deleteLogForm').attr('action', "{{ url('activity-logs') }}/" + logId);
            $('#deleteLogModal').modal('show');
        }

        // Clear old logs
        function clearOldLogs() {
            Swal.fire({
                title: 'Clear Old Logs?',
                text: "This will delete all activity logs older than 90 days. This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, clear them!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('activity-logs.clear-old') }}";
                }
            });
        }

        // Refresh page
        function refreshPage() {
            window.location.reload();
        }

        // Auto-refresh page every 5 minutes
        $(document).ready(function() {
            setTimeout(function() {
                if (document.hasFocus()) {
                    // refreshPage();
                }
            }, 300000); // 5 minutes

            // Apply filters on select change
            $('select[name="user_id"], select[name="ticket_id"], select[name="action"]').on('change', function() {
                $('#filterForm').submit();
            });
        });
    </script>
@endpush
