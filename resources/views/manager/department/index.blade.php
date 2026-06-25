@extends('layouts.main')

@section('title', 'My Department | ' . config('app.name'))

@section('page-title', 'My Department')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'My Department', 'url' => 'javascript:void(0)'],
        ];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --navy: #003366;
            --orange: #ff6600;
            --navy-light: #1e4a7a;
            --orange-light: #ff8533;
        }

        .dept-header {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            color: white;
            box-shadow: 0 6px 15px rgba(0, 51, 102, 0.15);
            display: flex;
            align-items: center;
        }

        .dept-icon {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin-right: 20px;
        }

        .dept-info {
            flex: 1;
        }

        .dept-name {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dept-name i {
            font-size: 18px;
            cursor: pointer;
            opacity: 0.8;
            transition: opacity 0.2s;
            background: rgba(255, 255, 255, 0.2);
            padding: 8px;
            border-radius: 50%;
        }

        .dept-name i:hover {
            opacity: 1;
            background: rgba(255, 255, 255, 0.3);
        }

        .dept-meta {
            font-size: 14px;
            opacity: 0.9;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .dept-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-left: 4px solid var(--orange);
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 51, 102, 0.1);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--navy);
            line-height: 1.2;
        }

        .stat-label {
            font-size: 13px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-label i {
            color: var(--orange);
            margin-right: 5px;
            font-size: 12px;
        }

        .info-icon {
            color: #6c757d;
            cursor: pointer;
            transition: color 0.2s;
            font-size: 16px;
            padding: 5px;
        }

        .info-icon:hover {
            color: var(--orange);
        }

        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .card-custom {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--navy);
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--orange);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .user-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-height: 400px;
            overflow-y: auto;
        }

        .user-item {
            display: flex;
            align-items: center;
            padding: 12px;
            border: 1px solid #f0f0f0;
            border-radius: 10px;
            transition: all 0.2s;
            cursor: pointer;
            gap: 12px;
        }

        .user-item:hover {
            border-color: var(--orange);
            background: #fff8f0;
            /* transform: translateX(3px); */
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 18px;
            flex-shrink: 0;
        }

        .user-info {
            flex: 1;
            min-width: 0;
            overflow: hidden;
        }

        .user-name {
            font-weight: 600;
            color: #333;
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            margin-bottom: 4px;
            font-size: 14px;
        }

        .user-role-badge {
            font-size: 9px;
            padding: 2px 6px;
            border-radius: 12px;
            background: #f0f0f0;
            color: #666;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .user-email {
            font-size: 11px;
            color: #888;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-chevron {
            color: #ccc;
            font-size: 12px;
            transition: transform 0.2s;
            flex-shrink: 0;
        }

        .user-item:hover .user-chevron {
            transform: translateX(3px);
            color: var(--orange);
        }

        .chart-container {
            margin-top: 10px;
            min-height: 250px;
        }

        canvas {
            max-height: 250px;
            width: 100% !important;
        }

        .ticket-item {
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .ticket-item:last-child {
            border-bottom: none;
        }

        .ticket-number {
            font-weight: 600;
            color: var(--navy);
            text-decoration: none;
            font-size: 13px;
        }

        .ticket-number:hover {
            color: var(--orange);
        }

        .ticket-title {
            font-size: 13px;
            color: #666;
            margin: 3px 0 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .ticket-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 11px;
            flex-wrap: wrap;
        }

        .badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-weight: 600;
            color: white;
            font-size: 10px;
        }

        .badge-open {
            background: #1976d2;
        }

        .badge-progress {
            background: #388e3c;
        }

        .badge-pending {
            background: #f57c00;
        }

        .badge-completed {
            background: #00796b;
        }

        .badge-closed {
            background: #616161;
        }

        .badge-canceled {
            background: #dc3545;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        /* Modal Profile Styles */
        .modal-profile .avatar-large {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 32px;
            margin: 0 auto;
        }

        .modal-profile .profile-detail-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .modal-profile .profile-detail-row:last-child {
            border-bottom: none;
        }

        .modal-profile .detail-label {
            width: 130px;
            font-size: 13px;
            color: #666;
            font-weight: 500;
            flex-shrink: 0;
        }

        .modal-profile .detail-label i {
            width: 20px;
            color: var(--navy);
            margin-right: 8px;
        }

        .modal-profile .detail-value {
            flex: 1;
            font-size: 13px;
            color: #333;
            font-weight: 500;
            word-break: break-word;
        }

        .status-badge-active {
            display: inline-block;
            padding: 4px 12px;
            background: #d4edda;
            color: #155724;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-badge-inactive {
            display: inline-block;
            padding: 4px 12px;
            background: #f8d7da;
            color: #721c24;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-badge-pending {
            display: inline-block;
            padding: 4px 12px;
            background: #fff3cd;
            color: #856404;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .role-badge-modal {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        /* Stats Modal */
        .stats-modal .stat-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .stats-modal .stat-row:last-child {
            border-bottom: none;
        }

        .stats-modal .stat-label-modal {
            color: #666;
            font-size: 13px;
        }

        .stats-modal .stat-value-modal {
            font-weight: 600;
            color: var(--navy);
            font-size: 14px;
        }

        .stats-modal .stat-total {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid var(--orange);
            font-weight: 700;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .dept-header {
                padding: 20px 15px;
            }

            .dept-name {
                font-size: 18px;
                flex-wrap: wrap;
            }

            .dept-icon {
                width: 50px;
                height: 50px;
                font-size: 24px;
                margin-right: 15px;
            }

            .dept-meta {
                font-size: 12px;
                gap: 10px;
            }

            .stats-grid {
                gap: 10px;
            }

            .stat-value {
                font-size: 24px;
            }

            .stat-card {
                padding: 15px 12px;
            }

            .stat-label {
                font-size: 11px;
            }

            .two-column {
                grid-template-columns: 1fr;
            }

            .user-item {
                padding: 10px;
            }

            .user-avatar {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }

            .user-name {
                font-size: 13px;
            }

            .user-email {
                font-size: 10px;
            }

            .modal-profile .detail-label {
                width: 110px;
                font-size: 12px;
            }

            .modal-profile .detail-value {
                font-size: 12px;
            }
        }

        @media (min-width: 1200px) {
            .chart-container {
                min-height: 280px;
            }

            canvas {
                max-height: 280px;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Department Header -->
    <div class="dept-header">
        <div class="dept-icon">
            <i class="fas fa-building"></i>
        </div>
        <div class="dept-info">
            <div class="dept-name">
                {{ $department->name }}
                <i class="fas fa-pencil-alt" onclick="editDepartmentName('{{ $department->name }}')"
                    title="Edit department name"></i>
            </div>
            <div class="dept-meta">
                <span><i class="fas fa-user-tie"></i> {{ auth()->user()->name }}</span>
                <span><i class="fas fa-users"></i> {{ $departmentUsers->count() }} Members</span>
                <span><i class="fas fa-calendar-alt"></i> Since {{ $department->created_at->format('M Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-label"><i class="fas fa-ticket-alt"></i> TOTAL MR</div>
                <i class="fas fa-info-circle info-icon" data-stat-type="total" onclick="showStatsModal('total')"></i>
            </div>
            <div class="stat-value">{{ $ticketStats['total'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-label"><i class="fas fa-spinner"></i> IN PROGRESS</div>
                <i class="fas fa-info-circle info-icon" data-stat-type="in_progress"
                    onclick="showStatsModal('in_progress')"></i>
            </div>
            <div class="stat-value">{{ $ticketStats['in_progress'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-label"><i class="fas fa-check-circle"></i> COMPLETED</div>
                <i class="fas fa-info-circle info-icon" data-stat-type="completed"
                    onclick="showStatsModal('completed')"></i>
            </div>
            <div class="stat-value">{{ $ticketStats['completed'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-label"><i class="fas fa-ban"></i> CANCELED</div>
                <i class="fas fa-info-circle info-icon" data-stat-type="canceled" onclick="showStatsModal('canceled')"></i>
            </div>
            <div class="stat-value">{{ $ticketStats['canceled'] }}</div>
        </div>
    </div>

    <!-- Two Column Content -->
    <div class="two-column">
        <div>
            <!-- Department Members -->
            @if ($departmentUsers->count() > 0)
                <div class="card-custom">
                    <div class="card-title">
                        <i class="fas fa-users"></i>
                        <span>Department Members ({{ $departmentUsers->count() }})</span>
                    </div>
                    <div class="user-list">
                        @foreach ($departmentUsers as $member)
                            @php
                                $avatarColor = match ($member->role) {
                                    'technician' => '#fdcb6e',
                                    'manager' => '#0984e3',
                                    'admin_eng' => '#00b894',
                                    'superadmin' => '#6c5ce7',
                                    'om' => '#e17055',
                                    'gm' => '#d63031',
                                    default => '#003366',
                                };
                                $avatarTextColor = $member->role === 'technician' ? '#2d3436' : 'white';
                                $roleDisplay = match ($member->role) {
                                    'superadmin' => 'Super Admin',
                                    'admin_eng' => 'Admin Eng',
                                    'manager' => 'Manager',
                                    'technician' => 'Tech',
                                    'user' => 'User',
                                    'om' => 'OM',
                                    'gm' => 'GM',
                                    default => ucfirst($member->role),
                                };
                            @endphp
                            <div class="user-item" data-bs-toggle="modal"
                                data-bs-target="#memberInfoModal{{ $member->id }}">
                                <div class="user-avatar"
                                    style="background: {{ $avatarColor }}; color: {{ $avatarTextColor }}">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <div class="user-info">
                                    <div class="user-name">
                                        {{ $member->name }}
                                        <span class="user-role-badge">{{ $roleDisplay }}</span>
                                    </div>
                                    <div class="user-email">{{ $member->email }}</div>
                                </div>
                                <i class="fas fa-chevron-right user-chevron"></i>
                            </div>

                            <!-- Member Modal -->
                            <div class="modal fade modal-profile" id="memberInfoModal{{ $member->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="fas fa-user-circle me-2" style="color: var(--navy);"></i>
                                                Profile Details
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="text-center mb-4">
                                                <div class="avatar-large"
                                                    style="background: {{ $avatarColor }}; color: {{ $avatarTextColor }}">
                                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                                </div>
                                                <h6 class="mt-3 mb-0 fw-bold">{{ $member->name }}</h6>
                                                <span class="role-badge-modal mt-2"
                                                    style="background: {{ $avatarColor }}; color: {{ $avatarTextColor }}">
                                                    {{ $roleDisplay }}
                                                </span>
                                            </div>

                                            <div class="profile-detail-row">
                                                <div class="detail-label"><i class="fas fa-user"></i> Full Name</div>
                                                <div class="detail-value">{{ $member->name }}</div>
                                            </div>
                                            <div class="profile-detail-row">
                                                <div class="detail-label"><i class="fas fa-envelope"></i> Email</div>
                                                <div class="detail-value">{{ $member->email }}</div>
                                            </div>
                                            <div class="profile-detail-row">
                                                <div class="detail-label"><i class="fas fa-phone"></i> Phone</div>
                                                <div class="detail-value">{{ $member->phone ?? '-' }}</div>
                                            </div>
                                            <div class="profile-detail-row">
                                                <div class="detail-label"><i class="fas fa-building"></i> Department</div>
                                                <div class="detail-value">{{ $department->name }}</div>
                                            </div>
                                            <div class="profile-detail-row">
                                                <div class="detail-label"><i class="fas fa-calendar-alt"></i> Since
                                                </div>
                                                <div class="detail-value">{{ $member->created_at->format('d M Y') }}</div>
                                            </div>
                                            <div class="profile-detail-row">
                                                <div class="detail-label"><i class="fas fa-ticket-alt"></i> MR Created
                                                </div>
                                                <div class="detail-value">
                                                    <strong>{{ $member->tickets_count ?? 0 }}</strong> MR
                                                </div>
                                            </div>
                                            <div class="profile-detail-row">
                                                <div class="detail-label"><i class="fas fa-check-circle"></i> Status</div>
                                                <div class="detail-value">
                                                    @if ($member->status === 'active')
                                                        <span class="status-badge-active">Active</span>
                                                    @elseif($member->status === 'inactive')
                                                        <span class="status-badge-inactive">Inactive</span>
                                                    @else
                                                        <span class="status-badge-pending">Pending</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                <i class="fas fa-times me-1"></i> Close
                                            </button>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Top Senders Chart -->
            <div class="card-custom">
                <div class="card-title">
                    <i class="fas fa-chart-bar"></i>
                    <span>Top MR Senders</span>
                </div>
                @if ($topSenders->count() > 0)
                    <div class="chart-container">
                        <canvas id="topSendersChart"></canvas>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-chart-line"></i>
                        <p>No MR data available yet</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column - Recent Tickets -->
        <div class="card-custom">
            <div class="card-title">
                <i class="fas fa-history"></i>
                <span>Recent MR</span>
            </div>
            @if ($recentTickets->count() > 0)
                @foreach ($recentTickets as $ticket)
                    @php
                        $badgeClass = match ($ticket->status) {
                            'open' => 'badge-open',
                            'received', 'in_progress' => 'badge-progress',
                            'pending_om', 'pending_vr', 'pending_gm', 'ready_for_closure' => 'badge-pending',
                            'completed' => 'badge-completed',
                            'closed' => 'badge-closed',
                            'cancelled' => 'badge-canceled',
                            default => 'badge-pending',
                        };
                        $statusText = strtoupper(str_replace('_', ' ', $ticket->status));
                        if ($ticket->status === 'in_progress') {
                            $statusText = 'IN PROGRESS';
                        }
                    @endphp
                    <div class="ticket-item">
                        <a href="{{ route('tickets.show', $ticket->id) }}" class="ticket-number">
                            #{{ $ticket->ticket_number }}
                        </a>
                        <div class="ticket-title">{{ $ticket->title }}</div>
                        <div class="ticket-meta">
                            <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                            <span style="color: {{ $ticket->priority->color ?? '#666' }};">●
                                {{ $ticket->priority->name ?? 'N/A' }}</span>
                            <span><i class="far fa-user"></i> {{ $ticket->user->name ?? 'N/A' }}</span>
                            <span><i class="far fa-calendar"></i> {{ $ticket->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                @endforeach
                <div class="text-center mt-3">
                    <a href="{{ route('tickets.index', ['department' => $department->id]) }}" class="btn btn-sm"
                        style="color: var(--navy); border: 1px solid var(--navy); border-radius: 8px;">
                        View All Requests <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No MR data available yet</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Stats Modal -->
    <div class="modal fade stats-modal" id="statsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statsModalTitle">
                        <i class="fas fa-chart-pie me-2" style="color: var(--navy);"></i>
                        <span id="statsTitleText">MR Statistics</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="statsModalBody">
                    <!-- Content will be populated by JS -->
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
        // Toastr config
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };

        // Detailed stats data from backend
        const detailedStats = @json($detailedStats);

        // Stats Modal
        let statsModal;
        document.addEventListener('DOMContentLoaded', function() {
            statsModal = new bootstrap.Modal(document.getElementById('statsModal'));
        });

        function showStatsModal(statType) {
            let title = '';
            let content = '';

            switch (statType) {
                case 'total':
                    title = '📊 Complete Request Statistics';
                    content = `
                        <div class="stat-row"><span class="stat-label-modal">Open:</span><span class="stat-value-modal">${detailedStats.open || 0}</span></div>
                        <div class="stat-row"><span class="stat-label-modal">Received:</span><span class="stat-value-modal">${detailedStats.received || 0}</span></div>
                        <div class="stat-row"><span class="stat-label-modal">In Progress:</span><span class="stat-value-modal">${detailedStats.in_progress || 0}</span></div>
                        <div class="stat-row"><span class="stat-label-modal">Pending OM:</span><span class="stat-value-modal">${detailedStats.pending_om || 0}</span></div>
                        <div class="stat-row"><span class="stat-label-modal">Pending VR:</span><span class="stat-value-modal">${detailedStats.pending_vr || 0}</span></div>
                        <div class="stat-row"><span class="stat-label-modal">Pending GM:</span><span class="stat-value-modal">${detailedStats.pending_gm || 0}</span></div>
                        <div class="stat-row"><span class="stat-label-modal">Ready for Closure:</span><span class="stat-value-modal">${detailedStats.ready_for_closure || 0}</span></div>
                        <div class="stat-row"><span class="stat-label-modal">Completed:</span><span class="stat-value-modal">${detailedStats.completed || 0}</span></div>
                        <div class="stat-row"><span class="stat-label-modal">Closed:</span><span class="stat-value-modal">${detailedStats.closed || 0}</span></div>
                        <div class="stat-row"><span class="stat-label-modal">Canceled:</span><span class="stat-value-modal">${detailedStats.cancelled || 0}</span></div>
                        <div class="stat-row stat-total"><span class="stat-label-modal">⚠️ Overdue:</span><span class="stat-value-modal" style="color: #f57c00;">${detailedStats.overdue || 0}</span></div>
                    `;
                    break;
                case 'in_progress':
                    title = '🔄 In Progress MR';
                    const inProgressTotal = (detailedStats.received || 0) + (detailedStats.in_progress || 0) +
                        (detailedStats.pending_om || 0) + (detailedStats.pending_vr || 0) +
                        (detailedStats.ready_for_closure || 0);
                    content = `
                        <div class="stat-row"><span class="stat-label-modal">Received:</span><span class="stat-value-modal">${detailedStats.received || 0}</span></div>
                        <div class="stat-row"><span class="stat-label-modal">In Progress:</span><span class="stat-value-modal">${detailedStats.in_progress || 0}</span></div>
                        <div class="stat-row"><span class="stat-label-modal">Pending OM:</span><span class="stat-value-modal">${detailedStats.pending_om || 0}</span></div>
                        <div class="stat-row"><span class="stat-label-modal">Pending VR:</span><span class="stat-value-modal">${detailedStats.pending_vr || 0}</span></div>
                        <div class="stat-row"><span class="stat-label-modal">Ready for Closure:</span><span class="stat-value-modal">${detailedStats.ready_for_closure || 0}</span></div>
                        <div class="stat-row stat-total"><span class="stat-label-modal">Total In Progress:</span><span class="stat-value-modal" style="font-size: 16px;">${inProgressTotal}</span></div>
                    `;
                    break;
                case 'completed':
                    const completedTotal = (detailedStats.completed || 0) + (detailedStats.closed || 0);
                    title = '✅ Completed MR';
                    content = `
                        <div class="stat-row"><span class="stat-label-modal">Completed (pending GM):</span><span class="stat-value-modal">${detailedStats.completed || 0}</span></div>
                        <div class="stat-row"><span class="stat-label-modal">Closed (finalized):</span><span class="stat-value-modal">${detailedStats.closed || 0}</span></div>
                        <div class="stat-row stat-total"><span class="stat-label-modal">Total Completed:</span><span class="stat-value-modal" style="font-size: 16px;">${completedTotal}</span></div>
                    `;
                    break;
                case 'canceled':
                    title = '❌ Canceled MR';
                    content = `
                        <div class="stat-row"><span class="stat-label-modal">Canceled:</span><span class="stat-value-modal">${detailedStats.cancelled || 0}</span></div>
                        <div class="stat-row"><span class="stat-label-modal">Reason:</span><span class="stat-value-modal">Request was canceled before completion</span></div>
                    `;
                    break;
                default:
                    return;
            }

            document.getElementById('statsTitleText').textContent = title;
            document.getElementById('statsModalBody').innerHTML = content;
            statsModal.show();
        }

        // Top Senders Chart
        @if ($topSenders->count() > 0)
            const ctx = document.getElementById('topSendersChart').getContext('2d');
            const chartData = @json($topSenders);
            const isMobile = window.innerWidth < 768;

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.map(item => {
                        const maxLen = isMobile ? 10 : 15;
                        return item.name.length > maxLen ? item.name.substring(0, maxLen - 3) + '...' : item
                            .name;
                    }),
                    datasets: [{
                        label: 'MR',
                        data: chartData.map(item => item.total),
                        backgroundColor: '#003366',
                        borderRadius: 6,
                        barPercentage: 0.7,
                        categoryPercentage: 0.8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    return chartData[context[0].dataIndex].name;
                                },
                                label: function(context) {
                                    return `MR: ${context.raw}`;
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
                                    size: 11
                                },
                                precision: 0
                            },
                            grid: {
                                color: '#e5e7eb'
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 11
                                },
                                maxRotation: isMobile ? 45 : 0,
                                minRotation: isMobile ? 45 : 0
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    layout: {
                        padding: {
                            top: 10,
                            bottom: 5
                        }
                    }
                }
            });
        @endif

        // Edit Department Name
        function editDepartmentName(currentName) {
            Swal.fire({
                title: 'Edit Department Name',
                input: 'text',
                inputValue: currentName,
                showCancelButton: true,
                confirmButtonText: 'Save',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#003366',
                cancelButtonColor: '#6c757d',
                inputValidator: (value) => {
                    if (!value) return 'Department name cannot be empty!';
                    if (value.length > 255) return 'Department name too long (max 255 characters)';
                },
                showLoaderOnConfirm: true,
                preConfirm: (newName) => {
                    return fetch('{{ route('my-department.update-name') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            department_name: newName
                        })
                    }).then(response => response.json()).catch(error => {
                        Swal.showValidationMessage(`Request failed: ${error}`);
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed && result.value.success) {
                    document.querySelector('.dept-name').childNodes[0].nodeValue = result.value.new_name + ' ';
                    toastr.success(result.value.message);
                }
            });
        }
    </script>
@endpush
