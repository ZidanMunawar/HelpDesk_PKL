@extends('layouts.main')

@section('title', 'Unassigned Tickets | ' . config('app.name'))

@section('page-title', 'Unassigned Tickets')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'Tickets', 'url' => route('tickets.index')],
            ['title' => 'Unassigned Tickets', 'url' => 'javascript:void(0)'],
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
            position: relative;
        }

        .ticket-item:hover {
            background: #f8f9fa;
        }

        .ticket-item.urgent {
            background: #fff5f5;
            border-left: 4px solid #dc3545;
        }

        .ticket-item.selected {
            background: #e7f3ff;
            border-left: 4px solid var(--primary);
        }

        .ticket-checkbox {
            width: 20px;
            height: 20px;
            margin-right: 15px;
            cursor: pointer;
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

        .bulk-action-bar {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }

        .bulk-action-bar.active {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .bulk-action-bar .selected-count {
            font-weight: 600;
            font-size: 16px;
        }

        .alert-unassigned {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 8px;
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
                            <a href="{{ route('tickets.unassigned') }}"
                                class="list-group-item {{ !request('status') ? 'active' : '' }}">
                                <i class="fas fa-folder"></i> All Unassigned
                                <span class="badge badge-warning badge-sm">{{ $statusCounts['all'] }}</span>
                            </a>
                            <a href="{{ route('tickets.unassigned', ['status' => 'open']) }}"
                                class="list-group-item {{ request('status') == 'open' ? 'active' : '' }}">
                                <i class="fas fa-folder-open"></i> New
                                <span class="badge badge-primary badge-sm">{{ $statusCounts['open'] }}</span>
                            </a>
                            <a href="{{ route('tickets.unassigned', ['status' => 'in_progress']) }}"
                                class="list-group-item {{ request('status') == 'in_progress' ? 'active' : '' }}">
                                <i class="fas fa-spinner"></i> In Progress
                                <span class="badge badge-info badge-sm">{{ $statusCounts['in_progress'] }}</span>
                            </a>
                            <a href="{{ route('tickets.unassigned', ['status' => 'pending']) }}"
                                class="list-group-item {{ request('status') == 'pending' ? 'active' : '' }}">
                                <i class="fas fa-clock"></i> Pending
                                <span class="badge badge-secondary badge-sm">{{ $statusCounts['pending'] }}</span>
                            </a>
                        </div>

                        <!-- Category Filters -->
                        <div class="intro-title d-flex justify-content-between rounded">
                            <h5 class="mb-0">Categories</h5>
                            <i class="fa fa-chevron-down"></i>
                        </div>
                        <div class="mail-list mt-0 rounded">
                            @forelse($categories as $category)
                                <a href="{{ route('tickets.unassigned', ['category' => $category->id]) }}"
                                    class="list-group-item {{ request('category') == $category->id ? 'active' : '' }}">
                                    <span class="icon-primary"><i class="fa fa-circle"></i></span>
                                    {{ $category->name }}
                                </a>
                            @empty
                                <div class="p-3 text-center text-muted">
                                    <small>No categories</small>
                                </div>
                            @endforelse
                        </div>

                        <!-- Quick Links -->
                        <div class="intro-title d-flex justify-content-between rounded">
                            <h5 class="mb-0">Quick Links</h5>
                        </div>
                        <div class="mail-list mt-0 rounded">
                            <a href="{{ route('tickets.index') }}" class="list-group-item">
                                <i class="fas fa-inbox"></i> All Tickets
                            </a>
                            <a href="{{ route('tickets.my-tickets') }}" class="list-group-item">
                                <i class="fas fa-file"></i> My Tickets
                            </a>
                            <a href="{{ route('tickets.assigned') }}" class="list-group-item">
                                <i class="fas fa-user"></i> Assigned to Me
                            </a>
                        </div>
                    </div>

                    <div class="email-right-box ms-0 ms-sm-4 ms-sm-0">
                        <!-- Alert -->
                        @if ($statusCounts['all'] > 0)
                            <div class="alert-unassigned">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                                    <div>
                                        <strong>{{ $statusCounts['all'] }} Unassigned Ticket(s)</strong>
                                        <p class="mb-0 text-muted">These tickets need to be assigned to a technician or
                                            staff member.</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Bulk Action Bar -->
                        <div class="bulk-action-bar" id="bulkActionBar">
                            <div class="selected-count">
                                <i class="fas fa-check-circle me-2"></i>
                                <span id="selectedCount">0</span> ticket(s) selected
                            </div>
                            <div>
                                <select id="bulkAssignUser" class="form-select d-inline-block me-2" style="width: 200px;">
                                    <option value="">Select User</option>
                                    @foreach ($assignableUsers as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }} - {{ $user->department->name ?? 'No Dept' }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-light me-2" onclick="bulkAssign()">
                                    <i class="fas fa-user-plus me-1"></i> Assign Selected
                                </button>
                                <button type="button" class="btn btn-outline-light" onclick="clearSelection()">
                                    <i class="fas fa-times me-1"></i> Clear
                                </button>
                            </div>
                        </div>

                        <!-- Filter Section -->
                        <div class="filter-section">
                            <form action="{{ route('tickets.unassigned') }}" method="GET" id="filterForm">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Search by ticket number or title..."
                                            value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <select name="priority" class="form-select">
                                            <option value="">All Priorities</option>
                                            @foreach ($priorities as $priority)
                                                <option value="{{ $priority->id }}"
                                                    {{ request('priority') == $priority->id ? 'selected' : '' }}>
                                                    {{ $priority->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="location" class="form-select">
                                            <option value="">All Locations</option>
                                            @foreach ($locations as $location)
                                                <option value="{{ $location->id }}"
                                                    {{ request('location') == $location->id ? 'selected' : '' }}>
                                                    {{ $location->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary me-2">
                                            <i class="fas fa-search me-1"></i> Filter
                                        </button>
                                        <a href="{{ route('tickets.unassigned') }}" class="btn btn-light">
                                            <i class="fas fa-redo me-1"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Ticket List -->
                        <div class="ticket-list-container">
                            @forelse($tickets as $ticket)
                                @php
                                    $isUrgent = $ticket->priority->level == 1;
                                @endphp
                                <div class="ticket-item {{ $isUrgent ? 'urgent' : '' }}"
                                    data-ticket-id="{{ $ticket->id }}">
                                    <div class="d-flex align-items-start">
                                        <input type="checkbox" class="ticket-checkbox" value="{{ $ticket->id }}"
                                            onclick="event.stopPropagation(); toggleTicketSelection(this)">

                                        <div class="flex-grow-1"
                                            onclick="window.location='{{ route('tickets.show', $ticket->id) }}'"
                                            style="cursor: pointer;">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <div class="ticket-number">
                                                        #{{ $ticket->ticket_number }}
                                                        @if ($isUrgent)
                                                            <span class="badge badge-danger badge-sm ms-2">URGENT</span>
                                                        @endif
                                                    </div>
                                                    <div class="ticket-title">{{ $ticket->title }}</div>
                                                    <div class="ticket-meta">
                                                        <span>
                                                            <i class="fas fa-user"></i>
                                                            {{ $ticket->user->name }}
                                                        </span>
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
                                                    @endphp
                                                    <span
                                                        class="badge badge-{{ $statusColors[$ticket->status] }} ticket-badge">
                                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                                    </span>
                                                    <br>
                                                    <span class="badge ticket-badge mt-2"
                                                        style="background-color: {{ $ticket->priority->color }}; color: white;">
                                                        {{ $ticket->priority->name }}
                                                    </span>
                                                    <br>
                                                    <button type="button" class="btn btn-sm btn-primary mt-2"
                                                        onclick="event.stopPropagation(); quickAssign({{ $ticket->id }})">
                                                        <i class="fas fa-user-plus"></i> Quick Assign
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <i class="fas fa-check-double"></i>
                                    <h4>All Tickets Assigned!</h4>
                                    <p class="text-muted">Great! There are no unassigned tickets at the moment.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Assign Modal -->
    <div class="modal fade" id="quickAssignModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Quick Assign Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="quickAssignForm">
                    @csrf
                    <input type="hidden" id="quickAssignTicketId">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label">Assign To</label>
                            <select name="assigned_to" id="quickAssignUser" class="form-select" required>
                                <option value="">Select User</option>
                                @foreach ($assignableUsers as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->name }} - {{ $user->department->name ?? 'No Department' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Assign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

    <script>
        // Toastr configuration
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };

        let selectedTickets = [];

        // Toggle ticket selection
        function toggleTicketSelection(checkbox) {
            const ticketId = parseInt(checkbox.value);
            const ticketItem = checkbox.closest('.ticket-item');

            if (checkbox.checked) {
                selectedTickets.push(ticketId);
                ticketItem.classList.add('selected');
            } else {
                selectedTickets = selectedTickets.filter(id => id !== ticketId);
                ticketItem.classList.remove('selected');
            }

            updateBulkActionBar();
        }

        // Update bulk action bar
        function updateBulkActionBar() {
            const bulkBar = document.getElementById('bulkActionBar');
            const countSpan = document.getElementById('selectedCount');

            if (selectedTickets.length > 0) {
                bulkBar.classList.add('active');
                countSpan.textContent = selectedTickets.length;
            } else {
                bulkBar.classList.remove('active');
            }
        }

        // Clear selection
        function clearSelection() {
            selectedTickets = [];
            document.querySelectorAll('.ticket-checkbox').forEach(cb => {
                cb.checked = false;
            });
            document.querySelectorAll('.ticket-item').forEach(item => {
                item.classList.remove('selected');
            });
            updateBulkActionBar();
        }

        // Bulk assign tickets
        function bulkAssign() {
            const userId = document.getElementById('bulkAssignUser').value;

            if (!userId) {
                toastr.error('Please select a user to assign');
                return;
            }

            if (selectedTickets.length === 0) {
                toastr.error('Please select at least one ticket');
                return;
            }

            Swal.fire({
                title: 'Assign Tickets?',
                text: `Assign ${selectedTickets.length} ticket(s) to selected user?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, assign them',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('tickets.bulk-assign') }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            ticket_ids: selectedTickets,
                            assigned_to: userId
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Success!', response.message, 'success');
                                setTimeout(() => location.reload(), 1500);
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', xhr.responseJSON?.message || 'Failed to assign tickets',
                                'error');
                        }
                    });
                }
            });
        }

        // Quick assign single ticket
        function quickAssign(ticketId) {
            document.getElementById('quickAssignTicketId').value = ticketId;
            $('#quickAssignModal').modal('show');
        }

        // Quick assign form submit
        $('#quickAssignForm').on('submit', function(e) {
            e.preventDefault();

            const ticketId = document.getElementById('quickAssignTicketId').value;
            const userId = document.getElementById('quickAssignUser').value;

            $.ajax({
                url: `/tickets/${ticketId}/assign`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    assigned_to: userId
                },
                success: function(response) {
                    if (response.success) {
                        $('#quickAssignModal').modal('hide');
                        toastr.success(response.message);
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Failed to assign ticket');
                }
            });
        });
    </script>
@endpush
