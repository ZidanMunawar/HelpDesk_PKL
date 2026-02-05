@php
    // Helper functions (sama seperti di index)
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
@endphp

<div class="row">
    <div class="col-md-12">
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i> Log Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>ID:</strong> #{{ $log->id }}</p>
                        <p><strong>Date & Time:</strong> {{ $log->created_at->format('d F Y, H:i:s') }}</p>
                        <p><strong>Action:</strong>
                            <span
                                class="badge bg-{{ in_array($log->action, ['created', 'approved', 'completed'])
                                    ? 'success'
                                    : (in_array($log->action, ['rejected', 'cancelled'])
                                        ? 'danger'
                                        : ($log->action === 'commented'
                                            ? 'warning'
                                            : 'primary')) }}">
                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                            </span>
                        </p>
                        <p><strong>Description:</strong> {{ $log->description }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>User:</strong>
                            @if ($log->user)
                                {{ $log->user->name }} ({{ ucfirst($log->user->role) }})
                            @else
                                System
                            @endif
                        </p>
                        <p><strong>IP Address:</strong> {{ $log->ip_address ?? 'N/A' }}</p>
                        <p><strong>User Agent:</strong> {{ $log->user_agent ?? 'N/A' }}</p>
                        @if ($log->ticket)
                            <p><strong>Related Ticket:</strong>
                                <a href="{{ route('tickets.show', $log->ticket_id) }}" target="_blank">
                                    #{{ $log->ticket->ticket_number }}
                                </a>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if ($log->old_values || $log->new_values)
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-exchange-alt me-2"></i> Changes Made</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if ($log->old_values)
                            <div class="col-md-6">
                                <h6 class="text-danger"><i class="fas fa-arrow-left me-1"></i> Old Values</h6>
                                <div class="bg-light p-3 rounded">
                                    <pre class="mb-0 small">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                        @endif

                        @if ($log->new_values)
                            <div class="col-md-6">
                                <h6 class="text-success"><i class="fas fa-arrow-right me-1"></i> New Values</h6>
                                <div class="bg-light p-3 rounded">
                                    <pre class="mb-0 small">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if ($log->ticket)
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="fas fa-ticket-alt me-2"></i> Related Ticket Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Ticket Number:</strong> #{{ $log->ticket->ticket_number }}</p>
                            <p><strong>Title:</strong> {{ $log->ticket->title }}</p>
                            <p><strong>Category:</strong> {{ $log->ticket->category->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Status:</strong>
                                <span class="badge status-{{ $log->ticket->status }}">
                                    {{ str_replace('_', ' ', $log->ticket->status) }}
                                </span>
                            </p>
                            <p><strong>Priority:</strong>
                                <span class="badge"
                                    style="background-color: {{ $log->ticket->priority->color ?? '#6c757d' }}">
                                    {{ $log->ticket->priority->name ?? 'N/A' }}
                                </span>
                            </p>
                            <p><strong>Created:</strong> {{ $log->ticket->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div class="col-md-4">
                            @if ($log->ticket->assigned_to)
                                <p><strong>Assigned To:</strong> {{ $log->ticket->assignedUser->name ?? 'N/A' }}</p>
                            @endif
                            @if ($log->ticket->due_date)
                                <p><strong>Due Date:</strong> {{ $log->ticket->due_date->format('d M Y, H:i') }}</p>
                            @endif
                            @if ($log->ticket->closed_at)
                                <p><strong>Closed At:</strong> {{ $log->ticket->closed_at->format('d M Y, H:i') }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('tickets.show', $log->ticket_id) }}" class="btn btn-primary btn-sm"
                            target="_blank">
                            <i class="fas fa-external-link-alt me-1"></i> View Full Ticket
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
