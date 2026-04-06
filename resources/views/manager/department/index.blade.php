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
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">
    <style>
        :root {
            --navy: #003366;
            --orange: #ff6600;
            --navy-light: #1e4a7a;
            --orange-light: #ff8533;
        }

        /* Department Header */
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

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-left: 4px solid var(--orange);
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 51, 102, 0.1);
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--navy);
            line-height: 1.2;
            margin-bottom: 5px;
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

        /* Two Column Layout */
        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        /* Card */
        .card-custom {
            background: white;
            border-radius: 10px;
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

        /* Department Users List */
        .user-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .user-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border: 1px solid #f0f0f0;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .user-item:hover {
            border-color: var(--orange);
            background: #fff8f0;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--navy);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 12px;
        }

        .user-info {
            flex: 1;
        }

        .user-name {
            font-weight: 600;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .user-role-badge {
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 12px;
            background: #f0f0f0;
            color: #666;
            text-transform: uppercase;
        }

        .user-email {
            font-size: 12px;
            color: #888;
        }

        /* Recent Tickets */
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

        /* Mobile */
        @media (max-width: 768px) {
            .dept-name {
                font-size: 20px;
                flex-wrap: wrap;
            }

            .dept-icon {
                width: 50px;
                height: 50px;
                font-size: 24px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .two-column {
                grid-template-columns: 1fr;
            }

            .stat-value {
                font-size: 22px;
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
            <div class="stat-value">{{ $ticketStats['total'] }}</div>
            <div class="stat-label"><i class="fas fa-ticket-alt"></i> TOTAL</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $ticketStats['open'] }}</div>
            <div class="stat-label"><i class="fas fa-folder-open"></i> OPEN</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $ticketStats['in_progress'] }}</div>
            <div class="stat-label"><i class="fas fa-spinner"></i> IN PROGRESS</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $ticketStats['completed'] }}</div>
            <div class="stat-label"><i class="fas fa-check-circle"></i> COMPLETED</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $ticketStats['pending_gm'] }}</div>
            <div class="stat-label"><i class="fas fa-clock"></i> PENDING GM</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $ticketStats['closed'] }}</div>
            <div class="stat-label"><i class="fas fa-check-double"></i> CLOSED</div>
        </div>
        @if ($ticketStats['overdue'] > 0)
            <div class="stat-card">
                <div class="stat-value" style="color: #dc3545;">{{ $ticketStats['overdue'] }}</div>
                <div class="stat-label"><i class="fas fa-exclamation-triangle"></i> OVERDUE</div>
            </div>
        @endif
    </div>

    <!-- Two Column Content -->
    <div class="two-column">
        <!-- Left Column - Department Members -->
        @if ($departmentUsers->count() > 0)
            <div class="card-custom">
                <div class="card-title">
                    <i class="fas fa-users"></i>
                    <span>Department Members ({{ $departmentUsers->count() }})</span>
                </div>
                <div class="user-list">
                    @foreach ($departmentUsers as $member)
                        <div class="user-item">
                            <div class="user-avatar">
                                {{ substr($member->name, 0, 1) }}
                            </div>
                            <div class="user-info">
                                <div class="user-name">
                                    {{ $member->name }}
                                    <span class="user-role-badge">{{ ucfirst($member->role) }}</span>
                                </div>
                                <div class="user-email">{{ $member->email }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Right Column - Recent Tickets -->
        <div class="card-custom">
            <div class="card-title">
                <i class="fas fa-history"></i>
                <span>Recent Tickets</span>
            </div>
            @if ($recentTickets->count() > 0)
                @foreach ($recentTickets as $ticket)
                    @php
                        $badgeClass = '';
                        $statusText = $ticket->status;

                        switch ($ticket->status) {
                            case 'open':
                                $badgeClass = 'badge-open';
                                $statusText = 'OPEN';
                                break;
                            case 'in_progress':
                                $badgeClass = 'badge-progress';
                                $statusText = 'IN PROGRESS';
                                break;
                            case 'pending_om':
                            case 'pending_vr':
                            case 'pending_gm':
                                $badgeClass = 'badge-pending';
                                $statusText = strtoupper(str_replace('_', ' ', $ticket->status));
                                break;
                            case 'completed':
                                $badgeClass = 'badge-completed';
                                $statusText = 'COMPLETED';
                                break;
                            case 'closed':
                            case 'cancelled':
                                $badgeClass = 'badge-closed';
                                $statusText = 'CLOSED';
                                break;
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
                        style="color: var(--navy); border: 1px solid var(--navy);">
                        View All Tickets <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            @else
                <div class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p>No tickets yet</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Toastr -->
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

    <script>
        // Toastr config
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };

        // Edit Department Name with SweetAlert
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
                    if (!value) {
                        return 'Department name cannot be empty!';
                    }
                    if (value.length > 255) {
                        return 'Department name too long (max 255 characters)';
                    }
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
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(data => {
                                    throw new Error(data.message || 'Failed to update');
                                });
                            }
                            return response.json();
                        })
                        .catch(error => {
                            Swal.showValidationMessage(`Request failed: ${error}`);
                        });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed && result.value.success) {
                    // Update nama di halaman
                    const deptNameElement = document.querySelector('.dept-name');
                    deptNameElement.childNodes[0].nodeValue = result.value.new_name + ' ';

                    toastr.success(result.value.message);
                }
            });
        }
    </script>
@endpush
