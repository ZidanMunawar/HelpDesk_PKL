@extends('layouts.main')

@section('title', 'Dashboard | ' . config('app.name'))

@section('page-title', 'Dashboard')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'App', 'url' => 'javascript:void(0)'],
            ['title' => 'Dashboard', 'url' => 'javascript:void(0)'],
        ];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

@section('content')
    @php
        $user = auth()->user();
    @endphp

    <!-- Welcome Card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Welcome, {{ $user->name }}!</h4>
                    <p class="mb-0">Role: <span class="badge bg-primary">{{ ucfirst($user->role) }}</span></p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <h6>Account Information</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{{ $user->email }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            @if ($user->isActive())
                                                <span class="badge bg-success">Active</span>
                                            @elseif($user->status === 'pending')
                                                <span class="badge bg-warning">Pending Approval</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Department:</strong></td>
                                        <td>{{ $user->department ? $user->department->name : 'Not assigned' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Registered:</strong></td>
                                        <td>{{ $user->created_at->format('d M Y') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <h6>Quick Actions</h6>
                                <div class="d-flex flex-wrap gap-2 mt-3">
                                    @if ($user->role === 'user')
                                        <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus me-1"></i> Create Ticket
                                        </a>
                                    @endif

                                    @if (in_array($user->role, ['admin_eng', 'superadmin']))
                                        <a href="{{ route('tickets.index') }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-list me-1"></i> View All Tickets
                                        </a>
                                    @endif

                                    @if ($user->role === 'technician')
                                        <a href="{{ route('tickets.index') }}?assigned_to={{ $user->id }}"
                                            class="btn btn-warning btn-sm">
                                            <i class="fas fa-tasks me-1"></i> My Tasks
                                        </a>
                                    @endif

                                    <a href="{{ route('profile.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-user me-1"></i> My Profile
                                    </a>

                                    @if ($user->role === 'superadmin')
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-danger btn-sm">
                                            <i class="fas fa-cog me-1"></i> Admin Panel
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        @if (in_array($user->role, ['superadmin', 'admin_eng', 'gm', 'om', 'manager']))
            <!-- Admin/Manager Stats -->
            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="text-white mb-0">{{ $totalTickets ?? '0' }}</h2>
                                <p class="mb-0">Total Tickets</p>
                            </div>
                            <div>
                                <i class="fas fa-ticket-alt fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="text-white mb-0">{{ $openTickets ?? '0' }}</h2>
                                <p class="mb-0">Open Tickets</p>
                            </div>
                            <div>
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="text-white mb-0">{{ $inProgressTickets ?? '0' }}</h2>
                                <p class="mb-0">In Progress</p>
                            </div>
                            <div>
                                <i class="fas fa-cogs fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="text-white mb-0">{{ $completedTickets ?? '0' }}</h2>
                                <p class="mb-0">Completed</p>
                            </div>
                            <div>
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($user->role === 'technician')
            <!-- Technician Stats -->
            <div class="col-xl-3 col-md-6">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="text-white mb-0">{{ $assignedTicketsCount ?? '0' }}</h2>
                                <p class="mb-0">Assigned to Me</p>
                            </div>
                            <div>
                                <i class="fas fa-user-cog fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="text-white mb-0">{{ $inProgressCount ?? '0' }}</h2>
                                <p class="mb-0">In Progress</p>
                            </div>
                            <div>
                                <i class="fas fa-spinner fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="text-white mb-0">{{ $completedCount ?? '0' }}</h2>
                                <p class="mb-0">Completed</p>
                            </div>
                            <div>
                                <i class="fas fa-check fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="text-white mb-0">{{ $pendingVRCount ?? '0' }}</h2>
                                <p class="mb-0">Pending VR</p>
                            </div>
                            <div>
                                <i class="fas fa-file-invoice-dollar fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($user->role === 'user')
            <!-- User Stats -->
            <div class="col-xl-4 col-md-6">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="text-white mb-0">{{ $myTicketsCount ?? '0' }}</h2>
                                <p class="mb-0">My Tickets</p>
                            </div>
                            <div>
                                <i class="fas fa-ticket-alt fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="text-white mb-0">{{ $myOpenTicketsCount ?? '0' }}</h2>
                                <p class="mb-0">Open</p>
                            </div>
                            <div>
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="text-white mb-0">{{ $myCompletedTicketsCount ?? '0' }}</h2>
                                <p class="mb-0">Resolved</p>
                            </div>
                            <div>
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Recent Activity</h4>
                </div>
                <div class="card-body">
                    @if (isset($recentActivities) && count($recentActivities) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Action</th>
                                        <th>Description</th>
                                        <th>Ticket</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentActivities as $activity)
                                        <tr>
                                            <td>{{ $activity->created_at->format('d M H:i') }}</td>
                                            <td>{!! $activity->action_badge !!}</td>
                                            <td>{{ Str::limit($activity->description, 50) }}</td>
                                            <td>
                                                @if ($activity->ticket)
                                                    <a href="{{ route('tickets.show', $activity->ticket) }}">
                                                        {{ $activity->ticket->ticket_number }}
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No recent activity found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .info-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            height: 100%;
        }

        .info-box h6 {
            color: #003366;
            font-weight: 600;
            margin-bottom: 15px;
            border-bottom: 2px solid #003366;
            padding-bottom: 8px;
        }

        .card.bg-primary {
            background: linear-gradient(135deg, #003366, #004080) !important;
        }

        .card.bg-success {
            background: linear-gradient(135deg, #28a745, #20c997) !important;
        }

        .card.bg-warning {
            background: linear-gradient(135deg, #ffc107, #fd7e14) !important;
        }

        .card.bg-info {
            background: linear-gradient(135deg, #17a2b8, #20c997) !important;
        }

        .card.bg-danger {
            background: linear-gradient(135deg, #dc3545, #c82333) !important;
        }
    </style>
@endpush
