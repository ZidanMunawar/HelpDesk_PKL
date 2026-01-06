@extends('layouts.main')

@section('title', 'My Tickets | ' . config('app.name'))

@section('page-title', 'My Tickets')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'Tickets', 'url' => 'javascript:void(0)'],
            ['title' => 'My Tickets', 'url' => 'javascript:void(0)'],
        ];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">

    <style>
        .email-left-box {
            width: 250px;
            float: left;
            padding: 0;
        }

        .email-right-box {
            margin-left: 270px;
        }

        .mail-list a {
            padding: 12px 20px;
            border-bottom: 1px solid #f0f1f5;
            display: block;
            color: #6e6e6e;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .mail-list a:hover,
        .mail-list a.active {
            background: var(--primary);
            color: white;
        }

        .mail-list a i {
            margin-right: 10px;
            font-size: 16px;
        }

        .mail-list a .badge {
            float: right;
            margin-top: 2px;
        }

        .intro-title {
            padding: 15px 20px;
            background: #f8f9fa;
            margin-top: 20px;
            font-weight: 600;
            cursor: pointer;
        }

        .ticket-item {
            padding: 15px;
            border-bottom: 1px solid #f0f1f5;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .ticket-item:hover {
            background: #f8f9fa;
        }

        .ticket-item.unread {
            background: #f8f9ff;
            font-weight: 600;
        }

        .ticket-number {
            font-size: 13px;
            color: var(--primary);
            font-weight: 600;
        }

        .ticket-title {
            font-size: 15px;
            color: #333;
            margin: 5px 0;
            font-weight: 500;
        }

        .ticket-meta {
            font-size: 12px;
            color: #6e6e6e;
            margin-top: 5px;
        }

        .ticket-meta span {
            margin-right: 15px;
        }

        .ticket-meta i {
            margin-right: 5px;
        }

        .ticket-badge {
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 500;
            border-radius: 4px;
        }

        .filter-section {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f0f1f5;
        }

        .filter-section .form-control,
        .filter-section .form-select {
            font-size: 14px;
            height: 40px;
        }

        .ticket-list-container {
            background: #fff;
            border-radius: 8px;
            border: 1px solid #f0f1f5;
            max-height: 700px;
            overflow-y: auto;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6e6e6e;
        }

        .empty-state i {
            font-size: 64px;
            color: #dee2e6;
            margin-bottom: 20px;
        }

        @media (max-width: 991px) {
            .email-left-box {
                width: 100%;
                float: none;
                margin-bottom: 30px;
            }

            .email-right-box {
                margin-left: 0;
            }
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="email-left-box px-0 mb-3">
                        <div class="p-0">
                            <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-plus me-2"></i> New Ticket
                            </a>
                        </div>

                        <!-- Status Filters -->
                        <div class="mail-list rounded mt-4">
                            <a href="{{ route('tickets.my-tickets') }}"
                                class="list-group-item {{ !request('status') ? 'active' : '' }}">
                                <i class="fas fa-file"></i> All My Tickets
                                <span class="badge badge-secondary badge-sm">{{ $statusCounts['all'] }}</span>
                            </a>
                            <a href="{{ route('tickets.my-tickets', ['status' => 'open']) }}"
                                class="list-group-item {{ request('status') == 'open' ? 'active' : '' }}">
                                <i class="fas fa-folder-open"></i> Open
                                <span class="badge badge-primary badge-sm">{{ $statusCounts['open'] }}</span>
                            </a>
                            <a href="{{ route('tickets.my-tickets', ['status' => 'in_progress']) }}"
                                class="list-group-item {{ request('status') == 'in_progress' ? 'active' : '' }}">
                                <i class="fas fa-spinner"></i> In Progress
                                <span class="badge badge-info badge-sm">{{ $statusCounts['in_progress'] }}</span>
                            </a>
                            <a href="{{ route('tickets.my-tickets', ['status' => 'resolved']) }}"
                                class="list-group-item {{ request('status') == 'resolved' ? 'active' : '' }}">
                                <i class="fas fa-check-circle"></i> Resolved
                                <span class="badge badge-success badge-sm">{{ $statusCounts['resolved'] }}</span>
                            </a>
                            <a href="{{ route('tickets.my-tickets', ['status' => 'closed']) }}"
                                class="list-group-item {{ request('status') == 'closed' ? 'active' : '' }}">
                                <i class="fas fa-times-circle"></i> Closed
                                <span class="badge badge-dark badge-sm">{{ $statusCounts['closed'] }}</span>
                            </a>
                        </div>

                        <!-- Category Filters -->
                        <div class="intro-title d-flex justify-content-between rounded">
                            <h5 class="mb-0">Categories</h5>
                            <i class="fa fa-chevron-down"></i>
                        </div>
                        <div class="mail-list mt-0 rounded">
                            @forelse($categories as $category)
                                <a href="{{ route('tickets.my-tickets', ['category' => $category->id]) }}"
                                    class="list-group-item {{ request('category') == $category->id ? 'active' : '' }}">
                                    <span class="icon-primary"><i class="fa fa-circle"></i></span>
                                    {{ $category->name }}
                                </a>
                            @empty
                                <div class="p-3 text-center text-muted">
                                    <small>No categories available</small>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="email-right-box ms-0 ms-sm-4 ms-sm-0">
                        <!-- Filter Section -->
                        <div class="filter-section">
                            <form action="{{ route('tickets.my-tickets') }}" method="GET" id="filterForm">
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Search by ticket number or title..."
                                            value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary me-2">
                                            <i class="fas fa-search me-1"></i> Search
                                        </button>
                                        <a href="{{ route('tickets.my-tickets') }}" class="btn btn-light">
                                            <i class="fas fa-redo me-1"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Ticket List -->
                        <div class="ticket-list-container">
                            @forelse($tickets as $ticket)
                                <div class="ticket-item {{ $ticket->status == 'open' ? 'unread' : '' }}"
                                    onclick="window.location='{{ route('tickets.show', $ticket->id) }}'">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="ticket-number">#{{ $ticket->ticket_number }}</div>
                                            <div class="ticket-title">{{ $ticket->title }}</div>
                                            <div class="ticket-meta">
                                                <span>
                                                    <i class="fas fa-folder"></i>
                                                    {{ $ticket->category->name }}
                                                </span>
                                                @if ($ticket->location)
                                                    <span>
                                                        <i class="fas fa-map-marker-alt"></i>
                                                        {{ $ticket->location->name }}
                                                    </span>
                                                @endif
                                                @if ($ticket->assignedUser)
                                                    <span>
                                                        <i class="fas fa-user-check"></i>
                                                        Assigned to {{ $ticket->assignedUser->name }}
                                                    </span>
                                                @endif
                                                <span>
                                                    <i class="fas fa-clock"></i>
                                                    {{ $ticket->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            @php
                                                $statusColors = [
                                                    'open' => 'primary',
                                                    'in_progress' => 'info',
                                                    'pending' => 'warning',
                                                    'resolved' => 'success',
                                                    'closed' => 'dark',
                                                    'cancelled' => 'danger',
                                                ];
                                                $priorityColors = [
                                                    1 => 'danger',
                                                    2 => 'warning',
                                                    3 => 'info',
                                                    4 => 'secondary',
                                                ];
                                            @endphp
                                            <span class="badge badge-{{ $statusColors[$ticket->status] }} ticket-badge">
                                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                            </span>
                                            <br>
                                            <span
                                                class="badge badge-{{ $priorityColors[$ticket->priority->level] ?? 'secondary' }} ticket-badge mt-2"
                                                style="background-color: {{ $ticket->priority->color }}">
                                                {{ $ticket->priority->name }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <h5>No Tickets Yet</h5>
                                    <p class="text-muted">You haven't created any tickets yet.</p>
                                    <a href="{{ route('tickets.create') }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-plus me-2"></i> Create Your First Ticket
                                    </a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Toastr configuration
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "3000"
            };
        });
    </script>
@endpush
