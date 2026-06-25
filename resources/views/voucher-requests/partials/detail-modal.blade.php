<div class="container-fluid p-0">
    <!-- Header Info dengan 2 Kolom -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="info-card" style="background: #f8f9fa; border-radius: 12px; padding: 15px;">
                <div class="d-flex align-items-center gap-3 mb-3 pb-2 border-bottom border-secondary">
                    <i class="fas fa-receipt fs-4" style="color: var(--orange);"></i>
                    <h6 class="mb-0 fw-bold" style="color: var(--navy);">PR INFORMATION</h6>
                </div>
                <div class="row g-2">
                    <div class="col-5 text-muted small">PR Number:</div>
                    <div class="col-7 fw-bold" style="color: var(--orange);">{{ $vr->vr_number }}</div>

                    <div class="col-5 text-muted small">Status:</div>
                    <div class="col-7">
                        <span class="pr-status {{ $vr->status }}">
                            {{ str_replace('_', ' ', ucfirst($vr->status)) }}
                        </span>
                    </div>

                    <div class="col-5 text-muted small">Created By:</div>
                    <div class="col-7">{{ $vr->creator->name ?? 'Unknown' }}</div>

                    <div class="col-5 text-muted small">Created At:</div>
                    <div class="col-7">{{ $vr->created_at->format('d M Y, H:i') }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="info-card" style="background: #f8f9fa; border-radius: 12px; padding: 15px;">
                <div class="d-flex align-items-center gap-3 mb-3 pb-2 border-bottom border-secondary">
                    <i class="fas fa-ticket-alt fs-4" style="color: var(--orange);"></i>
                    <h6 class="mb-0 fw-bold" style="color: var(--navy);">TICKET INFORMATION</h6>
                </div>
                <div class="row g-2">
                    <div class="col-5 text-muted small">Ticket Number:</div>
                    <div class="col-7">
                        <a href="{{ route('tickets.show', $vr->ticket_id) }}" target="_blank"
                            style="color: var(--navy); font-weight: 500; text-decoration: none;">
                            #{{ $vr->ticket->ticket_number ?? 'N/A' }}
                        </a>
                    </div>

                    <div class="col-5 text-muted small">Ticket Title:</div>
                    <div class="col-7">{{ Str::limit($vr->ticket->title ?? 'N/A', 50) }}</div>

                    <div class="col-5 text-muted small">Department:</div>
                    <div class="col-7">{{ $vr->ticket->department->name ?? 'N/A' }}</div>

                    <div class="col-5 text-muted small">Priority:</div>
                    <div class="col-7">
                        @if ($vr->ticket->priority)
                            <span class="priority-badge"
                                style="background-color: {{ $vr->ticket->priority->color }}; padding: 2px 8px; border-radius: 12px; font-size: 11px; color: white;">
                                {{ $vr->ticket->priority->name }}
                            </span>
                        @else
                            N/A
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes Section -->
    @if ($vr->notes)
        <div class="mb-4">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="fas fa-sticky-note" style="color: var(--orange);"></i>
                <strong style="color: var(--navy);">Notes</strong>
            </div>
            <div
                style="background: #fefce8; border-left: 3px solid var(--orange); padding: 12px 15px; border-radius: 8px;">
                {{ $vr->notes }}
            </div>
        </div>
    @endif

    <!-- Rejection Reason (if rejected) -->
    @if ($vr->rejection_reason)
        <div class="mb-4">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="fas fa-exclamation-triangle" style="color: #dc2626;"></i>
                <strong style="color: #dc2626;">Rejection Reason</strong>
            </div>
            <div style="background: #fef2f2; border-left: 3px solid #dc2626; padding: 12px 15px; border-radius: 8px;">
                {{ $vr->rejection_reason }}
            </div>
        </div>
    @endif

    <!-- Approval Timeline - Vertical Stepper Style -->
    <div class="mb-4">
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="fas fa-clock" style="color: var(--orange);"></i>
            <strong style="color: var(--navy);">Approval Timeline</strong>
        </div>

        <div style="position: relative;">
            <!-- Timeline Line -->
            <div style="position: absolute; left: 24px; top: 30px; bottom: 30px; width: 2px; background: #e5e7eb;">
            </div>

            <!-- Admin Engineering -->
            <div style="display: flex; gap: 16px; margin-bottom: 24px; position: relative;">
                <div
                    style="width: 48px; height: 48px; background: {{ $vr->admin_approved ? '#10b981' : '#f3f4f6' }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    @if ($vr->admin_approved)
                        <i class="fas fa-check" style="color: white; font-size: 20px;"></i>
                    @else
                        <i class="fas fa-clock" style="color: #9ca3af; font-size: 20px;"></i>
                    @endif
                </div>
                <div style="flex: 1; padding-bottom: 8px;">
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                        <div>
                            <strong style="color: var(--navy);">Admin Engineering</strong>
                            <span class="badge {{ $vr->admin_approved ? 'bg-success' : 'bg-secondary' }} ms-2"
                                style="font-size: 10px;">
                                {{ $vr->admin_approved ? 'Approved' : 'Pending' }}
                            </span>
                        </div>
                        @if ($vr->admin_approved_at)
                            <small class="text-muted">{{ $vr->admin_approved_at->format('d M Y, H:i') }}</small>
                        @endif
                    </div>
                    @if ($vr->admin_approved_by)
                        <div class="text-muted small mt-1">
                            <i class="fas fa-user-check me-1"></i> {{ $vr->adminApprover->name ?? 'Unknown' }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- OM Approval -->
            <div style="display: flex; gap: 16px; margin-bottom: 24px; position: relative;">
                <div
                    style="width: 48px; height: 48px; background:
                    @if ($vr->om_approved) #10b981
                    @elseif($vr->admin_approved && !$vr->om_approved) #f59e0b
                    @else #f3f4f6 @endif;
                    border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">

                    @if ($vr->om_approved)
                        <i class="fas fa-check" style="color: white; font-size: 20px;"></i>
                    @elseif($vr->admin_approved && !$vr->om_approved)
                        <i class="fas fa-hourglass-half" style="color: white; font-size: 20px;"></i>
                    @else
                        <i class="fas fa-clock" style="color: #9ca3af; font-size: 20px;"></i>
                    @endif
                </div>
                <div style="flex: 1; padding-bottom: 8px;">
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                        <div>
                            <strong style="color: var(--navy);">Operation Manager</strong>
                            <span
                                class="badge
                                @if ($vr->om_approved) bg-success
                                @elseif($vr->admin_approved && !$vr->om_approved) bg-warning
                                @else bg-secondary @endif ms-2"
                                style="font-size: 10px;">
                                @if ($vr->om_approved)
                                    Approved
                                @elseif($vr->admin_approved && !$vr->om_approved)
                                    Waiting
                                @else
                                    Pending
                                @endif
                            </span>
                        </div>
                        @if ($vr->om_approved_at)
                            <small class="text-muted">{{ $vr->om_approved_at->format('d M Y, H:i') }}</small>
                        @endif
                    </div>
                    @if ($vr->om_approved_by)
                        <div class="text-muted small mt-1">
                            <i class="fas fa-user-check me-1"></i> {{ $vr->omApprover->name ?? 'Unknown' }}
                        </div>
                    @endif
                    @if ($vr->admin_approved && !$vr->om_approved && !$vr->om_approved_at)
                        <div class="text-muted small mt-1">
                            <i class="fas fa-spinner fa-pulse me-1"></i> Waiting for OM approval...
                        </div>
                    @endif
                </div>
            </div>

            <!-- GM Approval -->
            <div style="display: flex; gap: 16px; margin-bottom: 24px; position: relative;">
                <div
                    style="width: 48px; height: 48px; background:
                    @if ($vr->gm_approved) #10b981
                    @elseif($vr->om_approved && !$vr->gm_approved) #f59e0b
                    @else #f3f4f6 @endif;
                    border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">

                    @if ($vr->gm_approved)
                        <i class="fas fa-check" style="color: white; font-size: 20px;"></i>
                    @elseif($vr->om_approved && !$vr->gm_approved)
                        <i class="fas fa-hourglass-half" style="color: white; font-size: 20px;"></i>
                    @else
                        <i class="fas fa-clock" style="color: #9ca3af; font-size: 20px;"></i>
                    @endif
                </div>
                <div style="flex: 1; padding-bottom: 8px;">
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                        <div>
                            <strong style="color: var(--navy);">General Manager</strong>
                            <span
                                class="badge
                                @if ($vr->gm_approved) bg-success
                                @elseif($vr->om_approved && !$vr->gm_approved) bg-warning
                                @else bg-secondary @endif ms-2"
                                style="font-size: 10px;">
                                @if ($vr->gm_approved)
                                    Approved
                                @elseif($vr->om_approved && !$vr->gm_approved)
                                    Waiting
                                @else
                                    Pending
                                @endif
                            </span>
                        </div>
                        @if ($vr->gm_approved_at)
                            <small class="text-muted">{{ $vr->gm_approved_at->format('d M Y, H:i') }}</small>
                        @endif
                    </div>
                    @if ($vr->gm_approved_by)
                        <div class="text-muted small mt-1">
                            <i class="fas fa-user-check me-1"></i> {{ $vr->gmApprover->name ?? 'Unknown' }}
                        </div>
                    @endif
                    @if ($vr->om_approved && !$vr->gm_approved && !$vr->gm_approved_at)
                        <div class="text-muted small mt-1">
                            <i class="fas fa-spinner fa-pulse me-1"></i> Waiting for GM approval...
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment Status -->
            <div style="display: flex; gap: 16px; position: relative;">
                <div
                    style="width: 48px; height: 48px; background:
                    @if ($vr->status == 'paid') #047857
                    @elseif($vr->gm_approved && $vr->status != 'paid') #f59e0b
                    @else #f3f4f6 @endif;
                    border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">

                    @if ($vr->status == 'paid')
                        <i class="fas fa-money-bill-wave" style="color: white; font-size: 20px;"></i>
                    @elseif($vr->gm_approved && $vr->status != 'paid')
                        <i class="fas fa-hourglass-half" style="color: white; font-size: 20px;"></i>
                    @else
                        <i class="fas fa-clock" style="color: #9ca3af; font-size: 20px;"></i>
                    @endif
                </div>
                <div style="flex: 1; padding-bottom: 8px;">
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                        <div>
                            <strong style="color: var(--navy);">Payment</strong>
                            <span
                                class="badge
                                @if ($vr->status == 'paid') bg-success
                                @elseif($vr->gm_approved && $vr->status != 'paid') bg-warning
                                @else bg-secondary @endif ms-2"
                                style="font-size: 10px;">
                                @if ($vr->status == 'paid')
                                    Paid
                                @elseif($vr->gm_approved && $vr->status != 'paid')
                                    Waiting Payment
                                @else
                                    Not Started
                                @endif
                            </span>
                        </div>
                    </div>
                    @if ($vr->gm_approved && $vr->status != 'paid')
                        <div class="text-muted small mt-1">
                            <i class="fas fa-spinner fa-pulse me-1"></i> Waiting for payment confirmation...
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Photos Gallery -->
    @if ($vr->attachments && $vr->attachments->count() > 0)
        <div>
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="fas fa-camera" style="color: var(--orange);"></i>
                <strong style="color: var(--navy);">Photos ({{ $vr->attachments->count() }})</strong>
            </div>
            <div class="row g-2" id="vrPhotoGallery">
                @foreach ($vr->attachments as $photo)
                    <div class="col-4 col-md-3">
                        <div class="gallery-photo-item"
                            style="position: relative; border-radius: 10px; overflow: hidden; cursor: pointer; aspect-ratio: 1; border: 1px solid #e5e7eb;"
                            onclick="previewPhoto('{{ Storage::url($photo->file_path) }}')">
                            <img src="{{ Storage::url($photo->file_path) }}" alt="{{ $photo->file_name }}"
                                style="width: 100%; height: 100%; object-fit: cover;">
                            @if ($photo->description)
                                <div
                                    style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0,0,0,0.7)); padding: 6px; font-size: 10px; color: white; text-align: center;">
                                    {{ Str::limit($photo->description, 30) }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<style>
    .gallery-photo-item {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .gallery-photo-item:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .pr-status {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .pr-status.pending {
        background: #fef3c7;
        color: #d97706;
    }

    .pr-status.admin_approved {
        background: #dbeafe;
        color: #2563eb;
    }

    .pr-status.om_approved {
        background: #d1fae5;
        color: #059669;
    }

    .pr-status.gm_approved {
        background: #a7f3d0;
        color: #047857;
    }

    .pr-status.paid {
        background: #064e3b;
        color: white;
    }

    .pr-status.rejected {
        background: #fee2e2;
        color: #dc2626;
    }

    .fa-pulse {
        animation: fa-spin 1s infinite steps(8);
    }

    @keyframes fa-spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>
