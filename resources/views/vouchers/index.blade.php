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
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">

    <style>
        /* Reset & Base Styles */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fb;
            color: #333;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 25px;
        }

        .page-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .page-subtitle {
            color: #666;
            font-size: 14px;
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .stats-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .stats-icon {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .stats-number {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stats-label {
            font-size: 13px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* VR Card */
        .vr-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }

        .vr-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
            border-color: #ff7b00;
        }

        .vr-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        .vr-number {
            font-weight: 700;
            color: #ff6200;
            font-size: 16px;
        }

        .vr-status {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
            border: 1px solid #218838;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .vr-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .info-item {
            font-size: 14px;
        }

        .info-label {
            color: #666;
            font-weight: 500;
            margin-bottom: 4px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            color: #333;
            font-weight: 500;
            line-height: 1.4;
        }

        .vr-actions {
            display: flex;
            gap: 8px;
            margin-top: 15px;
            flex-wrap: wrap;
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
        }

        .btn-vr {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-vr:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-view {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
        }

        .btn-view:hover {
            background: linear-gradient(135deg, #5a6268, #343a40);
        }

        .btn-approve {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .btn-approve:hover {
            background: linear-gradient(135deg, #218838, #1ba87e);
        }

        .btn-reject {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }

        .btn-reject:hover {
            background: linear-gradient(135deg, #bd2130, #a71e2a);
        }

        .btn-paid {
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: white;
        }

        .btn-paid:hover {
            background: linear-gradient(135deg, #138496, #117a8b);
        }

        .btn-delete {
            background: linear-gradient(135deg, #dc3545, #bd2130);
            color: white;
        }

        .btn-delete:hover {
            background: linear-gradient(135deg, #c82333, #a71e2a);
        }

        /* Create Button */
        .btn-create {
            background: linear-gradient(135deg, #ff6200, #ff7b00);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(255, 98, 0, 0.2);
        }

        .btn-create:hover {
            background: linear-gradient(135deg, #ff7b00, #ff6200);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(255, 98, 0, 0.25);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
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
            font-weight: 600;
        }

        .empty-state p {
            font-size: 14px;
            margin-bottom: 20px;
            color: #666;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Pagination */
        .pagination-container {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        /* Modal Styles */
        .modal-vr-body {
            max-height: 70vh;
            overflow-y: auto;
            padding: 20px;
        }

        .signature-canvas {
            border: 2px dashed #ddd;
            border-radius: 8px;
            background: white;
            cursor: crosshair;
            width: 100%;
            height: 150px;
            margin-bottom: 10px;
        }

        .signature-actions {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .vr-info {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .vr-actions {
                flex-direction: column;
            }

            .btn-vr {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .stats-container {
                grid-template-columns: 1fr;
            }

            .vr-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h2 class="page-title">Voucher Requests (VR)</h2>
        <p class="page-subtitle">Manage all voucher requests for maintenance tickets</p>
    </div>

    <!-- Quick Stats -->
    @php
        $user = auth()->user();

        // Calculate stats based on user role
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
    @endphp

    <div class="stats-container">
        <div class="stats-card">
            <div class="stats-icon">
                <i class="fas fa-file-invoice-dollar text-primary"></i>
            </div>
            <div class="stats-number">{{ $stats['all'] }}</div>
            <div class="stats-label">Total VR</div>
        </div>

        @if (in_array($user->role, ['admin_eng', 'om', 'gm']))
            <div class="stats-card">
                <div class="stats-icon">
                    <i class="fas fa-clock text-warning"></i>
                </div>
                <div class="stats-number">{{ $pendingMyApproval }}</div>
                <div class="stats-label">Pending My Approval</div>
            </div>
        @endif

        <div class="stats-card">
            <div class="stats-icon">
                <i class="fas fa-hourglass-half text-info"></i>
            </div>
            <div class="stats-number">{{ $stats['pending'] }}</div>
            <div class="stats-label">Pending</div>
        </div>

        <div class="stats-card">
            <div class="stats-icon">
                <i class="fas fa-check-circle text-success"></i>
            </div>
            <div class="stats-number">{{ $stats['paid'] + $stats['gm_approved'] }}</div>
            <div class="stats-label">Approved</div>
        </div>

        <div class="stats-card">
            <div class="stats-icon">
                <i class="fas fa-times-circle text-danger"></i>
            </div>
            <div class="stats-number">{{ $stats['rejected'] }}</div>
            <div class="stats-label">Rejected</div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <!-- Create VR Button (Admin Eng only) -->
            @if (auth()->user()->role === 'admin_eng')
                <button class="btn-create" onclick="openCreateVRModal()">
                    <i class="fas fa-plus"></i> Create New VR
                </button>
            @endif

            <!-- VR List -->
            <div id="vr-list">
                @forelse($vrs as $vr)
                    <div class="vr-card" id="vr-card-{{ $vr->id }}">
                        <div class="vr-header">
                            <div>
                                <div class="vr-number">#{{ $vr->vr_number }}</div>
                                <div class="small text-muted">
                                    Created: {{ $vr->created_at->format('d M Y, H:i') }}
                                    @if ($vr->created_by === auth()->id())
                                        <span class="badge bg-info ms-2">My VR</span>
                                    @endif
                                </div>
                            </div>
                            <div class="vr-status status-{{ $vr->status }}">
                                {{ str_replace('_', ' ', $vr->status) }}
                            </div>
                        </div>

                        <div class="vr-info">
                            <div class="info-item">
                                <div class="info-label">Ticket</div>
                                <div class="info-value">
                                    <strong>#{{ $vr->ticket->ticket_number }}</strong><br>
                                    {{ Str::limit($vr->ticket->title, 40) }}
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">Created By</div>
                                <div class="info-value">
                                    {{ $vr->creator->name }}<br>
                                    <small class="text-muted">{{ ucfirst($vr->creator->role) }}</small>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">Amount</div>
                                <div class="info-value">
                                    <strong>Rp {{ number_format($vr->total_amount, 0, ',', '.') }}</strong><br>
                                    <small class="text-muted">{{ $vr->items->count() }} items</small>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">Last Update</div>
                                <div class="info-value">
                                    {{ $vr->updated_at->format('d M Y, H:i') }}<br>
                                    <small class="text-muted">{{ $vr->updated_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="vr-actions">
                            <button class="btn-vr btn-view" onclick="showVRModal({{ $vr->id }})">
                                <i class="fas fa-eye"></i> View Details
                            </button>

                            <!-- Action buttons berdasarkan role dan status -->
                            @php
                                $user = auth()->user();
                                $canApprove = false;
                                $canReject = false;
                                $canMarkPaid = false;
                                $canDelete = false;

                                // Admin Eng: bisa approve/reject jika status pending
                                if ($user->role === 'admin_eng' && $vr->status === 'pending') {
                                    $canApprove = $canReject = true;
                                }
                                // OM: bisa approve/reject jika status admin_approved
                                elseif ($user->role === 'om' && $vr->status === 'admin_approved') {
                                    $canApprove = $canReject = true;
                                }
                                // GM: bisa approve/reject jika status om_approved
                                elseif ($user->role === 'gm' && $vr->status === 'om_approved') {
                                    $canApprove = $canReject = true;
                                }

                                // Admin Eng dan Superadmin: bisa mark as paid jika status gm_approved
                                if (
                                    in_array($user->role, ['admin_eng', 'superadmin']) &&
                                    $vr->status === 'gm_approved'
                                ) {
                                    $canMarkPaid = true;
                                }

                                // Delete permissions
                                if ($user->role === 'superadmin') {
                                    $canDelete = true;
                                } elseif (
                                    $vr->created_by === $user->id &&
                                    in_array($vr->status, ['pending', 'rejected'])
                                ) {
                                    $canDelete = true;
                                }
                            @endphp

                            @if ($canApprove)
                                <button class="btn-vr btn-approve" onclick="approveVR({{ $vr->id }})">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                            @endif

                            @if ($canReject)
                                <button class="btn-vr btn-reject" onclick="rejectVR({{ $vr->id }})">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            @endif

                            @if ($canMarkPaid)
                                <button class="btn-vr btn-paid" onclick="markPaid({{ $vr->id }})">
                                    <i class="fas fa-check-double"></i> Mark Paid
                                </button>
                            @endif

                            @if ($canDelete)
                                <button class="btn-vr btn-delete" onclick="deleteVR({{ $vr->id }})">
                                    <i class="fas fa-trash"></i> Delete
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
        </div>
    </div>
@endsection

<!-- MODALS SECTION -->
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

<!-- Approve Modal dengan Signature -->
<div class="modal fade" id="approveVRModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
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
                        $user = auth()->user();
                        $hasSignature =
                            !empty($user->signature_path) && Storage::disk('public')->exists($user->signature_path);
                    @endphp

                    @if ($hasSignature)
                        <!-- Signature Options -->
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            You have a saved signature. Choose your preferred signing method.
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <input class="form-check-input" type="radio" name="signature_option"
                                            id="useSaved" value="saved" checked>
                                        <label class="form-check-label d-block" for="useSaved">
                                            <i class="fas fa-save fa-3x text-primary mb-3"></i>
                                            <h6>Use Saved Signature</h6>
                                            <p class="small text-muted">Quick approve with your existing signature</p>
                                            @if ($hasSignature)
                                                <img src="{{ Storage::url($user->signature_path) }}"
                                                    alt="Saved Signature" class="img-fluid mt-2"
                                                    style="max-height: 50px;">
                                            @endif
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <input class="form-check-input" type="radio" name="signature_option"
                                            id="createNew" value="new">
                                        <label class="form-check-label d-block" for="createNew">
                                            <i class="fas fa-pen fa-3x text-warning mb-3"></i>
                                            <h6>Create New Signature</h6>
                                            <p class="small text-muted">Draw a new signature for this approval</p>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning mb-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            You don't have a saved signature. Please draw your signature below.
                        </div>
                    @endif

                    <!-- New Signature Canvas -->
                    <div id="newSignatureSection" style="{{ $hasSignature ? 'display: none;' : 'display: block;' }}">
                        <div class="mb-3">
                            <label class="form-label">Draw Your Signature *</label>
                            <div class="signature-canvas-container border rounded mb-3">
                                <canvas id="approveSignatureCanvas" class="signature-canvas"></canvas>
                            </div>
                            <div class="signature-actions">
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

                            <!-- Password verification jika ingin replace signature lama -->
                            <div id="passwordSection" style="display: none;" class="mb-3">
                                <label class="form-label">Current Password *</label>
                                <input type="password" name="current_password" class="form-control"
                                    placeholder="Enter your password to update signature">
                                <small class="text-muted">Required to update your saved signature</small>
                            </div>
                        @endif
                    </div>

                    <!-- Notes -->
                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Add any notes about this approval..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i> Approve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectVRModal" tabindex="-1">
    <div class="modal-dialog">
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-1"></i> Reject VR
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Mark Paid Modal -->
<div class="modal fade" id="markPaidModal" tabindex="-1">
    <div class="modal-dialog">
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check-double me-1"></i> Mark as Paid
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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
            // Initialize signature pad when modal opens
            $('#approveVRModal').on('shown.bs.modal', function() {
                const canvas = document.getElementById('approveSignatureCanvas');
                if (canvas) {
                    canvas.width = canvas.offsetWidth;
                    canvas.height = 150;

                    signaturePad = new SignaturePad(canvas, {
                        backgroundColor: 'rgb(255, 255, 255)',
                        penColor: 'rgb(0, 0, 0)',
                        minWidth: 0.5,
                        maxWidth: 2.5,
                        throttle: 16
                    });
                }
            });

            // Toggle signature options
            $('input[name="signature_option"]').on('change', function() {
                if ($(this).val() === 'new') {
                    $('#newSignatureSection').show();
                    $('#passwordSection').show();
                } else {
                    $('#newSignatureSection').hide();
                    $('#passwordSection').hide();
                }
            });

            // Toggle password field
            $('#saveSignature').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#passwordSection').show();
                } else {
                    $('#passwordSection').hide();
                }
            });
        });

        // ============================================
        // MODAL FUNCTIONS
        // ============================================

        // Open Create VR Modal
        function openCreateVRModal() {
            $('#createVRModal').modal('show');

            // Load ticket selection form
            $.ajax({
                url: '{{ route('vouchers.create-modal', 'ticket-select') }}',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#createVRModalBody').html(response.html);

                        // Initialize select2 for ticket search
                        initTicketSelect2();
                    } else {
                        $('#createVRModalBody').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> ${response.message}
                            </div>
                        `);
                    }
                },
                error: function(xhr) {
                    $('#createVRModalBody').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> Failed to load form. Please try again.
                        </div>
                    `);
                }
            });
        }

        // Initialize Select2 for ticket search
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
                        params.page = params.page || 1;
                        return {
                            results: data.results,
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                },
                templateResult: function(ticket) {
                    if (ticket.loading) return ticket.text;

                    return $(`
                        <div class="select2-result-ticket">
                            <div class="fw-bold">#${ticket.ticket_number}</div>
                            <div class="text-muted small mt-1">${ticket.title}</div>
                            <div class="small mt-1">
                                <span class="badge bg-info me-1">${ticket.category}</span>
                                <span class="badge bg-warning">${ticket.priority}</span>
                            </div>
                        </div>
                    `);
                },
                templateSelection: function(ticket) {
                    if (!ticket.id) return ticket.text;
                    return `#${ticket.ticket_number} - ${ticket.title}`;
                }
            });
        }

        // Continue to VR Form from ticket selection
        function continueToVRForm() {
            const selectedTicket = $('#ticketSelect').select2('data')[0];

            if (!selectedTicket || !selectedTicket.ticket_id) {
                toastr.error('Please select a ticket first');
                return;
            }

            // Show loading
            $('#createVRModalBody').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Loading VR form...</p>
                </div>
            `);

            // Load VR form for selected ticket
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
                            <button class="btn btn-secondary" onclick="openCreateVRModal()">
                                <i class="fas fa-arrow-left"></i> Back to Ticket Selection
                            </button>
                        `);
                    }
                },
                error: function(xhr) {
                    $('#createVRModalBody').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> Failed to load VR form. Please try again.
                        </div>
                        <button class="btn btn-secondary" onclick="openCreateVRModal()">
                            <i class="fas fa-arrow-left"></i> Back to Ticket Selection
                        </button>
                    `);
                }
            });
        }

        // Search manual ticket
        function searchManualTicket() {
            const ticketNumber = $('#manualTicketNumber').val().trim();

            if (!ticketNumber) {
                toastr.error('Please enter a ticket number');
                return;
            }

            // Show loading
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
                        // Load VR form for found ticket
                        $.ajax({
                            url: '{{ route('vouchers.create-modal', ':id') }}'.replace(':id', response
                                .ticket_id),
                            type: 'GET',
                            success: function(formResponse) {
                                if (formResponse.success) {
                                    $('#createVRModalBody').html(formResponse.html);
                                } else {
                                    $('#createVRModalBody').html(`
                                        <div class="alert alert-danger">
                                            <i class="fas fa-exclamation-triangle"></i> ${formResponse.message}
                                        </div>
                                        <button class="btn btn-secondary" onclick="openCreateVRModal()">
                                            <i class="fas fa-arrow-left"></i> Back to Ticket Selection
                                        </button>
                                    `);
                                }
                            },
                            error: function() {
                                $('#createVRModalBody').html(`
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-triangle"></i> Failed to load VR form
                                    </div>
                                    <button class="btn btn-secondary" onclick="openCreateVRModal()">
                                        <i class="fas fa-arrow-left"></i> Back to Ticket Selection
                                    </button>
                                `);
                            }
                        });
                    } else {
                        $('#createVRModalBody').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> ${response.message}
                            </div>
                            <button class="btn btn-secondary" onclick="openCreateVRModal()">
                                <i class="fas fa-arrow-left"></i> Back to Ticket Selection
                            </button>
                        `);
                    }
                },
                error: function() {
                    $('#createVRModalBody').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> Failed to find ticket
                        </div>
                        <button class="btn btn-secondary" onclick="openCreateVRModal()">
                            <i class="fas fa-arrow-left"></i> Back to Ticket Selection
                        </button>
                    `);
                }
            });
        }

        // Toggle search sections
        function toggleSearchSection(type) {
            $('.search-section').removeClass('active');
            $(`#${type}SearchSection`).addClass('active');
        }

        // Show VR Details Modal
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
                error: function(xhr) {
                    $('#viewVRModalBody').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> Failed to load VR details
                        </div>
                    `);
                }
            });
        }

        // Approve VR
        function approveVR(vrId) {
            currentVRId = vrId;
            $('#approveVRId').val(vrId);

            // Reset form
            $('#approveVRForm')[0].reset();
            if ($('#newSignatureSection').is(':visible')) {
                $('#newSignatureSection').hide();
                $('#useSaved').prop('checked', true);
            }

            $('#approveVRModal').modal('show');
        }

        // Reject VR
        function rejectVR(vrId) {
            currentVRId = vrId;
            $('#rejectVRId').val(vrId);
            $('#rejectVRForm')[0].reset();
            $('#rejectVRModal').modal('show');
        }

        // Mark as Paid
        function markPaid(vrId) {
            currentVRId = vrId;
            $('#markPaidVRId').val(vrId);
            $('#markPaidForm')[0].reset();
            $('#markPaidModal').modal('show');
        }

        // Delete VR
        function deleteVR(vrId) {
            currentVRId = vrId;

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
                    // If superadmin, ask for password
                    @if (auth()->user()->role === 'superadmin')
                        Swal.fire({
                            title: 'Password Verification',
                            input: 'password',
                            inputLabel: 'Enter your password to confirm deletion',
                            inputPlaceholder: 'Your account password',
                            showCancelButton: true,
                            confirmButtonText: 'Delete',
                            cancelButtonText: 'Cancel',
                            inputAttributes: {
                                maxlength: 50,
                                autocapitalize: 'off',
                                autocorrect: 'off'
                            },
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

            if (password) {
                data.password = password;
            }

            $.ajax({
                url: '{{ route('vouchers.destroy', ':id') }}'.replace(':id', vrId),
                type: 'DELETE',
                data: data,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#vr-card-' + vrId).fadeOut(300, function() {
                            $(this).remove();
                            if ($('#vr-list').children('.vr-card').length === 0) {
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

        // ============================================
        // FORM SUBMISSION HANDLERS
        // ============================================

        // Submit Create VR Form (event delegation)
        $(document).on('submit', '#createVRForm', function(e) {
            e.preventDefault();

            const form = $(this);
            const formData = new FormData(form[0]);
            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.html();

            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating...');

            $.ajax({
                url: '{{ route('vouchers.store') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#createVRModal').modal('hide');

                        // Reload page after 1.5 seconds
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        toastr.error(response.message);
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                },
                error: function(xhr) {
                    let message = 'Failed to create VR';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    // Show validation errors
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        message = '';
                        Object.values(xhr.responseJSON.errors).forEach(error => {
                            if (Array.isArray(error)) {
                                error.forEach(err => {
                                    message += err + '<br>';
                                    toastr.error(err);
                                });
                            }
                        });
                    }

                    if (!message.includes('<br>')) {
                        toastr.error(message);
                    }

                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Submit Approve Form
        $('#approveVRForm').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const formData = new FormData(form[0]);

            // Jika pakai signature baru, validasi signature ada
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

            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Approving...');

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
                    let message = 'Failed to approve VR';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    toastr.error(message);
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Submit Reject Form
        $('#rejectVRForm').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const formData = $(this).serialize();
            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.html();

            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Rejecting...');

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

        // Submit Mark Paid Form
        $('#markPaidForm').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const formData = $(this).serialize();
            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.html();

            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Marking as Paid...');

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

        // ============================================
        // HELPER FUNCTIONS
        // ============================================

        // Signature functions
        function clearSignature() {
            if (signaturePad) {
                signaturePad.clear();
            }
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

        // Add item row in create form
        function addItemRow() {
            const rowCount = $('.item-row').length;
            const newRow = `
                <tr class="item-row">
                    <td>
                        <input type="text" name="items[${rowCount}][item_name]" class="form-control form-control-sm" required>
                    </td>
                    <td>
                        <input type="number" name="items[${rowCount}][qty]" class="form-control form-control-sm" min="1" value="1" required>
                    </td>
                    <td>
                        <input type="number" name="items[${rowCount}][unit_price]" class="form-control form-control-sm" min="0" step="1000" required>
                    </td>
                    <td>
                        <input type="text" name="items[${rowCount}][vendor]" class="form-control form-control-sm">
                    </td>
                    <td>
                        <input type="text" name="items[${rowCount}][description]" class="form-control form-control-sm">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeItemRow(this)">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
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
                total += qty * price;
            });
            $('#totalAmount').text('Rp ' + total.toLocaleString('id-ID'));
        }

        // Auto-calculate total (event delegation)
        $(document).on('input', 'input[name*="[qty]"], input[name*="[unit_price]"]', function() {
            calculateTotal();
        });
    </script>
@endpush
