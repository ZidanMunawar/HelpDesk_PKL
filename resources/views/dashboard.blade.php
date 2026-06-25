@extends('layouts.main')

@section('title', 'Dashboard | ' . config('app.name'))
@section('page-title', 'Dashboard')

@section('breadcrumb')
    @php
        $breadcrumb = [['title' => 'Dashboard', 'url' => 'javascript:void(0)']];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

@php
    function getStatusDisplay($status)
    {
        $statusMap = [
            'open' => 'Open',
            'received' => 'Received',
            'pending_om' => 'OM Approval',
            'in_progress' => 'In Progress',
            'pending_vr' => 'PR Approval',
            'completed' => 'Completed',
            'pending_gm' => 'GM Approval',
            'ready_for_closure' => 'Ready for Closure',
            'closed' => 'Closed',
            'cancelled' => 'Cancelled',
        ];
        return $statusMap[$status] ?? str_replace('_', ' ', $status);
    }
@endphp

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css">
    <style>
        :root {
            --primary-navy: #1a2b4c;
            --primary-orange: #ff6b35;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 18px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border-left: 4px solid;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .stat-card.primary {
            border-left-color: var(--primary-navy);
        }

        .stat-card.success {
            border-left-color: #2ecc71;
        }

        .stat-card.warning {
            border-left-color: #f39c12;
        }

        .stat-card.danger {
            border-left-color: #e74c3c;
        }

        .stat-card.info {
            border-left-color: #3498db;
        }

        .stat-card.orange {
            border-left-color: var(--primary-orange);
        }

        .stat-card.purple {
            border-left-color: #9b59b6;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .stat-icon.primary {
            background: rgba(26, 43, 76, 0.1);
            color: var(--primary-navy);
        }

        .stat-icon.success {
            background: rgba(46, 204, 113, 0.1);
            color: #2ecc71;
        }

        .stat-icon.warning {
            background: rgba(243, 156, 18, 0.1);
            color: #f39c12;
        }

        .stat-icon.danger {
            background: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
        }

        .stat-icon.info {
            background: rgba(52, 152, 219, 0.1);
            color: #3498db;
        }

        .stat-icon.orange {
            background: rgba(255, 107, 53, 0.1);
            color: var(--primary-orange);
        }

        .stat-content h3 {
            font-size: 26px;
            font-weight: 700;
            margin: 0;
            color: #333;
        }

        .stat-content p {
            margin: 4px 0 0;
            color: #666;
            font-size: 12px;
            font-weight: 500;
        }

        /* Welcome Card */
        .welcome-card {
            background: linear-gradient(135deg, var(--primary-navy), #2a3b5c);
            border-radius: 20px;
            padding: 25px 30px;
            margin-bottom: 30px;
            color: white;
        }

        .welcome-card h2 {
            font-size: 24px;
            font-weight: 600;
            margin: 0 0 5px;
            color: white !important;
        }

        .welcome-card p {
            opacity: 0.8;
            margin: 0;
            color: rgba(255, 255, 255, 0.9);
        }

        .role-badge {
            background: var(--primary-orange);
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        /* Section Title */
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary-navy);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: var(--primary-orange);
        }

        /* Chart Container */
        .chart-container {
            background: white;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            height: 350px;
        }

        /* Request Table */
        .request-table {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .request-table .table {
            margin: 0;
        }

        .request-table th {
            background: #f8f9fa;
            padding: 14px 18px;
            font-size: 13px;
            font-weight: 600;
            color: #666;
            border: none;
        }

        .request-table td {
            padding: 12px 18px;
            font-size: 13px;
            vertical-align: middle;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-open {
            background: #17a2b8;
            color: white;
        }

        .status-received {
            background: #007bff;
            color: white;
        }

        .status-pending_om {
            background: #ffc107;
            color: #212529;
        }

        .status-in_progress {
            background: #28a745;
            color: white;
        }

        .status-pending_vr {
            background: #ffc107;
            color: #212529;
        }

        .status-completed {
            background: #20c997;
            color: white;
        }

        .status-pending_gm {
            background: #ffc107;
            color: #212529;
        }

        .status-ready_for_closure {
            background: #20c997;
            color: white;
        }

        .status-closed {
            background: #6c757d;
            color: white;
        }

        .status-cancelled {
            background: #dc3545;
            color: white;
        }

        /* Priority Badge */
        .priority-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .stat-card {
                padding: 12px;
            }

            .stat-icon {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }

            .stat-content h3 {
                font-size: 20px;
            }

            .welcome-card {
                padding: 20px;
            }

            .welcome-card h2 {
                font-size: 18px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="dashboard-container">
        <!-- Welcome Card -->
        <div class="welcome-card">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2>Welcome back, {{ $user->name }}! 👋</h2>
                    <p>Here's what's happening with your maintenance requests today.</p>
                </div>
                <div>
                    <span class="role-badge">
                        <i class="fas fa-user-shield me-1"></i> {{ ucfirst(str_replace('_', ' ', $role)) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SUPERADMIN DASHBOARD -->
        <!-- ========================================== -->
        @if ($role === 'superadmin')
            <div class="stats-grid">
                <div class="stat-card primary">
                    <div class="stat-icon primary"><i class="fas fa-users"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format($totalUsers ?? 0) }}</h3>
                        <p>Total Users</p>
                    </div>
                </div>
                <div class="stat-card info">
                    <div class="stat-icon info"><i class="fas fa-clipboard-list"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format($ticketStats['total'] ?? 0) }}</h3>
                        <p>Total MR</p>
                    </div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-icon warning"><i class="fas fa-clock"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format(($ticketStats['open'] ?? 0) + ($ticketStats['in_progress'] ?? 0)) }}</h3>
                        <p>Active MR</p>
                    </div>
                </div>
                <div class="stat-card success">
                    <div class="stat-icon success"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format($ticketStats['closed'] ?? 0) }}</h3>
                        <p>Closed MR</p>
                    </div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-icon orange"><i class="fas fa-building"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format($totalDepartments ?? 0) }}</h3>
                        <p>Departments</p>
                    </div>
                </div>
                <div class="stat-card purple">
                    <div class="stat-icon" style="background: rgba(155, 89, 182, 0.1); color: #9b59b6;"><i
                            class="fas fa-file-invoice-dollar"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format($voucherStats['pending'] ?? 0) }}</h3>
                        <p>Pending PR</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="chart-container">
                        <canvas id="ticketsChart"></canvas>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="chart-container">
                        <canvas id="usersChart"></canvas>
                    </div>
                </div>
            </div>

            @push('scripts')
                <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
                <script>
                    const ticketsCtx = document.getElementById('ticketsChart').getContext('2d');
                    new Chart(ticketsCtx, {
                        type: 'line',
                        data: {
                            labels: @json(array_column($ticketsPerMonth ?? [], 'month')),
                            datasets: [{
                                label: 'MR Created',
                                data: @json(array_column($ticketsPerMonth ?? [], 'count')),
                                borderColor: '#ff6b35',
                                backgroundColor: 'rgba(255, 107, 53, 0.1)',
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: '#ff6b35',
                                pointBorderColor: '#fff',
                                pointRadius: 5
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });

                    const usersCtx = document.getElementById('usersChart').getContext('2d');
                    new Chart(usersCtx, {
                        type: 'doughnut',
                        data: {
                            labels: @json(array_map('ucfirst', $roleLabels ?? [])),
                            datasets: [{
                                data: @json($roleData ?? []),
                                backgroundColor: ['#1a2b4c', '#ff6b35', '#3498db', '#2ecc71', '#f39c12', '#9b59b6',
                                    '#e74c3c'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                </script>
            @endpush
        @endif

        <!-- ========================================== -->
        <!-- ADMIN ENGINEERING DASHBOARD -->
        <!-- ========================================== -->
        @if ($role === 'admin_eng')
            <div class="stats-grid">
                <div class="stat-card primary">
                    <div class="stat-icon primary"><i class="fas fa-clipboard-list"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format($ticketStats['total'] ?? 0) }}</h3>
                        <p>Total MR</p>
                    </div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-icon warning"><i class="fas fa-hourglass-half"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format(($ticketStats['open'] ?? 0) + ($ticketStats['received'] ?? 0)) }}</h3>
                        <p>Pending Receive</p>
                    </div>
                </div>
                <div class="stat-card info">
                    <div class="stat-icon info"><i class="fas fa-cogs"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format($ticketStats['in_progress'] ?? 0) }}</h3>
                        <p>In Progress</p>
                    </div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-icon orange"><i class="fas fa-file-invoice-dollar"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format($ticketStats['pending_vr'] ?? 0) }}</h3>
                        <p>Pending PR</p>
                    </div>
                </div>
                <div class="stat-card success">
                    <div class="stat-icon success"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format($ticketStats['closed'] ?? 0) }}</h3>
                        <p>Closed MR</p>
                    </div>
                </div>
                <div class="stat-card purple">
                    <div class="stat-icon" style="background: rgba(155, 89, 182, 0.1); color: #9b59b6;"><i
                            class="fas fa-user-cog"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format($totalTechnicians ?? 0) }}</h3>
                        <p>Technicians</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="chart-container" style="height: 320px;">
                        <canvas id="priorityChart"></canvas>
                    </div>
                </div>
            </div>

            @push('scripts')
                <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
                <script>
                    const priorityCtx = document.getElementById('priorityChart').getContext('2d');
                    const priorityData = @json($ticketsByPriority ?? []);
                    const priorityLabels = priorityData.map(item => item.priority?.name || 'Unknown');
                    const priorityCounts = priorityData.map(item => item.total);

                    new Chart(priorityCtx, {
                        type: 'bar',
                        data: {
                            labels: priorityLabels,
                            datasets: [{
                                label: 'Number of MR',
                                data: priorityCounts,
                                backgroundColor: ['#1a2b4c', '#ff6b35', '#3498db', '#2ecc71', '#f39c12'],
                                borderRadius: 8
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                </script>
            @endpush

            @if (isset($recentTickets) && count($recentTickets) > 0)
                <div class="section-title mt-4">
                    <i class="fas fa-history"></i> Recent MR
                </div>
                <div class="request-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>MR #</th>
                                <th>Title</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentTickets as $ticket)
                                <tr>
                                    <td><strong>#{{ $ticket->ticket_number }}</strong></td>
                                    <td>{{ Str::limit($ticket->title, 35) }}</td>
                                    <td><span class="priority-badge"
                                            style="background-color: {{ $ticket->priority->color ?? '#6c757d' }}">{{ $ticket->priority->name ?? 'N/A' }}</span>
                                    </td>
                                    <td><span
                                            class="status-badge status-{{ $ticket->status }}">{{ getStatusDisplay($ticket->status) }}</span>
                                    </td>
                                    <td><a href="{{ route('tickets.show', $ticket) }}"
                                            class="btn btn-sm btn-outline-primary">View</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endif

        <!-- ========================================== -->
        <!-- OM DASHBOARD -->
        <!-- ========================================== -->
        @if ($role === 'om')
            <div class="stats-grid">
                <div class="stat-card warning">
                    <div class="stat-icon warning"><i class="fas fa-clock"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format($pendingOMApproval ?? 0) }}</h3>
                        <p>Pending OM Approval</p>
                    </div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-icon orange"><i class="fas fa-file-invoice-dollar"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format($pendingPRApproval ?? 0) }}</h3>
                        <p>Pending PR</p>
                    </div>
                </div>
                <div class="stat-card info">
                    <div class="stat-icon info"><i class="fas fa-calendar-alt"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format($ticketsThisMonth ?? 0) }}</h3>
                        <p>MR This Month</p>
                    </div>
                </div>
                <div class="stat-card success">
                    <div class="stat-icon success"><i class="fas fa-check-double"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format($completedThisMonth ?? 0) }}</h3>
                        <p>Completed MR</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- ========================================== -->
        <!-- GM DASHBOARD -->
        <!-- ========================================== -->
        @if ($role === 'gm')
            <div class="stats-grid">
                <div class="stat-card warning">
                    <div class="stat-icon warning"><i class="fas fa-clock"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format($pendingGMApproval ?? 0) }}</h3>
                        <p>Pending GM Approval</p>
                    </div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-icon orange"><i class="fas fa-file-invoice-dollar"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format($pendingPRApproval ?? 0) }}</h3>
                        <p>Pending PR</p>
                    </div>
                </div>
                <div class="stat-card info">
                    <div class="stat-icon info"><i class="fas fa-calendar-alt"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format($ticketsThisMonth ?? 0) }}</h3>
                        <p>MR This Month</p>
                    </div>
                </div>
                <div class="stat-card success">
                    <div class="stat-icon success"><i class="fas fa-money-bill-wave"></i></div>
                    <div class="stat-content">
                        <h3>{{ $vrTotalAmount ?? 'Rp 0' }}</h3>
                        <p>PR Total (Paid)</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- ========================================== -->
        <!-- MANAGER DASHBOARD -->
        <!-- ========================================== -->
        @if ($role === 'manager')
            <div class="stats-grid">
                <div class="stat-card primary">
                    <div class="stat-icon primary"><i class="fas fa-building"></i></div>
                    <div class="stat-content">
                        <h3>{{ $departmentName ?? 'Engineering' }}</h3>
                        <p>Department</p>
                    </div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-icon warning"><i class="fas fa-clock"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format($ticketStats['open'] ?? 0) }}</h3>
                        <p>Open MR</p>
                    </div>
                </div>
                <div class="stat-card info">
                    <div class="stat-icon info"><i class="fas fa-cogs"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format($ticketStats['in_progress'] ?? 0) }}</h3>
                        <p>In Progress</p>
                    </div>
                </div>
                <div class="stat-card success">
                    <div class="stat-icon success"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format($ticketStats['closed'] ?? 0) }}</h3>
                        <p>Closed MR</p>
                    </div>
                </div>
                <div class="stat-card purple">
                    <div class="stat-icon" style="background: rgba(155, 89, 182, 0.1); color: #9b59b6;"><i
                            class="fas fa-user-cog"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format($totalTechnicians ?? 0) }}</h3>
                        <p>Technicians</p>
                    </div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-icon orange"><i class="fas fa-hourglass-half"></i></div>
                    <div class="stat-content">
                        <h3>{{ $avgCompletionTime ?? 'N/A' }}</h3>
                        <p>Avg Completion</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- ========================================== -->
        <!-- TECHNICIAN DASHBOARD -->
        <!-- ========================================== -->
        @if ($role === 'technician')
            <div class="stats-grid">
                <div class="stat-card warning">
                    <div class="stat-icon warning"><i class="fas fa-tasks"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format($ticketStats['assigned'] ?? 0) }}</h3>
                        <p>Assigned to Me</p>
                    </div>
                </div>
                <div class="stat-card info">
                    <div class="stat-icon info"><i class="fas fa-spinner"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format($ticketStats['in_progress'] ?? 0) }}</h3>
                        <p>In Progress</p>
                    </div>
                </div>
                <div class="stat-card success">
                    <div class="stat-icon success"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format($ticketStats['completed'] ?? 0) }}</h3>
                        <p>Completed</p>
                    </div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-icon orange"><i class="fas fa-file-invoice-dollar"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format($pendingPR ?? 0) }}</h3>
                        <p>Pending PR</p>
                    </div>
                </div>
            </div>

            @if (isset($recentTickets) && count($recentTickets) > 0)
                <div class="section-title">
                    <i class="fas fa-clipboard-list"></i> Recent MR
                </div>
                <div class="request-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>MR #</th>
                                <th>Title</th>
                                <th>Priority</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentTickets as $ticket)
                                <tr>
                                    <td><strong>#{{ $ticket->ticket_number }}</strong></td>
                                    <td>{{ Str::limit($ticket->title, 35) }}</td>
                                    <td><span class="priority-badge"
                                            style="background-color: {{ $ticket->priority->color ?? '#6c757d' }}">{{ $ticket->priority->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ $ticket->due_date ? $ticket->due_date->format('d M Y') : '-' }}</td>
                                    <td><span
                                            class="status-badge status-{{ $ticket->status }}">{{ getStatusDisplay($ticket->status) }}</span>
                                    </td>
                                    <td><a href="{{ route('tickets.show', $ticket) }}"
                                            class="btn btn-sm btn-outline-primary">View</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endif

        <!-- ========================================== -->
        <!-- USER DASHBOARD -->
        <!-- ========================================== -->
        @if ($role === 'user')
            <div class="stats-grid">
                <div class="stat-card primary">
                    <div class="stat-icon primary"><i class="fas fa-clipboard-list"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format($ticketStats['total'] ?? 0) }}</h3>
                        <p>My MR</p>
                    </div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-icon warning"><i class="fas fa-clock"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format($ticketStats['open'] ?? 0) }}</h3>
                        <p>Open</p>
                    </div>
                </div>
                <div class="stat-card info">
                    <div class="stat-icon info"><i class="fas fa-cogs"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format($ticketStats['in_progress'] ?? 0) }}</h3>
                        <p>In Progress</p>
                    </div>
                </div>
                <div class="stat-card success">
                    <div class="stat-icon success"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-content">
                        <h3>{{ number_format($ticketStats['completed'] ?? 0) }}</h3>
                        <p>Resolved</p>
                    </div>
                </div>
            </div>

            @if (isset($recentTickets) && count($recentTickets) > 0)
                <div class="section-title">
                    <i class="fas fa-history"></i> My Recent MR
                </div>
                <div class="request-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>MR #</th>
                                <th>Title</th>
                                <th>Priority</th>
                                <th>Created</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentTickets as $ticket)
                                <tr>
                                    <td><strong>#{{ $ticket->ticket_number }}</strong></td>
                                    <td>{{ Str::limit($ticket->title, 35) }}</td>
                                    <td><span class="priority-badge"
                                            style="background-color: {{ $ticket->priority->color ?? '#6c757d' }}">{{ $ticket->priority->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ $ticket->created_at->format('d M Y') }}</td>
                                    <td><span
                                            class="status-badge status-{{ $ticket->status }}">{{ getStatusDisplay($ticket->status) }}</span>
                                    </td>
                                    <td><a href="{{ route('tickets.show', $ticket) }}"
                                            class="btn btn-sm btn-outline-primary">View</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endif
    </div>
@endsection
