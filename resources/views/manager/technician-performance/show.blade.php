@extends('layouts.main')

@section('title', 'Technician Performance - ' . $technician->name . ' | ' . config('app.name'))

@section('page-title', 'Technician Performance')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Technician Performance', 'url' => route('technician-performance.index')],
            ['title' => $technician->name, 'url' => 'javascript:void(0)'],
        ];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        :root {
            --navy: #003366;
            --orange: #ff6600;
            --navy-light: #1e4a7a;
        }

        .profile-header {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 25px;
            color: white;
        }

        .profile-header h2,
        .profile-header p,
        .profile-header a {
            color: white !important;
        }

        .tech-avatar-large {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 600;
            flex-shrink: 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            border-left: 3px solid var(--orange);
        }

        .stat-value {
            font-size: 20px;
            font-weight: 700;
            color: var(--navy);
        }

        .stat-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }

        .chart-card {
            background: white;
            border-radius: 12px;
            padding: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 15px;
        }

        .chart-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--navy);
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 2px solid var(--orange);
        }

        .badge-custom {
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: 600;
            color: white;
        }

        .badge-completed {
            background: #28a745;
        }

        .badge-progress {
            background: #ffc107;
            color: #333;
        }

        .badge-overdue {
            background: #dc3545;
        }

        .badge-cancelled {
            background: #6c757d;
        }

        /* Modal Styling */
        .modal-header-custom {
            background: linear-gradient(135deg, #003366 0%, #1e4a7a 100%);
            color: white !important;
            padding: 12px 16px;
        }

        .modal-header-custom .modal-title {
            color: white !important;
            font-size: 16px;
        }

        .modal-header-custom .btn-close {
            filter: brightness(0) invert(1);
        }

        .modal-filter-bar {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        /* Search Input */
        .search-input-group {
            margin-bottom: 12px;
        }

        .search-input-group input {
            font-size: 12px;
            padding: 6px 10px;
            border-radius: 6px;
        }

        /* Date Filter Row - 3 grid kesamping */
        .date-filter-row {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 8px;
            align-items: end;
        }

        .date-filter-item label {
            font-size: 10px;
            margin-bottom: 3px;
            display: block;
            color: #666;
        }

        .date-filter-item .flatpickr-input {
            font-size: 11px;
            padding: 6px 8px;
            border-radius: 6px;
            border: 1px solid #ddd;
            width: 100%;
        }

        .btn-reset-filter {
            background: #6c757d;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 11px;
            cursor: pointer;
            white-space: nowrap;
        }

        .btn-reset-filter:hover {
            background: #5a6268;
        }

        /* Custom Scrollbar untuk modal */
        .modal-table-wrapper {
            max-height: 55vh;
            overflow: auto;
            position: relative;
        }

        .modal-table-wrapper::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .modal-table-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 8px;
        }

        .modal-table-wrapper::-webkit-scrollbar-thumb {
            background: var(--orange);
            border-radius: 8px;
        }

        .modal-table-wrapper::-webkit-scrollbar-thumb:hover {
            background: var(--navy);
        }

        /* Table Styling - Diperkecil */
        #allTicketsTable {
            font-size: 11px;
            min-width: 700px;
        }

        #allTicketsTable thead th {
            position: sticky;
            top: 0;
            background: var(--navy);
            color: white;
            z-index: 10;
            white-space: nowrap;
            padding: 8px 6px;
            font-size: 11px;
            font-weight: 500;
        }

        #allTicketsTable tbody td {
            padding: 6px;
            white-space: nowrap;
            font-size: 11px;
        }

        #allTicketsTable tbody tr:hover {
            background: #f8f9fa;
        }

        /* Filter Info */
        .filter-info {
            font-size: 10px;
            margin-top: 8px;
            color: #666;
        }

        /* Recent Tickets Table */
        .recent-table {
            font-size: 11px;
        }

        .recent-table th,
        .recent-table td {
            padding: 6px 4px;
            font-size: 10px;
        }

        /* View All Button */
        .btn-view-all {
            background: transparent;
            color: #ff6600;
            border: 1px solid #ff6600;
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 20px;
            transition: all 0.2s;
        }

        .btn-view-all:hover {
            background: #ff6600;
            color: white;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .stats-grid {
                gap: 10px;
            }

            .stat-value {
                font-size: 18px;
            }
        }

        @media (max-width: 768px) {
            .profile-header {
                padding: 15px;
            }

            .tech-avatar-large {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }

            .profile-header h2 {
                font-size: 16px;
            }

            .profile-header p {
                font-size: 10px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }

            .stat-value {
                font-size: 16px;
            }

            .stat-card {
                padding: 10px;
            }

            .stat-label {
                font-size: 9px;
            }

            /* Filter mobile - tetap 3 grid tapi lebih kecil */
            .date-filter-row {
                gap: 6px;
            }

            .date-filter-item .flatpickr-input {
                font-size: 10px;
                padding: 5px 6px;
            }

            .btn-reset-filter {
                padding: 5px 8px;
                font-size: 10px;
            }
        }

        @media (max-width: 576px) {
            .profile-header .d-flex {
                flex-direction: column;
                text-align: center;
            }

            .tech-avatar-large {
                margin-bottom: 10px;
                margin-right: 0 !important;
            }

            .date-filter-row {
                gap: 5px;
            }

            .date-filter-item .flatpickr-input {
                font-size: 9px;
                padding: 4px 5px;
            }

            .btn-reset-filter {
                padding: 4px 6px;
                font-size: 9px;
            }

            .modal-filter-bar {
                padding: 10px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-0">
        <!-- Back Button -->
        <a href="{{ route('technician-performance.index') }}" class="btn btn-sm btn-outline-secondary mb-3"
            style="font-size: 12px;">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>

        <!-- Profile Header -->
        <div class="profile-header">
            <div class="d-flex align-items-center" style="gap: 20px; flex-wrap: nowrap;">
                <div class="tech-avatar-large" style="flex-shrink: 0;">
                    {{ strtoupper(substr($technician->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="mb-1" style="font-size: 20px;">{{ $technician->name }}</h2>
                    <p class="mb-0 opacity-75" style="font-size: 11px;">
                        <i class="fas fa-envelope me-1"></i> {{ $technician->email }} &nbsp;|&nbsp;
                        <i class="fas fa-building me-1"></i> {{ $technician->department->name ?? 'Engineering' }}
                        &nbsp;|&nbsp;
                        <i class="fas fa-calendar me-1"></i> Joined {{ $technician->created_at->format('M Y') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Stats Cards Row 1 -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">{{ $totalTickets }}</div>
                <div class="stat-label">Total MR</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $successCount }}</div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $completionRate }}%</div>
                <div class="stat-label">Completion Rate</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $avgCompletionTime > 0 ? $avgCompletionTime . 'h' : '-' }}</div>
                <div class="stat-label">Avg Time</div>
            </div>
        </div>

        <!-- Stats Cards Row 2 -->
        <div class="stats-grid" style="margin-top: -10px;">
            <div class="stat-card">
                <div class="stat-value">{{ $inProgressCount }}</div>
                <div class="stat-label">In Progress</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-warning">{{ $overdueCount }}</div>
                <div class="stat-label">Overdue</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-danger">{{ $cancelledCount }}</div>
                <div class="stat-label">Cancelled</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $totalTickets > 0 ? round(($inProgressCount / $totalTickets) * 100, 1) : 0 }}%
                </div>
                <div class="stat-label">Active Rate</div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <div class="col-md-7">
                <div class="chart-card">
                    <div class="chart-title">
                        <i class="fas fa-chart-line me-2"></i> Weekly Performance
                    </div>
                    <canvas id="weeklyChart" style="height: 220px; width: 100%;"></canvas>
                </div>
            </div>
            <div class="col-md-5">
                <div class="chart-card">
                    <div class="chart-title">
                        <i class="fas fa-chart-pie me-2"></i> Status Breakdown
                    </div>
                    <canvas id="statusChart" style="height: 180px; width: 100%;"></canvas>
                </div>
            </div>
        </div>

        <!-- Second Row Charts -->
        <div class="row">
            <div class="col-md-6">
                <div class="chart-card">
                    <div class="chart-title">
                        <i class="fas fa-tags me-2"></i> Category Breakdown
                    </div>
                    <canvas id="categoryChart" style="height: 220px; width: 100%;"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-card">
                    <div class="chart-title">
                        <i class="fas fa-chart-bar me-2"></i> Monthly Trend
                    </div>
                    <canvas id="monthlyChart" style="height: 220px; width: 100%;"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Tickets dengan Modal -->
        <div class="chart-card">
            <div class="chart-title d-flex justify-content-between align-items-center flex-wrap" style="gap: 8px;">
                <div>
                    <i class="fas fa-history me-2"></i> Recent Maintenance Requests
                </div>
                <button type="button" class="btn-view-all" data-bs-toggle="modal" data-bs-target="#allTicketsModal">
                    <i class="fas fa-list me-1"></i> View All ({{ $totalTickets }})
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-sm recent-table">
                    <thead>
                        <tr>
                            <th>MR #</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentTickets->take(5) as $ticket)
                            <tr>
                                <td>
                                    <a href="{{ route('tickets.show', $ticket->id) }}"
                                        style="color: var(--navy); font-weight: 600;">
                                        #{{ $ticket->ticket_number }}
                                    </a>
                                </td>
                                <td>{{ Str::limit($ticket->title, 25) }}</td>
                                <td>{{ $ticket->category->name ?? '-' }}</td>
                                <td>
                                    <span style="color: {{ $ticket->priority->color ?? '#666' }}">
                                        ● {{ $ticket->priority->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusClass = match ($ticket->status) {
                                            'completed', 'closed' => 'badge-completed',
                                            'cancelled' => 'badge-cancelled',
                                            default => 'badge-progress',
                                        };
                                        $overdue =
                                            $ticket->due_date &&
                                            $ticket->due_date < now() &&
                                            !in_array($ticket->status, ['closed', 'cancelled', 'completed']);
                                    @endphp
                                    @if ($overdue)
                                        <span class="badge-custom badge-overdue">OVD</span>
                                    @else
                                        <span class="badge-custom {{ $statusClass }}">
                                            {{ substr(strtoupper(str_replace('_', ' ', $ticket->status)), 0, 4) }}
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $ticket->created_at->format('d/m/Y') }}</td>
                                <td>
                                    @if ($ticket->resolved_at)
                                        {{ $ticket->created_at->diffInHours($ticket->resolved_at) }}h
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No maintenance requests found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal All Tickets -->
    <div class="modal fade" id="allTicketsModal" tabindex="-1" aria-labelledby="allTicketsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title" id="allTicketsModalLabel">
                        <i class="fas fa-history me-2"></i> All Requests - {{ $technician->name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding: 12px;">
                    <!-- Filter Bar -->
                    <div class="modal-filter-bar">
                        <!-- Search di bagian atas sendiri -->
                        <div class="search-input-group">
                            <input type="text" id="searchTicketInput" class="form-control"
                                placeholder="🔍 Search by ticket number or title...">
                        </div>

                        <!-- 3 Grid kesamping: Start Date, End Date, Reset -->
                        <div class="date-filter-row">
                            <div class="date-filter-item">
                                <label><i class="fas fa-calendar"></i> Start Date</label>
                                <input type="text" id="startDateFilter" class="flatpickr-input"
                                    placeholder="YYYY-MM-DD">
                            </div>
                            <div class="date-filter-item">
                                <label><i class="fas fa-calendar"></i> End Date</label>
                                <input type="text" id="endDateFilter" class="flatpickr-input"
                                    placeholder="YYYY-MM-DD">
                            </div>
                            <div>
                                <button id="resetTicketFilter" class="btn-reset-filter">
                                    <i class="fas fa-redo"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Table dengan Scroll -->
                    <div class="modal-table-wrapper">
                        <table class="table" id="allTicketsTable">
                            <thead>
                                <tr>
                                    <th>MR #</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Created Date</th>
                                    <th>Completion Time</th>
                                </tr>
                            </thead>
                            <tbody id="ticketTableBody">
                                @foreach ($recentTickets as $ticket)
                                    <tr data-ticket-number="{{ $ticket->ticket_number }}"
                                        data-ticket-title="{{ strtolower($ticket->title) }}"
                                        data-created-date="{{ $ticket->created_at->format('Y-m-d') }}">
                                        <td>
                                            <a href="{{ route('tickets.show', $ticket->id) }}"
                                                style="color: var(--navy); font-weight: 600;">
                                                #{{ $ticket->ticket_number }}
                                            </a>
                                        </td>
                                        <td>{{ Str::limit($ticket->title, 40) }}</td>
                                        <td>{{ $ticket->category->name ?? '-' }}</td>
                                        <td>
                                            <span style="color: {{ $ticket->priority->color ?? '#666' }}">
                                                ● {{ $ticket->priority->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match ($ticket->status) {
                                                    'completed', 'closed' => 'badge-completed',
                                                    'cancelled' => 'badge-cancelled',
                                                    default => 'badge-progress',
                                                };
                                                $overdue =
                                                    $ticket->due_date &&
                                                    $ticket->due_date < now() &&
                                                    !in_array($ticket->status, ['closed', 'cancelled', 'completed']);
                                            @endphp
                                            @if ($overdue)
                                                <span class="badge-custom badge-overdue">OVERDUE</span>
                                            @else
                                                <span class="badge-custom {{ $statusClass }}">
                                                    {{ strtoupper(str_replace('_', ' ', $ticket->status)) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $ticket->created_at->format('d M Y') }}</td>
                                        <td>
                                            @if ($ticket->resolved_at)
                                                {{ $ticket->created_at->diffInHours($ticket->resolved_at) }}h
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="filter-info" id="filterInfo"></div>
                </div>
                <div class="modal-footer" style="padding: 10px;">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"
                        style="font-size: 11px;">Close</button>
                    <a href="{{ route('tickets.index', ['assigned_to' => $technician->id]) }}"
                        class="btn btn-primary btn-sm" style="font-size: 11px;">
                        <i class="fas fa-external-link-alt me-1"></i> Open in Maintenance Request Page
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Weekly Chart
        const weeklyLabels = @json(array_column($weeklyData, 'week'));
        const weeklyCompleted = @json(array_column($weeklyData, 'completed'));
        const weeklyOverdue = @json(array_column($weeklyData, 'overdue'));
        const weeklyProgress = @json(array_column($weeklyData, 'in_progress'));

        new Chart(document.getElementById('weeklyChart'), {
            type: 'bar',
            data: {
                labels: weeklyLabels,
                datasets: [{
                        label: 'Completed',
                        data: weeklyCompleted,
                        backgroundColor: '#28a745',
                        borderRadius: 4,
                        barPercentage: 0.7
                    },
                    {
                        label: 'In Progress',
                        data: weeklyProgress,
                        backgroundColor: '#ffc107',
                        borderRadius: 4,
                        barPercentage: 0.7
                    },
                    {
                        label: 'Overdue',
                        data: weeklyOverdue,
                        backgroundColor: '#dc3545',
                        borderRadius: 4,
                        barPercentage: 0.7
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 10
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: {
                                size: 9
                            }
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 9
                            }
                        }
                    }
                }
            }
        });

        // Status Chart
        const statusLabels = @json(array_keys($statusBreakdown));
        const statusData = @json(array_values($statusBreakdown));
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusData,
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#6c757d']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            font: {
                                size: 9
                            }
                        }
                    }
                }
            }
        });

        // Category Chart
        const catLabels = @json(array_keys($categoryBreakdown));
        const catData = @json(array_values($categoryBreakdown));
        new Chart(document.getElementById('categoryChart'), {
            type: 'bar',
            data: {
                labels: catLabels.slice(0, 6),
                datasets: [{
                    label: 'Tickets',
                    data: catData.slice(0, 6),
                    backgroundColor: '#003366',
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        labels: {
                            font: {
                                size: 9
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            font: {
                                size: 9
                            }
                        }
                    },
                    y: {
                        ticks: {
                            font: {
                                size: 9
                            }
                        }
                    }
                }
            }
        });

        // Monthly Chart
        const monthLabels = @json(array_column($monthlyTrend, 'month'));
        const monthCompleted = @json(array_column($monthlyTrend, 'completed'));
        const monthTotal = @json(array_column($monthlyTrend, 'total'));
        new Chart(document.getElementById('monthlyChart'), {
            type: 'line',
            data: {
                labels: monthLabels,
                datasets: [{
                        label: 'Completed',
                        data: monthCompleted,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40,167,69,0.1)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 2
                    },
                    {
                        label: 'Total Assigned',
                        data: monthTotal,
                        borderColor: '#003366',
                        backgroundColor: 'rgba(0,51,102,0.1)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 9
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            font: {
                                size: 8,
                                rotation: 45
                            }
                        }
                    },
                    y: {
                        ticks: {
                            font: {
                                size: 9
                            }
                        }
                    }
                }
            }
        });

        // ========== MODAL FILTER ==========
        flatpickr("#startDateFilter", {
            dateFormat: "Y-m-d",
            onChange: filterTickets
        });
        flatpickr("#endDateFilter", {
            dateFormat: "Y-m-d",
            onChange: filterTickets
        });

        function filterTickets() {
            const searchTerm = document.getElementById('searchTicketInput').value.toLowerCase();
            const startDate = document.getElementById('startDateFilter').value;
            const endDate = document.getElementById('endDateFilter').value;
            const rows = document.querySelectorAll('#ticketTableBody tr');
            let visibleCount = 0;

            rows.forEach(row => {
                const ticketNumber = row.getAttribute('data-ticket-number').toLowerCase();
                const ticketTitle = row.getAttribute('data-ticket-title');
                const createdDate = row.getAttribute('data-created-date');

                let showBySearch = true,
                    showByDate = true;
                if (searchTerm) showBySearch = ticketNumber.includes(searchTerm) || ticketTitle.includes(
                    searchTerm);
                if (startDate && createdDate < startDate) showByDate = false;
                if (endDate && createdDate > endDate) showByDate = false;

                if (showBySearch && showByDate) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            const totalRows = rows.length;
            const filterInfo = document.getElementById('filterInfo');
            filterInfo.innerHTML = visibleCount === totalRows ?
                `<i class="fas fa-info-circle"></i> Showing all ${totalRows} tickets` :
                `<i class="fas fa-filter"></i> Showing ${visibleCount} of ${totalRows} tickets`;
        }

        document.getElementById('searchTicketInput').addEventListener('keyup', filterTickets);
        document.getElementById('resetTicketFilter').addEventListener('click', () => {
            document.getElementById('searchTicketInput').value = '';
            document.getElementById('startDateFilter').value = '';
            document.getElementById('endDateFilter').value = '';
            filterTickets();
        });

        $('#allTicketsModal').on('hidden.bs.modal', () => {
            document.getElementById('searchTicketInput').value = '';
            document.getElementById('startDateFilter').value = '';
            document.getElementById('endDateFilter').value = '';
            filterTickets();
        });
    </script>
@endpush
