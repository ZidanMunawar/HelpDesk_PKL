{{-- resources/views/activity-logs/partials/logs-list.blade.php --}}
@if ($logs->count() > 0)
    @foreach ($logs as $log)
        <div class="log-card" data-log-id="{{ $log->id }}">
            <div class="log-card-content">
                <div class="log-header">
                    <div class="log-icon-wrapper">
                        <div class="log-icon log-icon-{{ $log->action }}">
                            <i
                                class="fas fa-{{ match ($log->action) {
                                    'login' => 'sign-in-alt',
                                    'logout' => 'sign-out-alt',
                                    'created' => 'plus-circle',
                                    'deleted' => 'trash',
                                    'updated' => 'edit',
                                    'login_failed' => 'times-circle',
                                    'user_registered' => 'user-plus',
                                    'user_deleted' => 'user-minus',
                                    'user_reset' => 'undo-alt',
                                    'status_changed' => 'exchange-alt',
                                    'assigned' => 'user-check',
                                    'commented' => 'comment',
                                    'password_reset_requested' => 'envelope',
                                    'password_reset' => 'key',
                                    'password_reset_failed' => 'exclamation-triangle',
                                    default => 'info-circle',
                                } }}"></i>
                        </div>
                    </div>
                    <div class="log-info">
                        <div class="log-title">
                            @php
                                $badgeClass = match ($log->action) {
                                    'login' => 'log-badge-login',
                                    'logout' => 'log-badge-logout',
                                    'created' => 'log-badge-created',
                                    'updated' => 'log-badge-updated',
                                    'deleted' => 'log-badge-deleted',
                                    'login_failed' => 'log-badge-danger',
                                    'user_registered' => 'log-badge-success',
                                    'user_deleted' => 'log-badge-danger',
                                    'user_reset' => 'log-badge-warning',
                                    'status_changed' => 'log-badge-warning',
                                    'assigned' => 'log-badge-assigned',
                                    'commented' => 'log-badge-commented',
                                    'approved' => 'log-badge-approved',
                                    'completed' => 'log-badge-completed',
                                    'rejected' => 'log-badge-rejected',
                                    'broadcast' => 'log-badge-primary',
                                    default => 'log-badge-secondary',
                                };

                                $badgeText = match ($log->action) {
                                    'login_failed' => 'Login Failed',
                                    'user_registered' => 'User Registered',
                                    'user_deleted' => 'User Deleted',
                                    'user_reset' => 'User Reset',
                                    'status_changed' => 'Status Changed',
                                    'password_reset_requested' => 'Password Reset Requested',
                                    'password_reset' => 'Password Reset',
                                    'password_reset_failed' => 'Password Reset Failed',
                                    'broadcast' => 'broadcast-tower',
                                    default => ucfirst(str_replace('_', ' ', $log->action)),
                                };
                            @endphp
                            <span class="log-badge {{ $badgeClass }}">{{ $badgeText }}</span>
                            <span class="log-user">
                                <i class="fas fa-user-circle"></i>
                                {{ $log->user->name ?? 'System' }}
                                <span class="log-role">({{ $log->user->role ?? 'system' }})</span>
                            </span>
                        </div>
                        <div class="log-time">
                            <i class="far fa-calendar-alt"></i>
                            {{ $log->created_at->format('d M Y, H:i:s') }}
                            <span class="log-relative">({{ $log->created_at->diffForHumans() }})</span>
                        </div>
                    </div>
                    <div class="log-actions">
                        <button class="btn-view-log" onclick="viewLogDetails({{ $log->id }})" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="log-description">
                    <p>{{ $log->description }}</p>
                </div>
                @if ($log->ticket)
                    <div class="log-ticket">
                        <i class="fas fa-ticket-alt"></i>
                        Ticket: <strong>#{{ $log->ticket->ticket_number }}</strong>
                        <span class="text-muted">- {{ $log->ticket->title }}</span>
                        <a href="{{ route('tickets.show', $log->ticket_id) }}" target="_blank" class="btn-ticket-link">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                @endif
                @if ($log->ip_address)
                    <div class="log-meta">
                        <span><i class="fas fa-globe"></i> {{ $log->ip_address }}</span>
                        @if ($log->user_agent)
                            <span class="ms-3"><i class="fas fa-desktop"></i>
                                {{ Str::limit($log->user_agent, 50) }}</span>
                        @endif
                    </div>
                @endif
                @if ($log->old_values || $log->new_values)
                    <div class="log-changes">
                        <button class="btn-toggle-changes" onclick="toggleChanges({{ $log->id }})">
                            <i class="fas fa-exchange-alt"></i> View Changes
                        </button>
                        <div id="changes-{{ $log->id }}" class="changes-content" style="display: none;">
                            <div class="row">
                                @if ($log->old_values)
                                    <div class="col-md-6">
                                        <strong class="text-danger">Old Values:</strong>
                                        <pre class="mt-1"><code>{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</code></pre>
                                    </div>
                                @endif
                                @if ($log->new_values)
                                    <div class="col-md-6">
                                        <strong class="text-success">New Values:</strong>
                                        <pre class="mt-1"><code>{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</code></pre>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
@else
    <div class="empty-state">
        <i class="fas fa-search"></i>
        <h4>No Activity Logs Found</h4>
        <p>Try changing your filters or search criteria.</p>
    </div>
@endif
