<div class="vr-details">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1">#{{ $vr->vr_number }}</h4>
            <div class="text-muted">
                Created: {{ $vr->created_at->format('d F Y, H:i') }}
                @if ($vr->created_by === auth()->id())
                    <span class="badge bg-info ms-2">Created by me</span>
                @endif
            </div>
        </div>
        <div>
            <span class="vr-status status-{{ $vr->status }}"
                style="display: inline-block; padding: 6px 15px; border-radius: 20px;">
                {{ strtoupper(str_replace('_', ' ', $vr->status)) }}
            </span>
        </div>
    </div>

    <!-- Quick Info Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title text-muted">TICKET</h6>
                    <h5 class="mb-1">#{{ $vr->ticket->ticket_number }}</h5>
                    <p class="card-text small text-muted">{{ Str::limit($vr->ticket->title, 50) }}</p>
                    <a href="{{ route('tickets.show', $vr->ticket->id) }}" target="_blank" class="small">
                        <i class="fas fa-external-link-alt"></i> View Ticket
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title text-muted">CREATED BY</h6>
                    <h5 class="mb-1">{{ $vr->creator->name }}</h5>
                    <p class="card-text small">{{ ucfirst($vr->creator->role) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title text-muted">PHOTOS</h6>
                    <h5 class="mb-1">{{ $vr->attachments->count() }} photo(s)</h5>
                    <p class="card-text small">Uploaded as proof</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Approval Timeline -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">Approval Status</h6>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col">
                    <div class="mb-2">
                        <i class="fas fa-user-cog fa-2x {{ $vr->admin_approved ? 'text-success' : 'text-muted' }}"></i>
                    </div>
                    <div class="fw-bold">Admin Engineering</div>
                    <div class="small">{{ $vr->admin_approved ? 'Approved' : 'Pending' }}</div>
                    @if ($vr->admin_approved_at)
                        <div class="small text-muted">{{ $vr->admin_approved_at->format('d M Y, H:i') }}</div>
                        @if ($vr->adminApprover)
                            <div class="small text-muted">by {{ $vr->adminApprover->name }}</div>
                        @endif
                    @endif
                </div>
                <div class="col">
                    <div class="mb-2">
                        <i class="fas fa-user-tie fa-2x {{ $vr->om_approved ? 'text-success' : 'text-muted' }}"></i>
                    </div>
                    <div class="fw-bold">Operation Manager</div>
                    <div class="small">{{ $vr->om_approved ? 'Approved' : 'Pending' }}</div>
                    @if ($vr->om_approved_at)
                        <div class="small text-muted">{{ $vr->om_approved_at->format('d M Y, H:i') }}</div>
                        @if ($vr->omApprover)
                            <div class="small text-muted">by {{ $vr->omApprover->name }}</div>
                        @endif
                    @endif
                </div>
                <div class="col">
                    <div class="mb-2">
                        <i class="fas fa-user-shield fa-2x {{ $vr->gm_approved ? 'text-success' : 'text-muted' }}"></i>
                    </div>
                    <div class="fw-bold">General Manager</div>
                    <div class="small">{{ $vr->gm_approved ? 'Approved' : 'Pending' }}</div>
                    @if ($vr->gm_approved_at)
                        <div class="small text-muted">{{ $vr->gm_approved_at->format('d M Y, H:i') }}</div>
                        @if ($vr->gmApprover)
                            <div class="small text-muted">by {{ $vr->gmApprover->name }}</div>
                        @endif
                    @endif
                </div>
                <div class="col">
                    <div class="mb-2">
                        <i
                            class="fas fa-money-check-alt fa-2x {{ $vr->status === 'paid' ? 'text-success' : 'text-muted' }}"></i>
                    </div>
                    <div class="fw-bold">Payment</div>
                    <div class="small">{{ $vr->status === 'paid' ? 'Paid' : 'Pending' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Photos Gallery -->
    @if ($vr->attachments->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-images me-2"></i> Attached Photos ({{ $vr->attachments->count() }})
                </h6>
            </div>
            <div class="card-body">
                <div class="detail-photo-grid">
                    @foreach ($vr->attachments as $photo)
                        <div class="detail-photo-item" onclick="viewPhoto('{{ Storage::url($photo->file_path) }}')">
                            <img src="{{ Storage::url($photo->file_path) }}" alt="VR Photo">
                            <div class="photo-name" title="{{ $photo->file_name }}">
                                {{ Str::limit($photo->file_name, 20) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Notes -->
    @if ($vr->notes)
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i> Notes & History</h6>
            </div>
            <div class="card-body">
                <pre class="mb-0" style="white-space: pre-wrap; font-family: inherit;">{{ $vr->notes }}</pre>
            </div>
        </div>
    @endif

    <!-- Action Buttons -->
    @php
        $userRole = auth()->user()->role;
        $canApprove = false;
        $canReject = false;
        $canMarkPaid = false;
        $canDelete = false;

        if ($userRole === 'admin_eng' && $vr->status === 'pending') {
            $canApprove = $canReject = true;
        } elseif ($userRole === 'om' && $vr->status === 'admin_approved') {
            $canApprove = $canReject = true;
        } elseif ($userRole === 'gm' && $vr->status === 'om_approved') {
            $canApprove = $canReject = true;
        }

        if (in_array($userRole, ['admin_eng', 'superadmin']) && $vr->status === 'gm_approved') {
            $canMarkPaid = true;
        }

        if ($userRole === 'superadmin') {
            $canDelete = true;
        } elseif ($vr->created_by === auth()->id() && in_array($vr->status, ['pending', 'rejected'])) {
            $canDelete = true;
        }
    @endphp

    @if ($canApprove || $canReject || $canMarkPaid || $canDelete)
        <div class="mt-4 pt-3 border-top">
            <div class="d-flex flex-wrap gap-2 justify-content-end">
                @if ($canApprove)
                    <button class="btn btn-success" onclick="openApproveModal({{ $vr->id }})">
                        <i class="fas fa-check me-1"></i> Approve
                    </button>
                @endif
                @if ($canReject)
                    <button class="btn btn-danger" onclick="openRejectModal({{ $vr->id }})">
                        <i class="fas fa-times me-1"></i> Reject
                    </button>
                @endif
                @if ($canMarkPaid)
                    <button class="btn btn-info" onclick="openMarkPaidModal({{ $vr->id }})">
                        <i class="fas fa-check-double me-1"></i> Mark Paid
                    </button>
                @endif
                @if ($canDelete)
                    <button class="btn btn-danger" onclick="deleteVR({{ $vr->id }})">
                        <i class="fas fa-trash me-1"></i> Delete
                    </button>
                @endif
                <button class="btn btn-secondary" onclick="printVR({{ $vr->id }})">
                    <i class="fas fa-print me-1"></i> Print
                </button>
            </div>
        </div>
    @endif
</div>

<script>
    function viewPhoto(url) {
        Swal.fire({
            imageUrl: url,
            imageAlt: 'VR Photo',
            showCloseButton: true,
            showConfirmButton: false,
            width: 'auto',
            padding: '1em'
        });
    }
</script>
