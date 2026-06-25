<div class="vr-details">
    <!-- VR Header -->
    <div class="vr-header-details mb-4">
        <div class="d-flex justify-content-between align-items-start">
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
                <span
                    class="badge bg-{{ $vr->status === 'paid' ? 'success' : ($vr->status === 'rejected' ? 'danger' : ($vr->status === 'gm_approved' ? 'success' : 'warning')) }} fs-6">
                    {{ strtoupper(str_replace('_', ' ', $vr->status)) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Quick Info -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title text-muted">TOTAL PHOTOS</h6>
                    <h3 class="text-primary">{{ $vr->attachments->count() }}</h3>
                    <p class="card-text small">photo(s) uploaded</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title text-muted">CREATED BY</h6>
                    <h5>{{ $vr->creator->name }}</h5>
                    <p class="card-text small">{{ ucfirst($vr->creator->role) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title text-muted">TICKET</h6>
                    <h5>#{{ $vr->ticket->ticket_number }}</h5>
                    <p class="card-text small">{{ Str::limit($vr->ticket->title, 30) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Approval Timeline -->
    <div class="card mb-4">
        <div class="card-header">
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
                        <div class="small text-muted">{{ $vr->admin_approved_at->format('d M, H:i') }}</div>
                        @if ($vr->adminApprover)
                            <div class="small text-muted">{{ $vr->adminApprover->name }}</div>
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
                        <div class="small text-muted">{{ $vr->om_approved_at->format('d M, H:i') }}</div>
                        @if ($vr->omApprover)
                            <div class="small text-muted">{{ $vr->omApprover->name }}</div>
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
                        <div class="small text-muted">{{ $vr->gm_approved_at->format('d M, H:i') }}</div>
                        @if ($vr->gmApprover)
                            <div class="small text-muted">{{ $vr->gmApprover->name }}</div>
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
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Photos ({{ $vr->attachments->count() }})</h6>
        </div>
        <div class="card-body">
            @if ($vr->attachments->count() > 0)
                <div class="row">
                    @foreach ($vr->attachments as $attachment)
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <img src="{{ Storage::url($attachment->file_path) }}" class="card-img-top"
                                    alt="VR Photo" style="height: 200px; object-fit: cover; cursor: pointer;"
                                    onclick="window.open('{{ Storage::url($attachment->file_path) }}', '_blank')">
                                <div class="card-body">
                                    <p class="card-text small text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $attachment->created_at->format('d M Y, H:i') }}
                                    </p>
                                    @if ($attachment->description)
                                        <p class="card-text">{{ $attachment->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center text-muted py-4">
                    <i class="fas fa-camera fa-3x mb-3"></i>
                    <p>No photos uploaded</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Notes -->
    @if ($vr->notes)
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Notes & History</h6>
            </div>
            <div class="card-body">
                <pre class="mb-0" style="white-space: pre-wrap; font-family: inherit;">{{ $vr->notes }}</pre>
            </div>
        </div>
    @endif
</div>

<style>
    .vr-details .card {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .vr-details .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e0e0e0;
    }
</style>
