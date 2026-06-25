<div class="ticket-list">
    @php
        $statusDisplayMap = [
            'open' => 'Open',
            'received' => 'Received',
            'pending_om' => 'OM Approval',
            'in_progress' => 'In Progress',
            'pending_vr' => 'PR Approval',
            'completed' => 'Completed',
            'pending_gm' => 'GM Approval',
            'ready_for_closure' => 'Ready for Closure',
            'closed' => 'Closed',
            'cancelled' => 'Cancelled',
        ];
    @endphp

    @forelse($tickets as $ticket)
        @php
            $statusDisplay = $statusDisplayMap[$ticket->status] ?? str_replace('_', ' ', $ticket->status);
        @endphp

        <div class="ticket-card" onclick="viewTicket({{ $ticket->id }})">
            <div class="ticket-header">
                <div class="ticket-number">#{{ $ticket->ticket_number }}</div>
                <span class="status-badge status-{{ $ticket->status }}">
                    {{ $statusDisplay }}
                </span>
            </div>

            <div class="ticket-title" title="{{ $ticket->title }}">
                {{ Str::limit($ticket->title, 80) }}
            </div>

            <div class="ticket-meta">
                <div class="ticket-meta-item">
                    <i class="fas fa-user"></i>
                    <span title="{{ $ticket->user->name }}">{{ Str::limit($ticket->user->name, 20) }}</span>
                </div>

                <div class="ticket-meta-item">
                    <i class="fas fa-folder"></i>
                    <span title="{{ $ticket->category->name }}">{{ Str::limit($ticket->category->name, 20) }}</span>
                </div>

                @if ($ticket->department)
                    <div class="ticket-meta-item">
                        <i class="fas fa-building"></i>
                        <span
                            title="{{ $ticket->department->name }}">{{ Str::limit($ticket->department->name, 18) }}</span>
                    </div>
                @endif

                <div class="ticket-meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>
                        @if ($ticket->location)
                            {{ Str::limit($ticket->location->name, 18) }}
                            @if ($ticket->location->floor_number)
                                (F{{ $ticket->location->floor_number }})
                            @endif
                        @elseif($ticket->location_manual)
                            {{ Str::limit($ticket->location_manual, 18) }}
                        @else
                            N/A
                        @endif
                    </span>
                </div>

                <div class="ticket-meta-item">
                    <i class="fas fa-clock"></i>
                    <span>{{ $ticket->created_at->diffForHumans() }}</span>
                </div>
            </div>

            <div class="ticket-footer">
                @if ($ticket->assigned_to)
                    <span class="badge-sm badge-assigned" title="Assigned To">
                        <i class="fas fa-user-tag"></i>
                        {{ Str::limit($ticket->assignedUser->name ?? 'N/A', 12) }}
                    </span>
                @endif

                @if ($ticket->due_date)
                    <span class="badge-sm badge-date" title="Due Date">
                        <i class="fas fa-calendar-alt"></i>
                        {{ \Carbon\Carbon::parse($ticket->due_date)->format('M d, Y') }}
                        @if ($ticket->due_date < now())
                            <i class="fas fa-exclamation-triangle text-danger"></i>
                        @endif
                    </span>
                @endif

                <span class="priority-badge" style="background-color: {{ $ticket->priority->color }}">
                    {{ $ticket->priority->name }}
                </span>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <h5 class="empty-state-title">No Maintenance Requests Found</h5>
            <p class="empty-state-text">
                @if (request()->anyFilled(['search', 'status', 'category', 'priority', 'department', 'date_from', 'date_to']))
                    Try adjusting your search criteria or clear filters.
                @else
                    There are no maintenance requests in the system yet.
                @endif
            </p>
            <div class="empty-state-buttons">
                @if (request()->anyFilled(['search', 'status', 'category', 'priority', 'department', 'date_from', 'date_to']))
                    <button onclick="resetAllFilters()" class="btn-reset">
                        <i class="fas fa-redo-alt"></i> Clear Filters
                    </button>
                @endif
                @if (in_array(auth()->user()->role, ['admin_eng', 'user', 'manager']))
                    <a href="{{ route('tickets.create') }}" class="btn-modern btn-create"
                        style="text-decoration: none;">
                        <i class="fas fa-plus-circle"></i> Create New MR
                    </a>
                @endif
            </div>
        </div>
    @endforelse

    <!-- Pagination -->
    <div class="pagination-wrapper">
        {{ $tickets->withQueryString()->links('pagination::bootstrap-4') }}
    </div>
</div>

<script>
    function resetAllFilters() {
        window.location.href = '{{ route('tickets.index') }}';
    }
</script>
