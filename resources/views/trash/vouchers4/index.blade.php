@extends('layouts.main')

@section('title', 'Voucher Requests | ' . config('app.name'))

@section('page-title', 'Voucher Requests')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Voucher Requests', 'url' => 'javascript:void(0)'],
        ];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

@push('styles')
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
    <div class="page-header">
        <h2 class="page-title">Voucher Requests (VR)</h2>
        <p class="page-subtitle">Manage all voucher requests for maintenance tickets</p>
    </div>

    @php
        $user = auth()->user();

        $stats = [
            'all' => App\Models\VoucherRequest::count(),
            'pending' => App\Models\VoucherRequest::where('status', 'pending')->count(),
            'admin_approved' => App\Models\VoucherRequest::where('status', 'admin_approved')->count(),
            'om_approved' => App\Models\VoucherRequest::where('status', 'om_approved')->count(),
            'gm_approved' => App\Models\VoucherRequest::where('status', 'gm_approved')->count(),
            'paid' => App\Models\VoucherRequest::where('status', 'paid')->count(),
            'rejected' => App\Models\VoucherRequest::where('status', 'rejected')->count(),
        ];

        $pendingMyApproval = 0;
        if ($user->role === 'admin_eng') {
            $pendingMyApproval = $stats['pending'];
        } elseif ($user->role === 'om') {
            $pendingMyApproval = $stats['admin_approved'];
        } elseif ($user->role === 'gm') {
            $pendingMyApproval = $stats['om_approved'];
        }

        $activeFilter = request()->get('filter', 'all');
    @endphp

    <!-- Stats Cards - Clickable Filters -->
    <div class="stats-container">
        <div class="stats-card {{ $activeFilter === 'all' ? 'active' : '' }}" data-filter="all">
            <div class="stats-icon">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="stats-number">{{ $stats['all'] }}</div>
            <div class="stats-label">All VR</div>
        </div>

        @if (in_array($user->role, ['admin_eng', 'om', 'gm']))
            <div class="stats-card {{ $activeFilter === 'pending_my_approval' ? 'active' : '' }}"
                data-filter="pending_my_approval">
                <div class="stats-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-number">{{ $pendingMyApproval }}</div>
                <div class="stats-label">Need My Approval</div>
            </div>
        @endif

        <div class="stats-card {{ $activeFilter === 'pending' ? 'active' : '' }}" data-filter="pending">
            <div class="stats-icon">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="stats-number">{{ $stats['pending'] }}</div>
            <div class="stats-label">Pending</div>
        </div>

        <div class="stats-card {{ $activeFilter === 'approved' ? 'active' : '' }}" data-filter="approved">
            <div class="stats-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stats-number">{{ $stats['gm_approved'] + $stats['paid'] }}</div>
            <div class="stats-label">Approved</div>
        </div>

        <div class="stats-card {{ $activeFilter === 'paid' ? 'active' : '' }}" data-filter="paid">
            <div class="stats-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stats-number">{{ $stats['paid'] }}</div>
            <div class="stats-label">Paid</div>
        </div>

        <div class="stats-card {{ $activeFilter === 'rejected' ? 'active' : '' }}" data-filter="rejected">
            <div class="stats-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stats-number">{{ $stats['rejected'] }}</div>
            <div class="stats-label">Rejected</div>
        </div>
    </div>

    <!-- Create VR Button (Admin Eng only) -->
    <div class="mb-4">
        @if (auth()->user()->role === 'admin_eng')
            <button class="btn-create" onclick="openCreateVRModal()">
                <i class="fas fa-plus"></i> Create New VR
            </button>
        @endif
    </div>

    <!-- VR List -->
    <div id="vr-list" class="vr-list-container">
        @forelse($vrs as $vr)
            <div class="vr-card" data-vr-id="{{ $vr->id }}" data-status="{{ $vr->status }}">
                <div class="vr-header">
                    <div class="vr-header-left">
                        <div class="vr-number">#{{ $vr->vr_number }}</div>
                        <div class="vr-ticket-info">
                            <i class="fas fa-ticket-alt"></i>
                            #{{ $vr->ticket->ticket_number }}
                            <span class="vr-ticket-title">{{ Str::limit($vr->ticket->title, 40) }}</span>
                        </div>
                        <div class="vr-date">
                            <i class="far fa-calendar-alt"></i>
                            {{ $vr->created_at->format('d M Y, H:i') }}
                        </div>
                    </div>
                    <div class="vr-header-right">
                        <div class="vr-status status-{{ $vr->status }}">
                            {{ str_replace('_', ' ', $vr->status) }}
                        </div>
                        @if ($vr->created_by === auth()->id())
                            <span class="badge-my-vr">My VR</span>
                        @endif
                    </div>
                </div>

                <div class="vr-info">
                    <div class="info-item">
                        <div class="info-label">Created By</div>
                        <div class="info-value">
                            {{ $vr->creator->name }}<br>
                            <small class="text-muted">{{ ucfirst($vr->creator->role) }}</small>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Amount</div>
                        <div class="info-value amount-value">
                            Rp {{ number_format($vr->total_amount, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Photos</div>
                        <div class="info-value">
                            @php
                                $photoCount = $vr->attachments->count();
                            @endphp
                            @if ($photoCount > 0)
                                <i class="fas fa-image"></i> {{ $photoCount }} photo(s)
                            @else
                                <span class="text-muted">No photos</span>
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Last Update</div>
                        <div class="info-value">
                            {{ $vr->updated_at->format('d M Y, H:i') }}
                        </div>
                    </div>
                </div>

                <div class="vr-actions">
                    <button class="btn-vr btn-view" onclick="event.stopPropagation(); showVRModal({{ $vr->id }})">
                        <i class="fas fa-eye"></i> View Details
                    </button>

                    @php
                        $canApprove = false;
                        $canReject = false;
                        $canMarkPaid = false;
                        $canDelete = false;
                        $canPrint = true; // Dummy print always available

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

                    @if ($canApprove)
                        <button class="btn-vr btn-approve"
                            onclick="event.stopPropagation(); openApproveVRModal({{ $vr->id }})">
                            <i class="fas fa-check"></i> Approve
                        </button>
                    @endif

                    @if ($canReject)
                        <button class="btn-vr btn-reject"
                            onclick="event.stopPropagation(); openRejectVRModal({{ $vr->id }})">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    @endif

                    @if ($canMarkPaid)
                        <button class="btn-vr btn-paid"
                            onclick="event.stopPropagation(); openMarkPaidModal({{ $vr->id }})">
                            <i class="fas fa-check-double"></i> Mark Paid
                        </button>
                    @endif

                    @if ($canDelete)
                        <button class="btn-vr btn-delete" onclick="event.stopPropagation(); deleteVR({{ $vr->id }})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    @endif

                    @if ($canPrint)
                        <button class="btn-vr btn-print" onclick="event.stopPropagation(); printVR({{ $vr->id }})">
                            <i class="fas fa-print"></i> Print
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-file-invoice-dollar"></i>
                <h4>No Voucher Requests Found</h4>
                <p>There are no voucher requests matching your current permissions.</p>
                @if (auth()->user()->role === 'admin_eng')
                    <button class="btn-create" onclick="openCreateVRModal()">
                        <i class="fas fa-plus"></i> Create Your First VR
                    </button>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($vrs->hasPages())
        <div class="pagination-container">
            {{ $vrs->links() }}
        </div>
    @endif
@endsection

<!-- ============================================
    MODALS SECTION - All CSS inlined to prevent FOUT
============================================ -->

<!-- Create VR Modal -->
<div class="modal fade" id="createVRModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-invoice-dollar me-2"></i> Create Voucher Request
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="createVRModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Loading VR form...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View VR Modal -->
<div class="modal fade" id="viewVRModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i> Voucher Request Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-vr-body" id="viewVRModalBody">
                <!-- Content loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Approve VR Modal - With Signature -->
<div class="modal fade" id="approveVRModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i> Approve Voucher Request
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="approveVRForm">
                @csrf
                <input type="hidden" id="approveVRId">
                <div class="modal-body">
                    @php
                        $hasSignature =
                            !empty($user->signature_path) && Storage::disk('public')->exists($user->signature_path);
                    @endphp

                    @if ($hasSignature)
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            You have a saved signature. Choose your preferred signing method.
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="signature-option-card"
                                    onclick="$('#useSaved').prop('checked', true).trigger('change');">
                                    <input class="form-check-input" type="radio" name="signature_option"
                                        id="useSaved" value="saved" checked>
                                    <label class="form-check-label" for="useSaved">
                                        <i class="fas fa-save fa-2x mb-2" style="color: var(--navy);"></i>
                                        <h6>Use Saved Signature</h6>
                                        <p class="small text-muted mb-0">Quick approve with your existing signature</p>
                                    </label>
                                    @if ($hasSignature)
                                        <div class="mt-2">
                                            <img src="{{ Storage::url($user->signature_path) }}"
                                                alt="Saved Signature"
                                                style="max-height: 50px; border: 1px solid #ddd; padding: 4px; border-radius: 4px;">
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="signature-option-card"
                                    onclick="$('#createNew').prop('checked', true).trigger('change');">
                                    <input class="form-check-input" type="radio" name="signature_option"
                                        id="createNew" value="new">
                                    <label class="form-check-label" for="createNew">
                                        <i class="fas fa-pen fa-2x mb-2" style="color: var(--orange);"></i>
                                        <h6>Create New Signature</h6>
                                        <p class="small text-muted mb-0">Draw a new signature for this approval</p>
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- New Signature Canvas Section -->
                    <div id="newSignatureSection" style="{{ $hasSignature ? 'display: none;' : 'display: block;' }}">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Draw Your Signature *</label>
                            <div class="signature-canvas-container">
                                <canvas id="approveSignatureCanvas" class="signature-canvas" width="300"
                                    height="200"
                                    style="width: 100%; max-width: 300px; height: auto; border: 2px dashed #ccc; border-radius: 8px; background: transparent; cursor: crosshair; display: block; margin: 0 auto;"></canvas>
                            </div>
                            <div class="signature-actions text-center mt-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                    onclick="clearSignature()">
                                    <i class="fas fa-eraser me-1"></i> Clear
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                    onclick="undoSignature()">
                                    <i class="fas fa-undo me-1"></i> Undo
                                </button>
                            </div>
                        </div>

                        @if ($hasSignature)
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="save_signature"
                                    id="saveSignature" value="1">
                                <label class="form-check-label" for="saveSignature">
                                    <i class="fas fa-save me-1"></i> Save this signature to my profile
                                </label>
                            </div>

                            <div id="passwordSection" style="display: none;" class="mb-3">
                                <label class="form-label">Current Password *</label>
                                <input type="password" name="current_password" class="form-control"
                                    placeholder="Enter your password to update signature">
                                <small class="text-muted">Required to update your saved signature</small>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Add any notes about this approval..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" style="background: var(--navy); border: none;">
                        <i class="fas fa-check me-1"></i> Approve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject VR Modal -->
<div class="modal fade" id="rejectVRModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle me-2"></i> Reject Voucher Request
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectVRForm">
                @csrf
                <input type="hidden" id="rejectVRId">
                <div class="modal-body">
                    <div class="alert alert-danger mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Are you sure you want to reject this VR?
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rejection Reason *</label>
                        <textarea name="rejection_reason" class="form-control" rows="3"
                            placeholder="Please provide a clear reason for rejection..." required></textarea>
                        <small class="text-muted">This will be visible to the VR creator</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Additional Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Any additional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" style="background: var(--danger); border: none;">
                        <i class="fas fa-times me-1"></i> Reject VR
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Mark Paid Modal -->
<div class="modal fade" id="markPaidModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-check-double me-2"></i> Mark as Paid
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="markPaidForm">
                @csrf
                <input type="hidden" id="markPaidVRId">
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        This will mark the VR as paid. Make sure payment has been processed.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="3"
                            placeholder="Payment details, reference number, transaction date, etc..."></textarea>
                        <small class="text-muted">This information will be stored with the VR record</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background: var(--navy); border: none;">
                        <i class="fas fa-check-double me-1"></i> Mark as Paid
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
    <style>
        /* ============================================
                VR INDEX STYLES - Inline to prevent FOUT
            ============================================ */
        :root {
            --navy: #003366;
            --orange: #ff6600;
            --navy-light: #1e4a7a;
            --orange-light: #ff8533;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --dark: #343a40;
            --light: #f8f9fa;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 25px;
        }

        .page-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
            font-size: 1.75rem;
        }

        .page-subtitle {
            color: #666;
            font-size: 14px;
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .stats-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 16px 12px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-color: var(--navy);
        }

        .stats-card.active {
            border-color: var(--navy);
            background: linear-gradient(135deg, #f8f9ff, #fff);
            box-shadow: 0 4px 12px rgba(0, 51, 102, 0.15);
        }

        .stats-card.active .stats-icon i {
            color: var(--navy);
        }

        .stats-card.active .stats-number {
            color: var(--navy);
        }

        .stats-icon {
            font-size: 28px;
            margin-bottom: 8px;
        }

        .stats-icon i {
            color: #888;
        }

        .stats-number {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 4px;
            color: #333;
        }

        .stats-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 500;
        }

        /* Create Button */
        .btn-create {
            background: linear-gradient(135deg, var(--orange), #ff8533);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 10px rgba(255, 102, 0, 0.2);
        }

        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(255, 102, 0, 0.3);
        }

        /* VR Card */
        .vr-list-container {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .vr-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .vr-card:hover {
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
            border-color: var(--orange-light);
        }

        .vr-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f0f0f0;
        }

        .vr-header-left {
            flex: 1;
        }

        .vr-number {
            font-weight: 700;
            color: var(--orange);
            font-size: 16px;
            margin-bottom: 6px;
        }

        .vr-ticket-info {
            font-size: 13px;
            color: #666;
            margin-bottom: 4px;
        }

        .vr-ticket-info i {
            margin-right: 4px;
            color: var(--navy);
        }

        .vr-ticket-title {
            color: #888;
            margin-left: 6px;
        }

        .vr-date {
            font-size: 12px;
            color: #999;
        }

        .vr-date i {
            margin-right: 4px;
        }

        .vr-header-right {
            text-align: right;
        }

        .vr-status {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-admin_approved {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .status-om_approved {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-gm_approved {
            background: #c3e6cb;
            color: #155724;
            border: 1px solid #b1dfbb;
        }

        .status-paid {
            background: #28a745;
            color: white;
            border: none;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .badge-my-vr {
            display: inline-block;
            margin-top: 6px;
            padding: 2px 8px;
            background: #e9ecef;
            color: #495057;
            font-size: 10px;
            border-radius: 12px;
        }

        .vr-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 16px;
        }

        .info-item {
            font-size: 13px;
        }

        .info-label {
            color: #888;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .info-value {
            color: #333;
            font-weight: 500;
        }

        .amount-value {
            font-size: 18px;
            font-weight: 700;
            color: var(--navy);
        }

        .vr-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            padding-top: 12px;
            border-top: 1px solid #f0f0f0;
        }

        .btn-vr {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-vr:hover {
            transform: translateY(-1px);
            filter: brightness(0.95);
        }

        .btn-view {
            background: #6c757d;
            color: white;
        }

        .btn-approve {
            background: var(--success);
            color: white;
        }

        .btn-reject {
            background: var(--danger);
            color: white;
        }

        .btn-paid {
            background: var(--info);
            color: white;
        }

        .btn-delete {
            background: var(--danger);
            color: white;
        }

        .btn-print {
            background: var(--dark);
            color: white;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 12px;
            border: 1px solid #e0e0e0;
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            color: #ddd;
        }

        .empty-state h4 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }

        .empty-state p {
            font-size: 14px;
            margin-bottom: 20px;
            color: #666;
        }

        /* Pagination */
        .pagination-container {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        /* Modal Styles */
        .modal-header {
            background: var(--navy) !important;
            color: white !important;
            border-bottom: none;
        }

        .modal-header .modal-title {
            color: white !important;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }

        .modal-vr-body {
            max-height: 70vh;
            overflow-y: auto;
            padding: 20px;
        }

        .signature-canvas-container {
            text-align: center;
        }

        .signature-canvas {
            border: 2px dashed #ddd;
            border-radius: 8px;
            background: transparent;
            cursor: crosshair;
            touch-action: none;
        }

        .signature-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 10px;
        }

        .signature-option-card {
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            height: 100%;
        }

        .signature-option-card:hover {
            border-color: var(--navy);
            background: #f8f9ff;
        }

        .signature-option-card input[type="radio"] {
            display: none;
        }

        .signature-option-card .form-check-label {
            cursor: pointer;
            display: block;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .vr-info {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .vr-actions {
                flex-wrap: wrap;
            }

            .btn-vr {
                flex: 1;
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }

            .vr-header {
                flex-direction: column;
            }

            .vr-header-right {
                text-align: left;
            }

            .vr-info {
                grid-template-columns: 1fr;
            }

            .btn-vr {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

    <script>
        // Global variables
        let signaturePad = null;
        let currentVRId = null;
        let pendingSignatureAction = null;

        // Toastr configuration
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "4000",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        $(document).ready(function() {
            // ============================================
            // FILTER CARDS - Click to filter VR list
            // ============================================
            $('.stats-card').on('click', function() {
                const filter = $(this).data('filter');
                const url = new URL(window.location.href);
                url.searchParams.set('filter', filter);
                window.location.href = url.toString();
            });

            // ============================================
            // VR CARD CLICK - View details
            // ============================================
            $('.vr-card').on('click', function(e) {
                // Don't trigger if clicked on action buttons
                if ($(e.target).closest('.btn-vr').length) return;
                const vrId = $(this).data('vr-id');
                if (vrId) showVRModal(vrId);
            });

            // ============================================
            // SIGNATURE PAD INITIALIZATION
            // ============================================
            $('#approveVRModal').on('shown.bs.modal', function() {
                const canvas = document.getElementById('approveSignatureCanvas');
                if (canvas) {
                    // Set explicit dimensions 300x200
                    canvas.width = 300;
                    canvas.height = 200;

                    signaturePad = new SignaturePad(canvas, {
                        backgroundColor: 'rgba(0,0,0,0)',
                        penColor: 'rgb(0, 0, 0)',
                        minWidth: 0.5,
                        maxWidth: 2.5,
                        throttle: 16
                    });
                }
            });

            // Clean up signature pad when modal closes
            $('#approveVRModal').on('hidden.bs.modal', function() {
                if (signaturePad) {
                    signaturePad.clear();
                    signaturePad = null;
                }
            });

            // ============================================
            // SIGNATURE OPTION TOGGLE
            // ============================================
            $('input[name="signature_option"]').on('change', function() {
                if ($(this).val() === 'new') {
                    $('#newSignatureSection').show();
                    $('#passwordSection').show();
                } else {
                    $('#newSignatureSection').hide();
                    $('#passwordSection').hide();
                }
            });

            $('#saveSignature').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#passwordSection').show();
                } else {
                    $('#passwordSection').hide();
                }
            });

            // ============================================
            // FORM SUBMISSION HANDLERS
            // ============================================

            // Approve Form
            $('#approveVRForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const formData = new FormData(form[0]);

                // If using new signature, validate
                if ($('#createNew').is(':checked')) {
                    if (!signaturePad || signaturePad.isEmpty()) {
                        toastr.error('Please draw your signature');
                        return;
                    }
                    formData.append('signature_data', signaturePad.toDataURL());
                } else if ($('#useSaved').is(':checked')) {
                    formData.append('use_saved_signature', '1');
                }

                const submitBtn = form.find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html(
                '<i class="fas fa-spinner fa-spin"></i> Approving...');

                $.ajax({
                    url: '{{ route('vouchers.approve', ':id') }}'.replace(':id', currentVRId),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#approveVRModal').modal('hide');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message || 'Failed to approve VR';
                        toastr.error(message);
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Reject Form
            $('#rejectVRForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const formData = form.serialize();
                const submitBtn = form.find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html(
                '<i class="fas fa-spinner fa-spin"></i> Rejecting...');

                $.ajax({
                    url: '{{ route('vouchers.reject', ':id') }}'.replace(':id', currentVRId),
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#rejectVRModal').modal('hide');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Failed to reject VR');
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Mark Paid Form
            $('#markPaidForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const formData = form.serialize();
                const submitBtn = form.find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Processing...');

                $.ajax({
                    url: '{{ route('vouchers.mark-paid', ':id') }}'.replace(':id', currentVRId),
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#markPaidModal').modal('hide');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Failed to mark as paid');
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });
        });

        // ============================================
        // MODAL FUNCTIONS
        // ============================================

        function openCreateVRModal() {
            $('#createVRModal').modal('show');
            $('#createVRModalBody').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Loading VR form...</p>
                </div>
            `);

            $.ajax({
                url: '{{ route('vouchers.create-modal', 'ticket-select') }}',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#createVRModalBody').html(response.html);
                        initTicketSelect2();
                    } else {
                        $('#createVRModalBody').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> ${response.message}
                            </div>
                        `);
                    }
                },
                error: function() {
                    $('#createVRModalBody').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> Failed to load form. Please try again.
                        </div>
                    `);
                }
            });
        }

        function initTicketSelect2() {
            $('#ticketSelect').select2({
                theme: 'bootstrap',
                width: '100%',
                placeholder: 'Search tickets by number or title...',
                allowClear: true,
                minimumInputLength: 1,
                ajax: {
                    url: '{{ route('vouchers.search-tickets') }}',
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return {
                            search: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        return {
                            results: data.results,
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    }
                }
            });
        }

        function continueToVRForm() {
            const selectedTicket = $('#ticketSelect').select2('data')[0];
            if (!selectedTicket || !selectedTicket.ticket_id) {
                toastr.error('Please select a ticket first');
                return;
            }

            $('#createVRModalBody').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Loading VR form...</p>
                </div>
            `);

            $.ajax({
                url: '{{ route('vouchers.create-modal', ':id') }}'.replace(':id', selectedTicket.ticket_id),
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#createVRModalBody').html(response.html);
                    } else {
                        $('#createVRModalBody').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> ${response.message}
                            </div>
                            <button class="btn btn-secondary mt-3" onclick="openCreateVRModal()">
                                <i class="fas fa-arrow-left"></i> Back
                            </button>
                        `);
                    }
                },
                error: function() {
                    $('#createVRModalBody').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> Failed to load VR form.
                        </div>
                        <button class="btn btn-secondary mt-3" onclick="openCreateVRModal()">
                            <i class="fas fa-arrow-left"></i> Back
                        </button>
                    `);
                }
            });
        }

        function showVRModal(vrId) {
            $('#viewVRModalBody').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Loading VR details...</p>
                </div>
            `);
            $('#viewVRModal').modal('show');

            $.ajax({
                url: '{{ route('vouchers.show-modal', ':id') }}'.replace(':id', vrId),
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#viewVRModalBody').html(response.html);
                    } else {
                        $('#viewVRModalBody').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> ${response.message}
                            </div>
                        `);
                    }
                },
                error: function() {
                    $('#viewVRModalBody').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> Failed to load VR details
                        </div>
                    `);
                }
            });
        }

        function openApproveVRModal(vrId) {
            currentVRId = vrId;
            $('#approveVRId').val(vrId);
            $('#approveVRForm')[0].reset();
            $('#newSignatureSection').hide();
            $('#useSaved').prop('checked', true);
            $('#approveVRModal').modal('show');
        }

        function openRejectVRModal(vrId) {
            currentVRId = vrId;
            $('#rejectVRId').val(vrId);
            $('#rejectVRForm')[0].reset();
            $('#rejectVRModal').modal('show');
        }

        function openMarkPaidModal(vrId) {
            currentVRId = vrId;
            $('#markPaidVRId').val(vrId);
            $('#markPaidForm')[0].reset();
            $('#markPaidModal').modal('show');
        }

        function deleteVR(vrId) {
            Swal.fire({
                title: 'Delete Voucher Request?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    @if (auth()->user()->role === 'superadmin')
                        Swal.fire({
                            title: 'Password Verification',
                            input: 'password',
                            inputLabel: 'Enter your password to confirm deletion',
                            inputPlaceholder: 'Your account password',
                            showCancelButton: true,
                            confirmButtonText: 'Delete',
                            cancelButtonText: 'Cancel',
                            preConfirm: (password) => {
                                if (!password) {
                                    Swal.showValidationMessage('Password is required');
                                    return false;
                                }
                            }
                        }).then((passwordResult) => {
                            if (passwordResult.isConfirmed) {
                                deleteVRRequest(vrId, passwordResult.value);
                            }
                        });
                    @else
                        deleteVRRequest(vrId);
                    @endif
                }
            });
        }

        function deleteVRRequest(vrId, password = null) {
            const data = {
                _token: '{{ csrf_token() }}'
            };
            if (password) data.password = password;

            $.ajax({
                url: '{{ route('vouchers.destroy', ':id') }}'.replace(':id', vrId),
                type: 'DELETE',
                data: data,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $(`.vr-card[data-vr-id="${vrId}"]`).fadeOut(300, function() {
                            $(this).remove();
                            if ($('.vr-card').length === 0) {
                                location.reload();
                            }
                        });
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Failed to delete VR');
                }
            });
        }

        function printVR(vrId) {
            // Dummy print - akan diimplementasikan nanti
            Swal.fire({
                title: 'Print Report',
                text: 'Print feature will be available soon.',
                icon: 'info',
                confirmButtonText: 'OK'
            });
        }

        // Signature functions
        function clearSignature() {
            if (signaturePad) signaturePad.clear();
        }

        function undoSignature() {
            if (signaturePad) {
                const data = signaturePad.toData();
                if (data.length > 0) {
                    data.pop();
                    signaturePad.fromData(data);
                }
            }
        }

        function toggleSearchSection(type) {
            $('.search-section').removeClass('active');
            $(`#${type}SearchSection`).addClass('active');
        }

        function searchManualTicket() {
            const ticketNumber = $('#manualTicketNumber').val().trim();
            if (!ticketNumber) {
                toastr.error('Please enter a ticket number');
                return;
            }

            $('#createVRModalBody').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Searching for ticket...</p>
                </div>
            `);

            $.ajax({
                url: '{{ route('vouchers.find-ticket', ':number') }}'.replace(':number', ticketNumber),
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        $.ajax({
                            url: '{{ route('vouchers.create-modal', ':id') }}'.replace(':id', response
                                .ticket_id),
                            type: 'GET',
                            success: function(formResponse) {
                                if (formResponse.success) {
                                    $('#createVRModalBody').html(formResponse.html);
                                } else {
                                    showCreateFormError(formResponse.message);
                                }
                            },
                            error: () => showCreateFormError('Failed to load VR form')
                        });
                    } else {
                        showCreateFormError(response.message);
                    }
                },
                error: () => showCreateFormError('Failed to find ticket')
            });
        }

        function showCreateFormError(message) {
            $('#createVRModalBody').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> ${message}
                </div>
                <button class="btn btn-secondary mt-3" onclick="openCreateVRModal()">
                    <i class="fas fa-arrow-left"></i> Back to Ticket Selection
                </button>
            `);
        }

        function addItemRow() {
            const rowCount = $('.item-row').length;
            const newRow = `
                <tr class="item-row">
                    <td><input type="text" name="items[${rowCount}][item_name]" class="form-control form-control-sm" required>                <tr class="item-row">
                    <td><input type="text" name="items[${rowCount}][item_name]" class="form-control form-control-sm" required></td>
                    <td><input type="number" name="items[${rowCount}][qty]" class="form-control form-control-sm" min="1" value="1" required></td>
                    <td><input type="number" name="items[${rowCount}][unit_price]" class="form-control form-control-sm" min="0" step="1000" required></td>
                    <td><input type="text" name="items[${rowCount}][vendor]" class="form-control form-control-sm"></td>
                    <td><input type="text" name="items[${rowCount}][description]" class="form-control form-control-sm"></td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="removeItemRow(this)"><i class="fas fa-times"></i></button></td>
                </tr>
            `;
            $('#itemsTable tbody').append(newRow);
            calculateTotal();
        }

        function removeItemRow(button) {
            if ($('.item-row').length > 1) {
                $(button).closest('tr').remove();
                calculateTotal();
            } else {
                toastr.error('At least one item is required');
            }
        }

        function calculateTotal() {
            let total = 0;
            $('.item-row').each(function() {
                const qty = $(this).find('input[name*="[qty]"]').val() || 0;
                const price = $(this).find('input[name*="[unit_price]"]').val() || 0;
                total += parseFloat(qty) * parseFloat(price);
            });
            $('#totalAmount').text('Rp ' + total.toLocaleString('id-ID'));
        }

        $(document).on('input', 'input[name*="[qty]"], input[name*="[unit_price]"]', function() {
            calculateTotal();
        });
    </script>
@endpush
