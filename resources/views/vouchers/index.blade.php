@extends('layouts.main')

@section('title', 'Purchase Requests | ' . config('app.name'))

@section('page-title', 'Purchase Requests')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Purchase Requests', 'url' => 'javascript:void(0)'],
        ];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <style>
        /* Reset & Base Styles - Load FIRST to prevent FOUT */
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
            font-size: 24px;
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
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .stats-card.active {
            border-color: #ff6200;
            background: #fff8f0;
        }

        .stats-icon {
            font-size: 24px;
            margin-bottom: 8px;
        }

        .stats-number {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stats-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Create Button */
        .btn-create {
            background: linear-gradient(135deg, #ff6200, #ff7b00);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(255, 98, 0, 0.2);
        }

        .btn-create:hover {
            background: linear-gradient(135deg, #ff7b00, #ff6200);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(255, 98, 0, 0.25);
        }

        /* PR Card - Compact Design */
        .pr-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }

        .pr-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-1px);
            border-color: #ff6200;
        }

        .pr-card.active {
            border-left: 4px solid #ff6200;
            background: #fffaf5;
        }

        .pr-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 12px;
        }

        .pr-number {
            font-weight: 700;
            color: #ff6200;
            font-size: 14px;
        }

        .pr-status {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-admin_approved {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-om_approved {
            background: #d4edda;
            color: #155724;
        }

        .status-gm_approved {
            background: #c3e6cb;
            color: #155724;
        }

        .status-paid {
            background: #28a745;
            color: white;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .pr-body {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 10px;
        }

        .pr-info {
            flex: 1;
            min-width: 150px;
        }

        .pr-info-item {
            margin-bottom: 5px;
            font-size: 12px;
        }

        .pr-info-label {
            color: #999;
            font-size: 10px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .pr-info-value {
            color: #333;
            font-weight: 500;
        }

        .pr-photos-preview {
            display: flex;
            gap: 5px;
            margin-top: 8px;
        }

        .pr-photo-thumb {
            width: 35px;
            height: 35px;
            border-radius: 4px;
            object-fit: cover;
            border: 1px solid #ddd;
        }

        .pr-footer {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            padding-top: 10px;
            border-top: 1px solid #f0f0f0;
        }

        .btn-pr-action {
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 11px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            background: #f0f0f0;
            color: #666;
        }

        .btn-pr-action:hover {
            transform: translateY(-1px);
        }

        .btn-approve {
            background: #28a745;
            color: white;
        }

        .btn-approve:hover {
            background: #218838;
        }

        .btn-reject {
            background: #dc3545;
            color: white;
        }

        .btn-reject:hover {
            background: #c82333;
        }

        .btn-paid {
            background: #17a2b8;
            color: white;
        }

        .btn-paid:hover {
            background: #138496;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background: #c82333;
        }

        .btn-view {
            background: #6c757d;
            color: white;
        }

        .btn-view:hover {
            background: #5a6268;
        }

        .btn-print {
            background: #ff6200;
            color: white;
        }

        .btn-print:hover {
            background: #e55a00;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 10px;
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

        /* Loading Spinner */
        .loading-spinner {
            text-align: center;
            padding: 40px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .stats-number {
                font-size: 22px;
            }

            .pr-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .pr-body {
                flex-direction: column;
            }

            .pr-footer {
                flex-wrap: wrap;
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .stats-container {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 20px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h2 class="page-title">Purchase Requests (PR)</h2>
        <p class="page-subtitle">Manage all purchase requests for maintenance tickets</p>
    </div>

    <!-- Stats Cards - Clickable Filters -->
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
    @endphp

    <div class="stats-container">
        <div class="stats-card" data-filter="all">
            <div class="stats-icon"><i class="fas fa-file-invoice-dollar text-primary"></i></div>
            <div class="stats-number">{{ $stats['all'] }}</div>
            <div class="stats-label">All PR</div>
        </div>
        <div class="stats-card" data-filter="pending">
            <div class="stats-icon"><i class="fas fa-clock text-warning"></i></div>
            <div class="stats-number">{{ $stats['pending'] }}</div>
            <div class="stats-label">Pending Admin</div>
        </div>
        <div class="stats-card" data-filter="admin_approved">
            <div class="stats-icon"><i class="fas fa-user-cog text-info"></i></div>
            <div class="stats-number">{{ $stats['admin_approved'] }}</div>
            <div class="stats-label">Admin Approved</div>
        </div>
        <div class="stats-card" data-filter="om_approved">
            <div class="stats-icon"><i class="fas fa-user-tie text-primary"></i></div>
            <div class="stats-number">{{ $stats['om_approved'] }}</div>
            <div class="stats-label">OM Approved</div>
        </div>
        <div class="stats-card" data-filter="gm_approved">
            <div class="stats-icon"><i class="fas fa-user-shield text-success"></i></div>
            <div class="stats-number">{{ $stats['gm_approved'] }}</div>
            <div class="stats-label">GM Approved</div>
        </div>
        <div class="stats-card" data-filter="paid">
            <div class="stats-icon"><i class="fas fa-check-circle text-success"></i></div>
            <div class="stats-number">{{ $stats['paid'] }}</div>
            <div class="stats-label">Paid</div>
        </div>
        <div class="stats-card" data-filter="rejected">
            <div class="stats-icon"><i class="fas fa-times-circle text-danger"></i></div>
            <div class="stats-number">{{ $stats['rejected'] }}</div>
            <div class="stats-label">Rejected</div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <!-- Create PR Button -->
            @if (auth()->user()->role === 'admin_eng')
                <button class="btn-create" onclick="openCreatePRModal()">
                    <i class="fas fa-plus"></i> Create New Purchase Request
                </button>
            @endif

            <!-- PR List Container -->
            <div id="pr-list-container">
                <div class="loading-spinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading Purchase Requests...</p>
                </div>
            </div>

            <!-- Pagination -->
            <div id="pagination-container" class="mt-3" style="display: none;"></div>
        </div>
    </div>
@endsection

<!-- ============================================
    MODALS - CSS inline to prevent FOUT
============================================ -->

<!-- Create PR Modal -->
<div class="modal fade" id="createPRModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: #003366; color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-file-invoice-dollar me-2"></i> Create Purchase Request
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="createPRModalBody" style="padding: 20px;">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Loading form...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View PR Modal -->
<div class="modal fade" id="viewPRModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: #003366; color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i> Purchase Request Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewPRModalBody" style="padding: 20px; max-height: 70vh; overflow-y: auto;">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Loading details...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve PR Modal -->
<div class="modal fade" id="approvePRModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header" style="background: #28a745; color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i> Approve Purchase Request
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <div id="approvePRContent">
                    <div class="text-center py-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject PR Modal -->
<div class="modal fade" id="rejectPRModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header" style="background: #dc3545; color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle me-2"></i> Reject Purchase Request
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <form id="rejectPRForm">
                    @csrf
                    <input type="hidden" id="rejectPRId">
                    <div class="alert alert-danger mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Are you sure you want to reject this Purchase Request?
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="3"
                            placeholder="Please provide a clear reason for rejection..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Additional Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Any additional notes..."></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject PR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Mark Paid Modal -->
<div class="modal fade" id="markPaidModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header" style="background: #17a2b8; color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-check-double me-2"></i> Mark as Paid
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <form id="markPaidForm">
                    @csrf
                    <input type="hidden" id="markPaidPRId">
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        This will mark the Purchase Request as paid. Make sure payment has been processed.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="3"
                            placeholder="Payment details, reference number, transaction date, etc..."></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Mark as Paid</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<form id="approvePRForm">
    @csrf
    <input type="hidden" id="approvePRId" value="{{ $vr->id }}">

    @php
        $hasSignature = isset($hasSignature)
            ? $hasSignature
            : !empty(auth()->user()->signature_path) && Storage::disk('public')->exists(auth()->user()->signature_path);
    @endphp

    @if ($hasSignature)
        <div class="alert alert-info mb-3">
            <i class="fas fa-info-circle me-2"></i>
            You have a saved signature. Choose your preferred signing method.
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <input class="form-check-input" type="radio" name="signature_option" id="useSaved"
                            value="saved" checked>
                        <label class="form-check-label d-block" for="useSaved">
                            <i class="fas fa-save fa-3x text-primary mb-3"></i>
                            <h6>Use Saved Signature</h6>
                            <p class="small text-muted">Quick approve with your existing signature</p>
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <input class="form-check-input" type="radio" name="signature_option" id="createNew"
                            value="new">
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

    <!-- New Signature Section -->
    <div id="newSignatureSection" style="{{ $hasSignature ? 'display: none;' : 'display: block;' }}">
        <div class="mb-3">
            <label class="form-label">Draw Your Signature <span class="text-danger">*</span></label>
            <div class="border rounded p-2 bg-white">
                <canvas id="approveSignatureCanvas" class="signature-canvas"
                    style="width: 100%; height: 150px; border: 1px solid #ddd;"></canvas>
            </div>
            <div class="mt-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSignature()">
                    <i class="fas fa-eraser me-1"></i> Clear
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="undoSignature()">
                    <i class="fas fa-undo me-1"></i> Undo
                </button>
            </div>
        </div>

        @if ($hasSignature)
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="save_signature" id="saveSignature"
                    value="1">
                <label class="form-check-label" for="saveSignature">
                    <i class="fas fa-save me-1"></i> Save this signature to my profile
                </label>
            </div>

            <div id="passwordSection" style="display: none;" class="mb-3">
                <label class="form-label">Current Password <span class="text-danger">*</span></label>
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

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success">
            <i class="fas fa-check me-1"></i> Approve
        </button>
    </div>
</form>

<style>
    .signature-canvas {
        touch-action: none;
        background: white;
    }
</style>

@push('scripts')
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

    <script>
        // Toastr configuration
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "4000"
        };

        let currentFilter = 'all';
        let currentPage = 1;
        let signaturePad = null;

        $(document).ready(function() {
            loadPRList();

            // Filter click handlers
            $('.stats-card').on('click', function() {
                $('.stats-card').removeClass('active');
                $(this).addClass('active');
                currentFilter = $(this).data('filter');
                currentPage = 1;
                loadPRList();
            });
        });

        // ============================================
        // LOAD PR LIST WITH AJAX
        // ============================================
        function loadPRList() {
            $('#pr-list-container').html(`
                <div class="loading-spinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading Purchase Requests...</p>
                </div>
            `);

            $.ajax({
                url: '{{ route('vouchers.list') }}',
                type: 'GET',
                data: {
                    filter: currentFilter,
                    page: currentPage
                },
                success: function(response) {
                    if (response.success) {
                        renderPRList(response.data);
                        renderPagination(response.pagination);
                    } else {
                        $('#pr-list-container').html(`
                            <div class="empty-state">
                                <i class="fas fa-file-invoice-dollar"></i>
                                <h4>No Purchase Requests Found</h4>
                                <p>${response.message || 'There are no purchase requests matching your criteria.'}</p>
                            </div>
                        `);
                        $('#pagination-container').hide();
                    }
                },
                error: function() {
                    $('#pr-list-container').html(`
                        <div class="empty-state">
                            <i class="fas fa-exclamation-triangle"></i>
                            <h4>Error Loading Data</h4>
                            <p>Failed to load purchase requests. Please try again.</p>
                        </div>
                    `);
                    $('#pagination-container').hide();
                }
            });
        }

        function renderPRList(vrs) {
            if (!vrs || vrs.length === 0) {
                $('#pr-list-container').html(`
                    <div class="empty-state">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <h4>No Purchase Requests Found</h4>
                        <p>There are no purchase requests matching your criteria.</p>
                    </div>
                `);
                return;
            }

            let html = '';
            const userRole = '{{ auth()->user()->role }}';

            for (const vr of vrs) {
                const statusClass = getStatusClass(vr.status);
                const statusLabel = getStatusLabel(vr.status);
                const photoCount = vr.photo_count || 0;
                const firstPhoto = vr.first_photo || null;

                html += `
                    <div class="pr-card" data-pr-id="${vr.id}" data-pr-number="${vr.vr_number}">
                        <div class="pr-header">
                            <div class="pr-number">#${vr.vr_number}</div>
                            <div class="pr-status ${statusClass}">${statusLabel}</div>
                        </div>
                        <div class="pr-body">
                            <div class="pr-info">
                                <div class="pr-info-item">
                                    <div class="pr-info-label">Ticket</div>
                                    <div class="pr-info-value">#${vr.ticket_number} - ${escapeHtml(truncate(vr.ticket_title, 40))}</div>
                                </div>
                                <div class="pr-info-item">
                                    <div class="pr-info-label">Created By</div>
                                    <div class="pr-info-value">${escapeHtml(vr.created_by_name)}</div>
                                </div>
                                <div class="pr-info-item">
                                    <div class="pr-info-label">Created At</div>
                                    <div class="pr-info-value">${formatDate(vr.created_at)}</div>
                                </div>
                            </div>
                            <div class="pr-info">
                                <div class="pr-info-item">
                                    <div class="pr-info-label">Photos</div>
                                    <div class="pr-info-value">${photoCount} photo(s)</div>
                                </div>
                                ${firstPhoto ? `
                                            <div class="pr-photos-preview">
                                                <img src="${firstPhoto}" class="pr-photo-thumb" alt="Preview">
                                            </div>
                                            ` : ''}
                            </div>
                        </div>
                        <div class="pr-footer">
                            <button class="btn-pr-action btn-view" onclick="event.stopPropagation(); viewPR(${vr.id})">
                                <i class="fas fa-eye"></i> View
                            </button>
                            ${canApprove(vr, userRole) ? `
                                        <button class="btn-pr-action btn-approve" onclick="event.stopPropagation(); approvePR(${vr.id})">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                        ` : ''}
                            ${canReject(vr, userRole) ? `
                                        <button class="btn-pr-action btn-reject" onclick="event.stopPropagation(); rejectPR(${vr.id})">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                        ` : ''}
                            ${canMarkPaid(vr, userRole) ? `
                                        <button class="btn-pr-action btn-paid" onclick="event.stopPropagation(); markPaid(${vr.id})">
                                            <i class="fas fa-check-double"></i> Mark Paid
                                        </button>
                                        ` : ''}
                            ${canDelete(vr, userRole) ? `
                                        <button class="btn-pr-action btn-delete" onclick="event.stopPropagation(); deletePR(${vr.id})">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                        ` : ''}
                            <button class="btn-pr-action btn-print" onclick="event.stopPropagation(); printPR(${vr.id})">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </div>
                    </div>
                `;
            }

            $('#pr-list-container').html(html);

            // Add click handler to cards for filtering
            $('.pr-card').on('click', function(e) {
                if (!$(e.target).closest('.btn-pr-action').length) {
                    const prNumber = $(this).data('pr-number');
                    filterByPR(prNumber);
                }
            });
        }

        function renderPagination(pagination) {
            if (!pagination || pagination.total <= pagination.per_page) {
                $('#pagination-container').hide();
                return;
            }

            let html = '<nav><ul class="pagination justify-content-center">';

            // Previous button
            if (pagination.current_page > 1) {
                html +=
                    `<li class="page-item"><a class="page-link" href="#" onclick="goToPage(${pagination.current_page - 1})">Previous</a></li>`;
            } else {
                html += `<li class="page-item disabled"><span class="page-link">Previous</span></li>`;
            }

            // Page numbers
            const startPage = Math.max(1, pagination.current_page - 2);
            const endPage = Math.min(pagination.last_page, pagination.current_page + 2);

            for (let i = startPage; i <= endPage; i++) {
                if (i === pagination.current_page) {
                    html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
                } else {
                    html += `<li class="page-item"><a class="page-link" href="#" onclick="goToPage(${i})">${i}</a></li>`;
                }
            }

            // Next button
            if (pagination.current_page < pagination.last_page) {
                html +=
                    `<li class="page-item"><a class="page-link" href="#" onclick="goToPage(${pagination.current_page + 1})">Next</a></li>`;
            } else {
                html += `<li class="page-item disabled"><span class="page-link">Next</span></li>`;
            }

            html += '</ul></nav>';
            $('#pagination-container').html(html).show();
        }

        function goToPage(page) {
            currentPage = page;
            loadPRList();
            $('html, body').animate({
                scrollTop: 0
            }, 300);
        }

        function filterByPR(prNumber) {
            // Filter list to show only this PR (or highlight it)
            $('.pr-card').removeClass('active');
            $(`.pr-card[data-pr-number="${prNumber}"]`).addClass('active');

            // Scroll to the card
            $('html, body').animate({
                scrollTop: $(`.pr-card[data-pr-number="${prNumber}"]`).offset().top - 100
            }, 300);
        }

        // ============================================
        // MODAL FUNCTIONS
        // ============================================

        function openCreatePRModal() {
            $('#createPRModal').modal('show');
            $('#createPRModalBody').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Loading ticket selection...</p>
                </div>
            `);

            $.ajax({
                url: '{{ route('vouchers.create-modal', 'ticket-select') }}',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#createPRModalBody').html(response.html);
                        initTicketSelect2();
                    } else {
                        $('#createPRModalBody').html(`
                            <div class="alert alert-danger">${response.message}</div>
                        `);
                    }
                },
                error: function() {
                    $('#createPRModalBody').html(`
                        <div class="alert alert-danger">Failed to load form. Please try again.</div>
                    `);
                }
            });
        }

        function initTicketSelect2() {
            $('#ticketSelect').select2({
                theme: 'bootstrap-5',
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
                    processResults: function(data) {
                        return {
                            results: data.results,
                            pagination: {
                                more: data.pagination && data.pagination.more
                            }
                        };
                    },
                    cache: true
                },
                templateResult: function(ticket) {
                    if (ticket.loading) return ticket.text;
                    return $(
                        `<div><strong>#${ticket.ticket_number}</strong><br><small>${escapeHtml(ticket.title)}</small></div>`
                    );
                },
                templateSelection: function(ticket) {
                    if (!ticket.id) return ticket.text;
                    return `#${ticket.ticket_number} - ${escapeHtml(ticket.title)}`;
                }
            });
        }

        function continueToVRForm() {
            const selectedTicket = $('#ticketSelect').select2('data')[0];
            if (!selectedTicket || !selectedTicket.ticket_id) {
                toastr.error('Please select a ticket first');
                return;
            }

            $('#createPRModalBody').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Loading form...</p>
                </div>
            `);

            $.ajax({
                url: '{{ route('vouchers.create-modal', ':id') }}'.replace(':id', selectedTicket.ticket_id),
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#createPRModalBody').html(response.html);
                        initPhotoUpload();
                    } else {
                        $('#createPRModalBody').html(`
                            <div class="alert alert-danger">${response.message}</div>
                            <button class="btn btn-secondary" onclick="openCreatePRModal()">Back</button>
                        `);
                    }
                },
                error: function() {
                    $('#createPRModalBody').html(`
                        <div class="alert alert-danger">Failed to load form</div>
                        <button class="btn btn-secondary" onclick="openCreatePRModal()">Back</button>
                    `);
                }
            });
        }

        function initPhotoUpload() {
            // Preview selected photos
            $('#photosInput').on('change', function(e) {
                const files = e.target.files;
                const maxFiles = 5;
                const previewContainer = $('#photosPreview');
                previewContainer.empty();

                if (files.length > maxFiles) {
                    toastr.error(`Maximum ${maxFiles} photos allowed`);
                    $(this).val('');
                    return;
                }

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    if (!file.type.match('image.*')) {
                        toastr.error(`${file.name} is not an image`);
                        continue;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewContainer.append(`
                            <div class="photo-preview-item">
                                <img src="${e.target.result}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                <button type="button" class="btn-remove-photo" data-index="${i}" style="position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 12px;">×</button>
                            </div>
                        `);
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        function viewPR(vrId) {
            $('#viewPRModal').modal('show');
            $('#viewPRModalBody').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Loading details...</p>
                </div>
            `);

            $.ajax({
                url: '{{ route('vouchers.show-modal', ':id') }}'.replace(':id', vrId),
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#viewPRModalBody').html(response.html);
                    } else {
                        $('#viewPRModalBody').html(`<div class="alert alert-danger">${response.message}</div>`);
                    }
                },
                error: function() {
                    $('#viewPRModalBody').html(`<div class="alert alert-danger">Failed to load details</div>`);
                }
            });
        }

        function approvePR(vrId) {
            $('#approvePRModal').modal('show');
            $('#approvePRContent').html(`
                <div class="text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `);

            $.ajax({
                url: '{{ route('vouchers.modal.approve') }}',
                type: 'GET',
                data: {
                    vr_id: vrId
                },
                success: function(response) {
                    if (response.success) {
                        $('#approvePRContent').html(response.html);
                        initApproveSignaturePad();
                    } else {
                        $('#approvePRContent').html(
                            `<div class="alert alert-danger">${response.message}</div>`);
                    }
                },
                error: function() {
                    $('#approvePRContent').html(
                        `<div class="alert alert-danger">Failed to load approval form</div>`);
                }
            });
        }

        function rejectPR(vrId) {
            $('#rejectPRId').val(vrId);
            $('#rejectPRModal').modal('show');
        }

        function markPaid(vrId) {
            $('#markPaidPRId').val(vrId);
            $('#markPaidModal').modal('show');
        }

        function deletePR(vrId) {
            Swal.fire({
                title: 'Delete Purchase Request?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('vouchers.destroy', ':id') }}'.replace(':id', vrId),
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                loadPRList();
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Failed to delete PR');
                        }
                    });
                }
            });
        }

        function printPR(vrId) {
            window.open('{{ route('vouchers.print', ':id') }}'.replace(':id', vrId), '_blank');
        }

        function initApproveSignaturePad() {
            const canvas = document.getElementById('approveSignatureCanvas');
            if (canvas && !signaturePad) {
                canvas.width = canvas.offsetWidth;
                canvas.height = 150;
                signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)',
                    penColor: 'rgb(0, 0, 0)'
                });
            }

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

            $('#saveSignature').on('change', function() {
                $('#passwordSection').toggle($(this).is(':checked'));
            });
        }

        function clearSignature() {
            if (signaturePad) signaturePad.clear();
        }

        function undoSignature() {
            if (signaturePad) {
                const data = signaturePad.toData();
                if (data.length) data.pop();
                signaturePad.fromData(data);
            }
        }

        // Form submissions
        $(document).on('submit', '#createPRForm', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = $(this).find('button[type="submit"]');
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
                        $('#createPRModal').modal('hide');
                        setTimeout(() => loadPRList(), 1000);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Failed to create PR');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        $(document).on('submit', '#approvePRForm', function(e) {
            e.preventDefault();
            const vrId = $('#approvePRId').val();
            const formData = new FormData(this);
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();

            if ($('#createNew').is(':checked')) {
                if (!signaturePad || signaturePad.isEmpty()) {
                    toastr.error('Please draw your signature');
                    return;
                }
                formData.append('signature_data', signaturePad.toDataURL());
            } else if ($('#useSaved').is(':checked')) {
                formData.append('use_saved_signature', '1');
            }

            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Approving...');

            $.ajax({
                url: '{{ route('vouchers.approve', ':id') }}'.replace(':id', vrId),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#approvePRModal').modal('hide');
                        setTimeout(() => loadPRList(), 1000);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Failed to approve PR');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        $('#rejectPRForm').on('submit', function(e) {
            e.preventDefault();
            const vrId = $('#rejectPRId').val();
            const formData = $(this).serialize();
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();

            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Rejecting...');

            $.ajax({
                url: '{{ route('vouchers.reject', ':id') }}'.replace(':id', vrId),
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#rejectPRModal').modal('hide');
                        setTimeout(() => loadPRList(), 1000);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Failed to reject PR');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        $('#markPaidForm').on('submit', function(e) {
            e.preventDefault();
            const vrId = $('#markPaidPRId').val();
            const formData = $(this).serialize();
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();

            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

            $.ajax({
                url: '{{ route('vouchers.mark-paid', ':id') }}'.replace(':id', vrId),
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#markPaidModal').modal('hide');
                        setTimeout(() => loadPRList(), 1000);
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

        function getStatusClass(status) {
            const classes = {
                'pending': 'status-pending',
                'admin_approved': 'status-admin_approved',
                'om_approved': 'status-om_approved',
                'gm_approved': 'status-gm_approved',
                'paid': 'status-paid',
                'rejected': 'status-rejected'
            };
            return classes[status] || 'status-pending';
        }

        function getStatusLabel(status) {
            const labels = {
                'pending': 'Pending',
                'admin_approved': 'Admin Approved',
                'om_approved': 'OM Approved',
                'gm_approved': 'GM Approved',
                'paid': 'Paid',
                'rejected': 'Rejected'
            };
            return labels[status] || status;
        }

        function canApprove(vr, userRole) {
            if (userRole === 'admin_eng' && vr.status === 'pending') return true;
            if (userRole === 'om' && vr.status === 'admin_approved') return true;
            if (userRole === 'gm' && vr.status === 'om_approved') return true;
            return false;
        }

        function canReject(vr, userRole) {
            if (userRole === 'admin_eng' && vr.status === 'pending') return true;
            if (userRole === 'om' && vr.status === 'admin_approved') return true;
            if (userRole === 'gm' && vr.status === 'om_approved') return true;
            return false;
        }

        function canMarkPaid(vr, userRole) {
            return (userRole === 'admin_eng' || userRole === 'superadmin') && vr.status === 'gm_approved';
        }

        function canDelete(vr, userRole) {
            if (userRole === 'superadmin') return true;
            return (vr.created_by === '{{ auth()->id() }}' && ['pending', 'rejected'].includes(vr.status));
        }

        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function truncate(str, length) {
            if (!str) return '';
            return str.length > length ? str.substring(0, length) + '...' : str;
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
@endpush
