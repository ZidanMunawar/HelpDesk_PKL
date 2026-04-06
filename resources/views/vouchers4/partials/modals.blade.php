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
    <!-- Lightbox2 for photo gallery -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css">

    <style>
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

        /* Stats Cards - Solid Colors */
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
            border-left: 4px solid transparent;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 51, 102, 0.15);
        }

        .stats-card.total {
            border-left-color: var(--navy);
        }

        .stats-card.pending {
            border-left-color: var(--warning);
        }

        .stats-card.approved {
            border-left-color: var(--success);
        }

        .stats-card.rejected {
            border-left-color: var(--danger);
        }

        .stats-card.my-pending {
            border-left-color: var(--orange);
        }

        .stats-icon {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .stats-number {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
            color: var(--navy);
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
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .vr-card:hover {
            box-shadow: 0 4px 12px rgba(0, 51, 102, 0.15);
            transform: translateY(-2px);
            border-color: var(--orange);
        }

        .vr-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
        }

        .vr-number {
            font-weight: 700;
            color: var(--navy);
            font-size: 16px;
        }

        .vr-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: white;
        }

        .status-pending {
            background: var(--warning);
            color: #212529;
        }

        .status-admin_approved {
            background: var(--info);
        }

        .status-om_approved {
            background: var(--primary);
        }

        .status-gm_approved {
            background: var(--success);
        }

        .status-paid {
            background: #28a745;
        }

        .status-rejected {
            background: var(--danger);
        }

        .vr-body {
            padding: 20px;
        }

        .vr-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .info-item {
            font-size: 14px;
        }

        .info-label {
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .info-value {
            color: #333;
            font-weight: 500;
        }

        .info-value small {
            font-weight: normal;
            color: #666;
        }

        /* Photo Thumbnails */
        .photo-thumbnails {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .photo-thumb {
            width: 60px;
            height: 60px;
            border-radius: 6px;
            object-fit: cover;
            border: 2px solid #e0e0e0;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .photo-thumb:hover {
            transform: scale(1.1);
            border-color: var(--orange);
            box-shadow: 0 2px 8px rgba(0, 51, 102, 0.2);
        }

        .photo-count {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            background: #f0f0f0;
            border-radius: 20px;
            font-size: 12px;
            color: #666;
        }

        /* Action Buttons */
        .vr-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            padding: 15px 20px;
            background: #f8f9fa;
            border-top: 1px solid #e0e0e0;
        }

        .btn-vr {
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: white;
        }

        .btn-vr:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-view {
            background: var(--navy);
        }

        .btn-approve {
            background: var(--success);
        }

        .btn-reject {
            background: var(--danger);
        }

        .btn-paid {
            background: var(--info);
        }

        .btn-delete {
            background: #6c757d;
        }

        .btn-print {
            background: var(--dark);
        }

        /* Create Button */
        .btn-create {
            background: var(--navy);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            margin-bottom: 20px;
            border: 1px solid var(--navy);
        }

        .btn-create:hover {
            background: var(--navy-light);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 51, 102, 0.2);
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
            color: #ddd;
            margin-bottom: 20px;
        }

        .empty-state h4 {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #666;
            max-width: 400px;
            margin: 0 auto 20px;
        }

        /* Modal Styles */
        .modal-dialog {
            max-width: 500px;
            margin: 0.5rem auto;
        }

        @media (min-width: 576px) {
            .modal-dialog {
                margin: 1.75rem auto;
            }
        }

        .modal-content {
            border-radius: 10px;
            overflow: hidden;
        }

        .modal-header {
            background: var(--navy) !important;
            color: white !important;
            padding: 1rem 1.5rem;
        }

        .modal-header .modal-title {
            color: white !important;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }

        .modal-body {
            padding: 1.5rem;
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }

        /* Signature Canvas - 300x200 Transparent */
        .signature-canvas-container {
            display: flex;
            justify-content: center;
            background: transparent;
            padding: 10px;
        }

        .signature-canvas {
            width: 100%;
            max-width: 300px;
            height: auto;
            aspect-ratio: 300/200;
            border: 2px dashed #ddd;
            border-radius: 6px;
            cursor: crosshair;
            background: transparent;
        }

        .signature-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 10px;
        }

        /* Signature Options Cards */
        .signature-option-card {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .signature-option-card:hover {
            border-color: var(--orange);
        }

        .signature-option-card.selected {
            border-color: var(--navy);
            background: #f0f7ff;
        }

        .signature-option-card input[type="radio"] {
            display: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }

            .vr-info-grid {
                grid-template-columns: 1fr;
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
        <p class="text-muted">Manage photo-based voucher requests for maintenance tickets</p>
    </div>

    <!-- Quick Stats -->
    <div class="stats-container">
        <div class="stats-card total">
            <div class="stats-icon">
                <i class="fas fa-file-invoice-dollar" style="color: var(--navy);"></i>
            </div>
            <div class="stats-number">{{ $stats['all'] }}</div>
            <div class="stats-label">Total VR</div>
        </div>

        @if (in_array(auth()->user()->role, ['admin_eng', 'om', 'gm']))
            <div class="stats-card my-pending">
                <div class="stats-icon">
                    <i class="fas fa-clock" style="color: var(--orange);"></i>
                </div>
                <div class="stats-number">{{ $pendingMyApproval }}</div>
                <div class="stats-label">Need My Approval</div>
            </div>
        @endif

        <div class="stats-card pending">
            <div class="stats-icon">
                <i class="fas fa-hourglass-half" style="color: var(--warning);"></i>
            </div>
            <div class="stats-number">{{ $stats['pending'] }}</div>
            <div class="stats-label">Pending</div>
        </div>

        <div class="stats-card approved">
            <div class="stats-icon">
                <i class="fas fa-check-circle" style="color: var(--success);"></i>
            </div>
            <div class="stats-number">{{ $stats['gm_approved'] + $stats['paid'] }}</div>
            <div class="stats-label">Approved/Paid</div>
        </div>

        <div class="stats-card rejected">
            <div class="stats-icon">
                <i class="fas fa-times-circle" style="color: var(--danger);"></i>
            </div>
            <div class="stats-number">{{ $stats['rejected'] }}</div>
            <div class="stats-label">Rejected</div>
        </div>
    </div>

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
                            <i class="fas fa-calendar-alt me-1"></i> {{ $vr->created_at->format('d M Y, H:i') }}
                            @if ($vr->created_by === auth()->id())
                                <span class="badge bg-info ms-2">Created by me</span>
                            @endif
                        </div>
                    </div>
                    <div class="vr-status status-{{ $vr->status }}">
                        {{ str_replace('_', ' ', $vr->status) }}
                    </div>
                </div>

                <div class="vr-body">
                    <div class="vr-info-grid">
                        <div class="info-item">
                            <div class="info-label">Ticket</div>
                            <div class="info-value">
                                <strong>#{{ $vr->ticket->ticket_number }}</strong><br>
                                <small>{{ Str::limit($vr->ticket->title, 40) }}</small>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Created By</div>
                            <div class="info-value">
                                {{ $vr->creator->name }}<br>
                                <small>{{ ucfirst($vr->creator->role) }}</small>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Photos</div>
                            <div class="info-value">
                                <span class="photo-count">
                                    <i class="fas fa-camera"></i> {{ $vr->attachments->count() }} photo(s)
                                </span>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Last Update</div>
                            <div class="info-value">
                                {{ $vr->updated_at->format('d M Y, H:i') }}<br>
                                <small>{{ $vr->updated_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Photo Thumbnails Preview -->
                    @if ($vr->attachments->count() > 0)
                        <div class="photo-thumbnails">
                            @foreach ($vr->attachments->take(3) as $photo)
                                <a href="{{ Storage::url($photo->file_path) }}" data-lightbox="vr-{{ $vr->id }}"
                                    data-title="{{ $photo->description ?? $photo->file_name }}">
                                    <img src="{{ Storage::url($photo->file_path) }}" alt="{{ $photo->file_name }}"
                                        class="photo-thumb">
                                </a>
                            @endforeach
                            @if ($vr->attachments->count() > 3)
                                <span class="photo-count">
                                    <i class="fas fa-plus"></i> {{ $vr->attachments->count() - 3 }} more
                                </span>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                @php
                    $user = auth()->user();
                    $canApprove = false;
                    $canReject = false;
                    $canMarkPaid = false;
                    $canDelete = false;

                    // Approve permissions
                    if ($user->role === 'admin_eng' && $vr->status === 'pending') {
                        $canApprove = $canReject = true;
                    } elseif ($user->role === 'om' && $vr->status === 'admin_approved') {
                        $canApprove = $canReject = true;
                    } elseif ($user->role === 'gm' && $vr->status === 'om_approved') {
                        $canApprove = $canReject = true;
                    }

                    // Mark as paid
                    if (in_array($user->role, ['admin_eng', 'superadmin']) && $vr->status === 'gm_approved') {
                        $canMarkPaid = true;
                    }

                    // Delete permissions
                    if ($user->role === 'superadmin') {
                        $canDelete = true;
                    } elseif ($vr->created_by === $user->id && in_array($vr->status, ['pending', 'rejected'])) {
                        $canDelete = true;
                    }
                @endphp

                <div class="vr-actions">
                    <button class="btn-vr btn-view" onclick="showVRModal({{ $vr->id }})">
                        <i class="fas fa-eye"></i> View Details
                    </button>

                    <button class="btn-vr btn-print" onclick="printVR({{ $vr->id }})">
                        <i class="fas fa-print"></i> Print
                    </button>

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
        <div class="mt-4">
            {{ $vrs->links() }}
        </div>
    @endif
@endsection

<!-- MODALS -->
@include('vouchers.partials.modals')

@push('scripts')
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>

    <script>
        // Lightbox configuration
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true,
            'albumLabel': 'Photo %1 of %2'
        });

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
            // Initialize signature pad when approve modal opens
            $('#approveVRModal').on('shown.bs.modal', function() {
                const canvas = document.getElementById('approveSignatureCanvas');
                if (canvas) {
                    // Set fixed dimensions
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

            // Toggle signature options
            $('input[name="signature_option"]').on('change', function() {
                if ($(this).val() === 'new') {
                    $('#newSignatureSection').show();
                    $('#savedSignatureSection').hide();
                } else {
                    $('#newSignatureSection').hide();
                    $('#savedSignatureSection').show();
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

            // Prevent FOUT - hide body until fully loaded
            $('body').css('visibility', 'visible');
        });

        // ============================================
        // VR CRUD FUNCTIONS
        // ============================================

        function openCreateVRModal() {
            $('#createVRModal').modal('show');
            $('#createVRModalBody').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading VR form...</p>
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
                        showError('Failed to load form');
                    }
                },
                error: function() {
                    showError('Failed to load form. Please try again.');
                }
            });
        }

        function initTicketSelect2() {
            if (typeof $.fn.select2 !== 'undefined') {
                $('#ticketSelect').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Search tickets by number or title...',
                    allowClear: true,
                    minimumInputLength: 2,
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
                                pagination: data.pagination
                            };
                        }
                    }
                });
            }
        }

        function continueToVRForm() {
            const selected = $('#ticketSelect').select2('data')[0];

            if (!selected || !selected.ticket_id) {
                toastr.error('Please select a ticket first');
                return;
            }

            loadVRForm(selected.ticket_id);
        }

        function searchManualTicket() {
            const ticketNumber = $('#manualTicketNumber').val().trim();

            if (!ticketNumber) {
                toastr.error('Please enter a ticket number');
                return;
            }

            $('#createVRModalBody').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Searching for ticket...</p>
                </div>
            `);

            $.ajax({
                url: '{{ route('vouchers.find-ticket', '') }}/' + encodeURIComponent(ticketNumber),
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        loadVRForm(response.ticket_id);
                    } else {
                        showError(response.message || 'Ticket not found');
                    }
                },
                error: function(xhr) {
                    showError(xhr.responseJSON?.message || 'Failed to find ticket');
                }
            });
        }

        function loadVRForm(ticketId) {
            $.ajax({
                url: '{{ route('vouchers.create-modal', '') }}/' + ticketId,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#createVRModalBody').html(response.html);
                    } else {
                        showError(response.message);
                    }
                },
                error: function(xhr) {
                    showError(xhr.responseJSON?.message || 'Failed to load VR form');
                }
            });
        }

        function showVRModal(vrId) {
            $('#viewVRModalBody').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading VR details...</p>
                </div>
            `);
            $('#viewVRModal').modal('show');

            $.ajax({
                url: '{{ route('vouchers.show-modal', '') }}/' + vrId,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#viewVRModalBody').html(response.html);
                        // Reinitialize lightbox for new content
                        if (typeof lightbox !== 'undefined') {
                            lightbox.init();
                        }
                    } else {
                        showError(response.message);
                    }
                },
                error: function() {
                    showError('Failed to load VR details');
                }
            });
        }

        function approveVR(vrId) {
            currentVRId = vrId;
            $('#approveVRId').val(vrId);
            $('#approveVRForm')[0].reset();
            $('#newSignatureSection').hide();
            $('#savedSignatureSection').show();
            $('#useSaved').prop('checked', true);
            $('#approveVRModal').modal('show');
        }

        function rejectVR(vrId) {
            currentVRId = vrId;
            $('#rejectVRId').val(vrId);
            $('#rejectVRForm')[0].reset();
            $('#rejectVRModal').modal('show');
        }

        function markPaid(vrId) {
            currentVRId = vrId;
            $('#markPaidVRId').val(vrId);
            $('#markPaidForm')[0].reset();
            $('#markPaidModal').modal('show');
        }

        function printVR(vrId) {
            // Dummy print function
            Swal.fire({
                title: 'Print Feature',
                text: 'Print feature will be available soon.',
                icon: 'info',
                confirmButtonColor: 'var(--navy)'
            });
        }

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
                            inputAttributes: {
                                autocapitalize: 'off',
                                autocorrect: 'off'
                            }
                        }).then((passwordResult) => {
                            if (passwordResult.isConfirmed && passwordResult.value) {
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
                url: '{{ route('vouchers.destroy', '') }}/' + vrId,
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

        $(document).on('submit', '#createVRForm', function(e) {
            e.preventDefault();

            const form = $(this);
            const formData = new FormData(form[0]);
            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.html();

            // Validate at least one photo
            const photos = formData.getAll('photos[]');
            if (!photos || photos.length === 0 || photos[0].size === 0) {
                toastr.error('Please upload at least one photo');
                return;
            }

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
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        toastr.error(response.message);
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                },
                error: function(xhr) {
                    let message = 'Failed to create VR';
                    if (xhr.responseJSON?.message) {
                        message = xhr.responseJSON.message;
                    }
                    if (xhr.responseJSON?.errors) {
                        Object.values(xhr.responseJSON.errors).forEach(err => {
                            if (Array.isArray(err)) err.forEach(e => toastr.error(e));
                        });
                    }
                    toastr.error(message);
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        $(document).on('submit', '#approveVRForm', function(e) {
            e.preventDefault();

            const form = $(this);
            const formData = new FormData(form[0]);
            const useNewSignature = $('#createNew').is(':checked');

            if (useNewSignature) {
                if (!signaturePad || signaturePad.isEmpty()) {
                    toastr.error('Please draw your signature');
                    return;
                }
                formData.append('signature_data', signaturePad.toDataURL());
            } else {
                formData.append('use_saved_signature', '1');
            }

            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Approving...');

            $.ajax({
                url: '{{ route('vouchers.approve', '') }}/' + currentVRId,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#approveVRModal').modal('hide');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Failed to approve VR');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        $(document).on('submit', '#rejectVRForm', function(e) {
            e.preventDefault();

            const form = $(this);
            const formData = form.serialize();
            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.html();

            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Rejecting...');

            $.ajax({
                url: '{{ route('vouchers.reject', '') }}/' + currentVRId,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#rejectVRModal').modal('hide');
                        setTimeout(() => location.reload(), 1500);
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

        $(document).on('submit', '#markPaidForm', function(e) {
            e.preventDefault();

            const form = $(this);
            const formData = form.serialize();
            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.html();

            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

            $.ajax({
                url: '{{ route('vouchers.mark-paid', '') }}/' + currentVRId,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#markPaidModal').modal('hide');
                        setTimeout(() => location.reload(), 1500);
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

        function toggleSearchSection(type) {
            $('.search-section').removeClass('active');
            $(`#${type}SearchSection`).addClass('active');
        }

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

        function showError(message) {
            $('#createVRModalBody').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i> ${message}
                </div>
                <div class="text-center mt-3">
                    <button class="btn btn-secondary" onclick="openCreateVRModal()">
                        <i class="fas fa-arrow-left"></i> Try Again
                    </button>
                </div>
            `);
        }

        // Preview uploaded photos
        $(document).on('change', 'input[name="photos[]"]', function() {
            const preview = $('#photoPreview');
            if (preview.length) {
                preview.empty();
                const files = this.files;

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        preview.append(`
                            <div class="photo-thumb-container">
                                <img src="${e.target.result}" class="photo-thumb" alt="Preview">
                                <input type="text" name="photo_descriptions[]"
                                       class="form-control form-control-sm mt-1"
                                       placeholder="Description (optional)">
                            </div>
                        `);
                    }

                    reader.readAsDataURL(file);
                }
            }
        });
    </script>
@endpush
