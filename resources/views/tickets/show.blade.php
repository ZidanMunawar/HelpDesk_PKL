@extends('layouts.main')

@section('title', 'Ticket #' . $ticket->ticket_number . ' | ' . config('app.name'))

@section('page-title', 'Ticket Detail')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'Tickets', 'url' => route('tickets.index')],
            ['title' => 'Ticket #' . $ticket->ticket_number, 'url' => 'javascript:void(0)'],
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

        .ticket-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .ticket-header h3 {
            color: white;
            margin-bottom: 10px;
        }

        .ticket-meta-item {
            display: inline-block;
            margin-right: 20px;
            font-size: 14px;
        }

        .ticket-meta-item i {
            margin-right: 5px;
        }

        .status-badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
            display: inline-block;
        }

        .priority-badge {
            padding: 4px 12px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 12px;
        }

        .ticket-description {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e0e0e0;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 25px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -25px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--primary);
            border: 3px solid white;
            box-shadow: 0 0 0 2px var(--primary);
        }

        .timeline-item.activity::before {
            background: #6c757d;
            box-shadow: 0 0 0 2px #6c757d;
        }

        .timeline-content {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }

        .comment-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .comment-header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .comment-meta {
            flex: 1;
        }

        .comment-author {
            font-weight: 600;
            color: #333;
            margin-bottom: 2px;
        }

        .comment-time {
            font-size: 12px;
            color: #6c757d;
        }

        .comment-body {
            color: #555;
            line-height: 1.6;
        }

        .attachment-item {
            display: inline-block;
            padding: 8px 15px;
            background: #f8f9fa;
            border-radius: 6px;
            margin-right: 10px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .attachment-item:hover {
            background: #e9ecef;
        }

        .attachment-item i {
            margin-right: 8px;
            color: var(--primary);
        }

        .comment-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .action-toolbar {
            margin-bottom: 20px;
        }

        .action-toolbar .btn-group {
            margin-right: 10px;
            margin-bottom: 10px;
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
                    <!-- Sidebar -->
                    <div class="email-left-box px-0 mb-5">
                        <div class="p-0">
                            <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-plus me-2"></i> New Ticket
                            </a>
                        </div>

                        <div class="mail-list mt-4 rounded">
                            <a href="{{ route('tickets.index') }}" class="list-group-item">
                                <i class="fa fa-inbox font-18 align-middle me-2"></i> All Tickets
                            </a>
                            <a href="{{ route('tickets.my-tickets') }}" class="list-group-item">
                                <i class="fa fa-file font-18 align-middle me-2"></i> My Tickets
                            </a>
                            @if (auth()->user()->role === 'admin' || auth()->user()->department_id)
                                <a href="#" class="list-group-item">
                                    <i class="fa fa-user font-18 align-middle me-2"></i> Assigned to Me
                                </a>
                            @endif
                            @if (auth()->user()->role === 'admin')
                                <a href="#" class="list-group-item">
                                    <i class="fa fa-folder font-18 align-middle me-2"></i> Unassigned
                                </a>
                            @endif
                        </div>

                        <div class="intro-title d-flex justify-content-between rounded mt-4">
                            <h5>Quick Actions</h5>
                        </div>
                        <div class="mail-list mt-0 rounded">
                            @if (auth()->user()->role === 'admin')
                                <a href="#" class="list-group-item"
                                    onclick="$('#assignModal').modal('show'); return false;">
                                    <i class="fa fa-user-plus"></i> Assign Ticket
                                </a>
                                <a href="#" class="list-group-item" onclick="confirmDelete(); return false;">
                                    <i class="fa fa-trash"></i> Delete Ticket
                                </a>
                            @endif
                            <a href="#" class="list-group-item" onclick="window.print(); return false;">
                                <i class="fa fa-print"></i> Print Ticket
                            </a>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="email-right-box clear-both ms-0 ms-sm-4 ms-sm-0">
                        <!-- Ticket Header -->
                        <div class="ticket-header">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 class="mb-2">#{{ $ticket->ticket_number }}</h3>
                                    <h4 class="mb-3">{{ $ticket->title }}</h4>
                                    <div class="ticket-meta">
                                        <span class="ticket-meta-item">
                                            <i class="fas fa-user"></i> {{ $ticket->user->name }}
                                        </span>
                                        <span class="ticket-meta-item">
                                            <i class="fas fa-calendar"></i> {{ $ticket->created_at->format('d M Y, H:i') }}
                                        </span>
                                        @if ($ticket->location)
                                            <span class="ticket-meta-item">
                                                <i class="fas fa-map-marker-alt"></i> {{ $ticket->location->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-end">
                                    @php
                                        $statusColors = [
                                            'open' => 'bg-primary',
                                            'in_progress' => 'bg-info',
                                            'pending' => 'bg-warning',
                                            'resolved' => 'bg-success',
                                            'closed' => 'bg-dark',
                                            'cancelled' => 'bg-danger',
                                        ];
                                    @endphp
                                    <span class="status-badge {{ $statusColors[$ticket->status] }} text-white">
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                    <br>
                                    <span class="priority-badge mt-2"
                                        style="background-color: {{ $ticket->priority->color }}; color: white;">
                                        {{ $ticket->priority->name }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Toolbar -->
                        <div class="action-toolbar">
                            @if (auth()->user()->role === 'admin')
                                <div class="btn-group mb-1">
                                    <button type="button" class="btn btn-primary light dropdown-toggle"
                                        data-bs-toggle="dropdown">
                                        <i class="fa fa-exchange"></i> Change Status
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#" onclick="updateStatus('open')">Open</a>
                                        <a class="dropdown-item" href="#" onclick="updateStatus('in_progress')">In
                                            Progress</a>
                                        <a class="dropdown-item" href="#"
                                            onclick="updateStatus('pending')">Pending</a>
                                        <a class="dropdown-item" href="#"
                                            onclick="updateStatus('resolved')">Resolved</a>
                                        <a class="dropdown-item" href="#" onclick="updateStatus('closed')">Closed</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item text-danger" href="#"
                                            onclick="updateStatus('cancelled')">Cancelled</a>
                                    </div>
                                </div>
                            @endif

                            <div class="btn-group mb-1">
                                <a href="{{ route('tickets.index') }}" class="btn btn-light">
                                    <i class="fa fa-arrow-left"></i> Back to List
                                </a>
                            </div>
                        </div>

                        <!-- Ticket Details -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Ticket Information</h5>
                                        <table class="table table-borderless">
                                            <tr>
                                                <td width="40%"><strong>Category:</strong></td>
                                                <td>{{ $ticket->category->name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Priority:</strong></td>
                                                <td>
                                                    <span class="priority-badge"
                                                        style="background-color: {{ $ticket->priority->color }}; color: white;">
                                                        {{ $ticket->priority->name }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Status:</strong></td>
                                                <td>
                                                    <span
                                                        class="badge badge-{{ str_replace('bg-', '', $statusColors[$ticket->status]) }}">
                                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @if ($ticket->location)
                                                <tr>
                                                    <td><strong>Location:</strong></td>
                                                    <td>{{ $ticket->location->name }}</td>
                                                </tr>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Assignment & Timeline</h5>
                                        <table class="table table-borderless">
                                            <tr>
                                                <td width="40%"><strong>Created By:</strong></td>
                                                <td>{{ $ticket->user->name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Assigned To:</strong></td>
                                                <td>
                                                    @if ($ticket->assignedUser)
                                                        {{ $ticket->assignedUser->name }}
                                                    @else
                                                        <span class="text-muted">Not assigned yet</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Created:</strong></td>
                                                <td>{{ $ticket->created_at->format('d M Y, H:i') }}</td>
                                            </tr>
                                            @if ($ticket->resolved_at)
                                                <tr>
                                                    <td><strong>Resolved:</strong></td>
                                                    <td>{{ $ticket->resolved_at->format('d M Y, H:i') }}</td>
                                                </tr>
                                            @endif
                                            @if ($ticket->due_date)
                                                <tr>
                                                    <td><strong>Due Date:</strong></td>
                                                    <td>{{ \Carbon\Carbon::parse($ticket->due_date)->format('d M Y, H:i') }}
                                                    </td>
                                                </tr>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="ticket-description">
                            <h5 class="mb-3"><i class="fas fa-clipboard-list me-2"></i> Description</h5>
                            <div class="description-content">
                                {!! $ticket->description !!}
                            </div>
                        </div>

                        <!-- Attachments (Original) -->
                        @if ($ticket->attachments->count() > 0)
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h6><i class="fa fa-paperclip me-2"></i> Attachments
                                        ({{ $ticket->attachments->count() }})</h6>
                                    <div class="mt-3">
                                        @foreach ($ticket->attachments as $attachment)
                                            <a href="{{ Storage::url($attachment->file_path) }}" target="_blank"
                                                class="attachment-item">
                                                <i class="fas fa-file"></i>
                                                {{ $attachment->file_name }}
                                                <small
                                                    class="text-muted">({{ number_format($attachment->file_size / 1024, 2) }}
                                                    KB)</small>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Activity Timeline & Comments -->
                        <div class="card">
                            <div class="card-body">
                                <h5 class="mb-4"><i class="fas fa-history me-2"></i> Activity Timeline & Follow Up</h5>

                                <div class="timeline">
                                    @php
                                        // Merge activities and comments, sort by date
                                        $timeline = collect();

                                        foreach ($ticket->activities as $activity) {
                                            $timeline->push([
                                                'type' => 'activity',
                                                'data' => $activity,
                                                'created_at' => $activity->created_at,
                                            ]);
                                        }

                                        foreach ($ticket->comments as $comment) {
                                            $timeline->push([
                                                'type' => 'comment',
                                                'data' => $comment,
                                                'created_at' => $comment->created_at,
                                            ]);
                                        }

                                        $timeline = $timeline->sortBy('created_at');
                                    @endphp

                                    @forelse($timeline as $item)
                                        @if ($item['type'] === 'activity')
                                            <div class="timeline-item activity">
                                                <div class="timeline-content">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <i class="fas fa-info-circle text-muted me-2"></i>
                                                            <strong>{{ $item['data']->description }}</strong>
                                                        </div>
                                                        <small class="text-muted">
                                                            {{ $item['data']->created_at->format('d M Y, H:i') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="timeline-item">
                                                <div class="timeline-content">
                                                    <div class="comment-header">
                                                        <img src="{{ $item['data']->user->profile_picture ? Storage::url($item['data']->user->profile_picture) : asset('assets/images/avatar/default.jpg') }}"
                                                            alt="{{ $item['data']->user->name }}">
                                                        <div class="comment-meta">
                                                            <div class="comment-author">
                                                                {{ $item['data']->user->name }}
                                                                @if ($item['data']->is_internal)
                                                                    <span
                                                                        class="badge badge-warning badge-sm">Internal</span>
                                                                @endif
                                                            </div>
                                                            <div class="comment-time">
                                                                {{ $item['data']->created_at->diffForHumans() }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="comment-body">
                                                        {!! nl2br(e($item['data']->comment)) !!}
                                                    </div>

                                                    @if ($item['data']->attachments->count() > 0)
                                                        <div class="mt-3">
                                                            <small class="text-muted"><i
                                                                    class="fas fa-paperclip me-1"></i>
                                                                Attachments:</small><br>
                                                            @foreach ($item['data']->attachments as $attachment)
                                                                <a href="{{ Storage::url($attachment->file_path) }}"
                                                                    target="_blank" class="attachment-item mt-2">
                                                                    <i class="fas fa-file"></i>
                                                                    {{ $attachment->file_name }}
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @empty
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p>No activity yet</p>
                                        </div>
                                    @endforelse
                                </div>

                                <!-- Add Comment Form -->
                                <div class="comment-form">
                                    <h6 class="mb-3"><i class="fas fa-comment me-2"></i> Add Follow Up / Comment</h6>
                                    <form id="commentForm" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <textarea name="comment" id="commentText" class="form-control" rows="4"
                                                placeholder="Write your follow up notes, progress updates, or comments here..." required></textarea>
                                        </div>

                                        @if (auth()->user()->role === 'admin')
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" name="is_internal"
                                                    id="isInternal">
                                                <label class="form-check-label" for="isInternal">
                                                    Internal note (only visible to staff)
                                                </label>
                                            </div>
                                        @endif

                                        <div class="form-group mb-3">
                                            <label class="form-label">Attach Files (Optional)</label>
                                            <input type="file" name="attachments[]" class="form-control" multiple
                                                accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                            <small class="text-muted">Max 5MB per file</small>
                                        </div>

                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane me-2"></i> Post Comment
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Modal (Admin Only) -->
    @if (auth()->user()->role === 'admin')
        <div class="modal fade" id="assignModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Assign Ticket</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="assignForm">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="form-label">Assign To</label>
                                <select name="assigned_to" class="form-select" required>
                                    <option value="">Select User</option>
                                    @foreach ($assignableUsers as $user)
                                        <option value="{{ $user->id }}"
                                            {{ $ticket->assigned_to == $user->id ? 'selected' : '' }}>
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
    @endif
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

            // Submit Comment
            $('#commentForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();

                submitBtn.prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin me-2"></i>Posting...');

                $.ajax({
                    url: "{{ route('tickets.add-comment', $ticket->id) }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Comment added successfully');
                            setTimeout(() => location.reload(), 1000);
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(originalText);
                        toastr.error(xhr.responseJSON?.message || 'Failed to add comment');
                    }
                });
            });

            // Assign Ticket
            @if (auth()->user()->role === 'admin')
                $('#assignForm').on('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);

                    $.ajax({
                        url: "{{ route('tickets.assign', $ticket->id) }}",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                $('#assignModal').modal('hide');
                                toastr.success(response.message);
                                setTimeout(() => location.reload(), 1000);
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message ||
                            'Failed to assign ticket');
                        }
                    });
                });
            @endif
        });

        // Update Status
        function updateStatus(status) {
            Swal.fire({
                title: 'Change Status?',
                text: `Change ticket status to ${status.replace('_', ' ')}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, change it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('tickets.update-status', $ticket->id) }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            status: status
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success('Status updated successfully');
                                setTimeout(() => location.reload(), 1000);
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Failed to update status');
                        }
                    });
                }
            });
        }

        // Delete Ticket
        function confirmDelete() {
            Swal.fire({
                title: 'Delete Ticket?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('tickets.destroy', $ticket->id) }}",
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Deleted!', 'Ticket has been deleted', 'success');
                                setTimeout(() => window.location.href = "{{ route('tickets.index') }}",
                                    1500);
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', xhr.responseJSON?.message || 'Failed to delete ticket',
                                'error');
                        }
                    });
                }
            });
        }
    </script>
@endpush
