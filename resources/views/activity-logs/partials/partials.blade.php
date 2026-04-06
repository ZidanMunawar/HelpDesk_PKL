{{-- resources/views/activity-logs/partials/details.blade.php --}}
@php
    function getActionIcon($action)
    {
        $icons = [
            'created' => 'plus-circle',
            'updated' => 'edit',
            'deleted' => 'trash',
            'received' => 'check-circle',
            'assigned' => 'user-plus',
            'approved' => 'thumbs-up',
            'rejected' => 'thumbs-down',
            'completed' => 'check-double',
            'commented' => 'comment',
            'cancelled' => 'ban',
            'closed' => 'lock',
            'login' => 'sign-in-alt',
            'logout' => 'sign-out-alt',
            'vr_created' => 'file-invoice',
            'vr_approved' => 'check-circle',
            'vr_rejected' => 'times-circle',
            'vr_paid' => 'money-check',
            'broadcast_sent' => 'bullhorn',
            'notification_read' => 'envelope-open',
            'notifications_marked_read' => 'check-double',
            'notification_deleted' => 'trash',
            'notifications_cleared' => 'trash-alt',
        ];
        return $icons[$action] ?? 'info-circle';
    }
@endphp

<div class="row">
    <div class="col-md-12">
        <!-- Log Information -->
        <div class="card mb-3">
            <div class="card-header" style="background: var(--primary-navy); color: white;">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i> Log Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="120"><strong>ID:</strong></td>
                                <td>#{{ $log->id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Date & Time:</strong></td>
                                <td>{{ $log->created_at->format('d F Y, H:i:s') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Action:</strong></td>
                                <td>
                                    <span class="action-badge {{ $log->action }}">
                                        <i class="fas fa-{{ getActionIcon($log->action) }} me-1"></i>
                                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Description:</strong></td>
                                <td>{{ $log->description }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="120"><strong>User:</strong></td>
                                <td>
                                    @if ($log->user)
                                        {{ $log->user->name }} <span
                                            class="text-muted">({{ ucfirst($log->user->role) }})</span>
                                    @else
                                        <span class="text-muted">System</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>IP Address:</strong></td>
                                <td>{{ $log->ip_address ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>User Agent:</strong></td>
                                <td><small>{{ $log->user_agent ?? 'N/A' }}</small></td>
                            </tr>
                            @if ($log->ticket)
                                <tr>
                                    <td><strong>Related Ticket:</strong></td>
                                    <td>
                                        <a href="{{ route('tickets.show', $log->ticket_id) }}" target="_blank"
                                            class="text-orange">
                                            #{{ $log->ticket->ticket_number }}
                                        </a>
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Changes -->
        @if ($log->old_values || $log->new_values)
            <div class="card mb-3">
                <div class="card-header" style="background: var(--primary-orange); color: white;">
                    <h6 class="mb-0"><i class="fas fa-exchange-alt me-2"></i> Changes Made</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if ($log->old_values)
                            <div class="col-md-6">
                                <h6 class="text-danger mb-2"><i class="fas fa-arrow-left me-1"></i> Old Values</h6>
                                <div class="bg-light p-3 rounded">
                                    <pre class="mb-0 small"><code>{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</code></pre>
                                </div>
                            </div>
                        @endif
                        @if ($log->new_values)
                            <div class="col-md-6">
                                <h6 class="text-success mb-2"><i class="fas fa-arrow-right me-1"></i> New Values</h6>
                                <div class="bg-light p-3 rounded">
                                    <pre class="mb-0 small"><code>{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</code></pre>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Ticket Information -->
        @if ($log->ticket)
            <div class="card">
                <div class="card-header" style="background: #2c3e50; color: white;">
                    <h6 class="mb-0"><i class="fas fa-ticket-alt me-2"></i> Ticket Information</h6>
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
                        <a href="{{ route('tickets.show', $log->ticket_id) }}" class="btn btn-sm"
                            style="background: var(--primary-navy); color: white;" target="_blank">
                            <i class="fas fa-external-link-alt me-1"></i> View Full Ticket
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    .action-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .action-badge.created {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .action-badge.updated {
        background: #e3f2fd;
        color: #1976d2;
    }

    .action-badge.deleted {
        background: #ffebee;
        color: #c62828;
    }

    .action-badge.login {
        background: #e3f2fd;
        color: #1976d2;
    }

    .action-badge.logout {
        background: #f5f5f5;
        color: #616161;
    }

    .action-badge.approved {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .action-badge.rejected {
        background: #ffebee;
        color: #c62828;
    }

    .action-badge.commented {
        background: #fff3e0;
        color: #f57c00;
    }

    .action-badge.completed {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .action-badge.cancelled {
        background: #f5f5f5;
        color: #616161;
    }

    .action-badge.closed {
        background: #e8eaf6;
        color: #283593;
    }

    .text-orange {
        color: var(--primary-orange);
    }
</style>
