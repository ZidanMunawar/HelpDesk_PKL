{{-- resources/views/activity-logs/partials/details.blade.php --}}
@if (isset($log))
    <div class="log-details-container">
        <div class="detail-section">
            <h6 class="detail-title"><i class="fas fa-info-circle"></i> Basic Information</h6>
            <table class="detail-table">
                <tr>
                    <td width="140"><strong>ID</strong></td>
                    <td>#{{ $log->id }}</td>
                </tr>
                <tr>
                    <td><strong>Date & Time</strong></td>
                    <td>{{ $log->created_at->format('d F Y, H:i:s') }}</td>
                </tr>
                <tr>
                    <td><strong>Action</strong></td>
                    <td><span
                            class="log-badge log-badge-{{ $log->action }}">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</span>
                    </td>
                </tr>
                <tr>
                    <td><strong>User</strong></td>
                    <td>{{ $log->user->name ?? 'System' }} <span
                            class="text-muted">({{ $log->user->role ?? 'system' }})</span></td>
                </tr>
                <tr>
                    <td><strong>IP Address</strong></td>
                    <td>{{ $log->ip_address ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>User Agent</strong></td>
                    <td><small>{{ $log->user_agent ?? 'N/A' }}</small></td>
                </tr>
            </table>
        </div>

        <div class="detail-section">
            <h6 class="detail-title"><i class="fas fa-file-alt"></i> Description</h6>
            <div class="detail-description">
                {{ $log->description }}
            </div>
        </div>

        @if ($log->ticket)
            <div class="detail-section">
                <h6 class="detail-title"><i class="fas fa-ticket-alt"></i> Related Ticket</h6>
                <table class="detail-table">
                    <tr>
                        <td width="140"><strong>Ticket Number</strong></td>
                        <td><strong>#{{ $log->ticket->ticket_number }}</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Title</strong></td>
                        <td>{{ $log->ticket->title }}</td>
                    </tr>
                    <tr>
                        <td><strong>Status</strong></td>
                        <td><span
                                class="ticket-status status-{{ $log->ticket->status }}">{{ str_replace('_', ' ', $log->ticket->status) }}</span>
                        </td>
                    </tr>
                </table>
                <div class="mt-2">
                    <a href="{{ route('tickets.show', $log->ticket_id) }}" target="_blank" class="btn-view-ticket">
                        <i class="fas fa-external-link-alt"></i> View Full Ticket
                    </a>
                </div>
            </div>
        @endif

        @if ($log->old_values || $log->new_values)
            <div class="detail-section">
                <h6 class="detail-title"><i class="fas fa-exchange-alt"></i> Changes</h6>
                <div class="row">
                    @if ($log->old_values)
                        <div class="col-md-6">
                            <div class="old-values">
                                <strong class="text-danger">Old Values</strong>
                                <pre class="mt-1"><code>{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</code></pre>
                            </div>
                        </div>
                    @endif
                    @if ($log->new_values)
                        <div class="col-md-6">
                            <div class="new-values">
                                <strong class="text-success">New Values</strong>
                                <pre class="mt-1"><code>{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</code></pre>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
@else
    <div class="text-center py-4">
        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
        <p>Log details not found.</p>
    </div>
@endif
