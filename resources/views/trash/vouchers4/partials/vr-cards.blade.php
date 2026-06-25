@if ($vrs->count() > 0)
    @foreach ($vrs as $vr)
        <div class="vr-card" id="vr-card-{{ $vr->id }}" onclick="showVRModal({{ $vr->id }})">
            <div class="vr-header">
                <div>
                    <span class="vr-number">#{{ $vr->vr_number }}</span>
                </div>
                <span class="status-badge status-{{ $vr->status }}">
                    {{ str_replace('_', ' ', strtoupper($vr->status)) }}
                </span>
            </div>

            <div class="vr-title">
                {{ $vr->ticket->title }}
            </div>

            <div class="vr-meta">
                <div class="vr-meta-item">
                    <i class="fas fa-ticket-alt"></i>
                    <span class="vr-meta-text">#{{ $vr->ticket->ticket_number }}</span>
                </div>
                <div class="vr-meta-item">
                    <i class="fas fa-user"></i>
                    <span class="vr-meta-text">{{ $vr->creator->name }}</span>
                </div>
                <div class="vr-meta-item">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Rp {{ number_format($vr->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="vr-meta-item">
                    <i class="fas fa-boxes"></i>
                    <span>{{ $vr->items->count() }} items</span>
                </div>
                <div class="vr-meta-item">
                    <i class="fas fa-clock"></i>
                    <span>{{ $vr->created_at->diffForHumans() }}</span>
                </div>
            </div>

            @if ($vr->notes)
                <div class="small text-muted mb-2">
                    <i class="fas fa-sticky-note me-1"></i> {{ Str::limit($vr->notes, 50) }}
                </div>
            @endif

            <div class="vr-footer" onclick="event.stopPropagation()">
                @php
                    $user = auth()->user();
                    $canApprove = false;
                    $canReject = false;
                    $canMarkPaid = false;
                    $canDelete = false;

                    if ($user->role === 'admin_eng' && $vr->status === 'pending') {
                        $canApprove = $canReject = true;
                    } elseif ($user->role === 'om' && $vr->status === 'admin_approved') {
                        $canApprove = $canReject = true;
                    } elseif ($user->role === 'gm' && $vr->status === 'om_approved') {
                        $canApprove = $canReject = true;
                    }

                    if (in_array($user->role, ['admin_eng', 'superadmin']) && $vr->status === 'gm_approved') {
                        $canMarkPaid = true;
                    }

                    if ($user->role === 'superadmin') {
                        $canDelete = true;
                    } elseif ($vr->created_by === $user->id && in_array($vr->status, ['pending', 'rejected'])) {
                        $canDelete = true;
                    }
                @endphp

                <span class="badge-sm badge-date">
                    <i class="far fa-calendar-alt"></i> {{ $vr->created_at->format('d M Y') }}
                </span>

                @if ($vr->created_by === $user->id)
                    <span class="badge-sm badge-assigned">
                        <i class="fas fa-pen"></i> Created by me
                    </span>
                @endif

                <div class="vr-actions">
                    @if ($canApprove)
                        <button class="btn-vr-action btn-approve" onclick="approveVR({{ $vr->id }})">
                            <i class="fas fa-check"></i> Approve
                        </button>
                    @endif

                    @if ($canReject)
                        <button class="btn-vr-action btn-reject" onclick="rejectVR({{ $vr->id }})">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    @endif

                    @if ($canMarkPaid)
                        <button class="btn-vr-action btn-paid" onclick="markPaid({{ $vr->id }})">
                            <i class="fas fa-check-double"></i> Mark Paid
                        </button>
                    @endif

                    @if ($canDelete)
                        <button class="btn-vr-action btn-delete" onclick="deleteVR({{ $vr->id }})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endforeach

    <!-- Pagination -->
    @if ($vrs->hasPages())
        <div class="pagination-wrapper">
            {{ $vrs->withQueryString()->links() }}
        </div>
    @endif
@else
    <div class="empty-state">
        <div class="empty-state-icon">
            <i class="fas fa-file-invoice-dollar"></i>
        </div>
        <h4 class="empty-state-title">No Voucher Requests Found</h4>
        <p class="empty-state-text">There are no voucher requests matching your current filters.</p>
        @if (auth()->user()->role === 'admin_eng')
            <div class="empty-state-buttons">
                <button class="btn-modern btn-create" onclick="openCreateVRModal()">
                    <i class="fas fa-plus-circle"></i> Create New VR
                </button>
            </div>
        @endif
    </div>
@endif
