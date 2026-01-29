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
    <style>
        .vr-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background: white;
            transition: all 0.3s;
        }

        .vr-card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .vr-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .vr-number {
            font-weight: 700;
            color: #ff6200;
            font-size: 16px;
        }

        .vr-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
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

        .vr-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-bottom: 10px;
        }

        .info-item {
            font-size: 13px;
        }

        .info-label {
            color: #666;
            font-weight: 500;
        }

        .info-value {
            color: #333;
            font-weight: 600;
        }

        .vr-actions {
            display: flex;
            gap: 8px;
            margin-top: 10px;
        }

        .btn-vr-action {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-view {
            background: #6c757d;
            color: white;
        }

        .btn-view:hover {
            background: #5a6268;
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

        .btn-create {
            background: #ff6200;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .btn-create:hover {
            background: #e65500;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #ddd;
        }

        /* Quick stats */
        .stats-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            margin-bottom: 15px;
        }

        .stats-number {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stats-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }

        .stats-pending {
            color: #ffc107;
        }

        .stats-approved {
            color: #28a745;
        }

        .stats-rejected {
            color: #dc3545;
        }

        .stats-paid {
            color: #17a2b8;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <!-- Quick Stats -->
        <div class="col-12 mb-4">
            <div class="row">
                @php
                    $user = auth()->user();
                    $stats = [
                        'all' => App\Models\VoucherRequest::count(),
                        'pending' => App\Models\VoucherRequest::where('status', 'pending')->count(),
                        'pending_my_approval' => 0,
                        'approved' => App\Models\VoucherRequest::whereIn('status', [
                            'admin_approved',
                            'om_approved',
                            'gm_approved',
                        ])->count(),
                        'rejected' => App\Models\VoucherRequest::where('status', 'rejected')->count(),
                        'paid' => App\Models\VoucherRequest::where('status', 'paid')->count(),
                    ];

                    // Hitung pending my approval berdasarkan role
                    if ($user->role === 'admin_eng') {
                        $stats['pending_my_approval'] = App\Models\VoucherRequest::where('status', 'pending')->count();
                    } elseif ($user->role === 'om') {
                        $stats['pending_my_approval'] = App\Models\VoucherRequest::where(
                            'status',
                            'admin_approved',
                        )->count();
                    } elseif ($user->role === 'gm') {
                        $stats['pending_my_approval'] = App\Models\VoucherRequest::where(
                            'status',
                            'om_approved',
                        )->count();
                    }
                @endphp

                <div class="col-md-2 col-sm-4 col-6">
                    <div class="stats-card">
                        <div class="stats-number">{{ $stats['all'] }}</div>
                        <div class="stats-label">Total VR</div>
                    </div>
                </div>

                @if (in_array($user->role, ['admin_eng', 'om', 'gm']))
                    <div class="col-md-2 col-sm-4 col-6">
                        <div class="stats-card">
                            <div class="stats-number stats-pending">{{ $stats['pending_my_approval'] }}</div>
                            <div class="stats-label">Pending My Approval</div>
                        </div>
                    </div>
                @endif

                <div class="col-md-2 col-sm-4 col-6">
                    <div class="stats-card">
                        <div class="stats-number">{{ $stats['pending'] }}</div>
                        <div class="stats-label">Pending</div>
                    </div>
                </div>

                <div class="col-md-2 col-sm-4 col-6">
                    <div class="stats-card">
                        <div class="stats-number stats-approved">{{ $stats['approved'] }}</div>
                        <div class="stats-label">Approved</div>
                    </div>
                </div>

                <div class="col-md-2 col-sm-4 col-6">
                    <div class="stats-card">
                        <div class="stats-number stats-rejected">{{ $stats['rejected'] }}</div>
                        <div class="stats-label">Rejected</div>
                    </div>
                </div>

                <div class="col-md-2 col-sm-4 col-6">
                    <div class="stats-card">
                        <div class="stats-number stats-paid">{{ $stats['paid'] }}</div>
                        <div class="stats-label">Paid</div>
                    </div>
                </div>
            </div>
        </div>

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
                            <div class="vr-number">#{{ $vr->vr_number }}</div>
                            <div class="vr-status status-{{ $vr->status }}">
                                {{ str_replace('_', ' ', $vr->status) }}
                            </div>
                        </div>

                        <div class="vr-info">
                            <div class="info-item">
                                <div class="info-label">Ticket</div>
                                <div class="info-value">
                                    #{{ $vr->ticket->ticket_number }} - {{ Str::limit($vr->ticket->title, 30) }}
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">Created By</div>
                                <div class="info-value">{{ $vr->creator->name }}</div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">Date</div>
                                <div class="info-value">{{ $vr->created_at->format('d M Y, H:i') }}</div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">Total Amount</div>
                                <div class="info-value">Rp {{ number_format($vr->total_amount, 0, ',', '.') }}</div>
                            </div>
                        </div>

                        <div class="vr-actions">
                            <button class="btn-vr-action btn-view" onclick="showVRModal({{ $vr->id }})">
                                <i class="fas fa-eye"></i> View
                            </button>

                            <!-- Approve/Reject buttons based on status and role -->
                            @php
                                $user = auth()->user();
                                $canApprove = false;
                                $canReject = false;

                                if ($user->role === 'admin_eng' && $vr->status === 'pending') {
                                    $canApprove = $canReject = true;
                                } elseif ($user->role === 'om' && $vr->status === 'admin_approved') {
                                    $canApprove = $canReject = true;
                                } elseif ($user->role === 'gm' && $vr->status === 'om_approved') {
                                    $canApprove = $canReject = true;
                                }
                            @endphp

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

                            <!-- Mark as Paid button -->
                            @if (in_array($user->role, ['admin_eng', 'superadmin']) && $vr->status === 'gm_approved')
                                <button class="btn-vr-action btn-paid" onclick="markAsPaid({{ $vr->id }})">
                                    <i class="fas fa-check-double"></i> Mark Paid
                                </button>
                            @endif

                            <!-- Delete button (creator or superadmin) -->
                            @if (($vr->created_by === $user->id || $user->role === 'superadmin') && in_array($vr->status, ['pending', 'rejected']))
                                <button class="btn-vr-action btn-reject" onclick="deleteVR({{ $vr->id }})">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <h4>No Voucher Requests</h4>
                        <p>There are no voucher requests yet.</p>
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
                <div class="mt-3">
                    {{ $vrs->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection


<!-- Create VR Modal -->
<div class="modal fade" id="createVRModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Voucher Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="createVRModalBody">
                <!-- Content akan diisi via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- View VR Modal -->
<div class="modal fade" id="viewVRModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Voucher Request Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewVRModalBody" style="max-height: 70vh; overflow-y: auto;">
                <!-- Content akan diisi via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Approve/Reject Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalTitle">Approve/Reject VR</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="currentVRId">
                <div class="mb-3">
                    <label class="form-label">Notes (Optional)</label>
                    <textarea class="form-control" id="approveNotes" rows="3" placeholder="Add any notes..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="submitApprove('approve')">Approve</button>
                <button type="button" class="btn btn-danger" onclick="submitApprove('reject')">Reject</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let currentTicketId = null;

        // Open Create VR Modal - SIMPLE VERSION
        function openCreateVRModal(ticketId = null) {
            if (ticketId) {
                currentTicketId = ticketId;
                loadCreateModal();
                return;
            }

            // Simple ticket number input
            Swal.fire({
                title: 'Create New VR',
                html: `
                <div class="text-start">
                    <p class="mb-3">Enter Ticket Number</p>
                    <div class="mb-3">
                        <label class="form-label">Ticket Number *</label>
                        <input type="text" id="ticketNumber" class="form-control"
                               placeholder="TKT-2026-XXXX" required>
                    </div>
                </div>
            `,
                showCancelButton: true,
                confirmButtonText: 'Continue',
                cancelButtonText: 'Cancel',
                preConfirm: () => {
                    const ticketNumber = document.getElementById('ticketNumber').value;
                    if (!ticketNumber) {
                        Swal.showValidationMessage('Please enter a ticket number');
                        return false;
                    }
                    return {
                        ticketNumber: ticketNumber
                    };
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    // Find ticket by number
                    $.ajax({
                        url: `/api/tickets/find-by-number/${result.value.ticketNumber}`,
                        type: 'GET',
                        success: function(data) {
                            if (data.success) {
                                loadCreateModal(data.ticket_id);
                            } else {
                                Swal.fire('Error', data.message || 'Ticket not found', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Failed to find ticket', 'error');
                        }
                    });
                }
            });
        }

        function loadCreateModal(ticketId) {
            currentTicketId = ticketId;

            // Show modal first
            const modal = new bootstrap.Modal(document.getElementById('createVRModal'));
            modal.show();

            // Load content via AJAX
            $('#createVRModalBody').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading VR form...</p>
            </div>
        `);

            $.ajax({
                url: `/vouchers/create-modal/${currentTicketId}`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#createVRModalBody').html(response.html);
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
                        <i class="fas fa-exclamation-triangle"></i> Failed to load VR form
                    </div>
                `);
                }
            });
        }

        // Show VR Details Modal
        function showVRModal(vrId) {
            // Show modal first
            const modal = new bootstrap.Modal(document.getElementById('viewVRModal'));
            modal.show();

            // Load content
            $('#viewVRModalBody').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading VR details...</p>
            </div>
        `);

            $.ajax({
                url: `/vouchers/${vrId}/show-modal`,
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

        // Approve/Reject VR
        function approveVR(vrId) {
            $('#currentVRId').val(vrId);
            $('#approveModalTitle').text('Approve VR');
            $('#approveNotes').val('');

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('approveModal'));
            modal.show();
        }

        function rejectVR(vrId) {
            $('#currentVRId').val(vrId);
            $('#approveModalTitle').text('Reject VR');
            $('#approveNotes').val('');

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('approveModal'));
            modal.show();
        }

        function submitApprove(action) {
            const vrId = $('#currentVRId').val();
            const notes = $('#approveNotes').val();

            $.ajax({
                url: `/vouchers/${vrId}/approve`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    action: action,
                    notes: notes
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);

                        // Hide modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('approveModal'));
                        modal.hide();

                        // Reload page after delay
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error('Failed to process action');
                }
            });
        }

        // Mark as Paid
        function markAsPaid(vrId) {
            Swal.fire({
                title: 'Mark as Paid?',
                text: "This will mark the VR as paid",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#17a2b8',
                confirmButtonText: 'Yes, mark as paid',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/vouchers/${vrId}/mark-as-paid`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                setTimeout(() => {
                                    location.reload();
                                }, 1000);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function() {
                            toastr.error('Failed to mark as paid');
                        }
                    });
                }
            });
        }

        // Delete VR
        function deleteVR(vrId) {
            Swal.fire({
                title: 'Delete VR?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/vouchers/${vrId}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
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
                        error: function() {
                            toastr.error('Failed to delete VR');
                        }
                    });
                }
            });
        }

        // Handle Create VR Form Submission
        $(document).on('submit', '#createVRForm', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();

            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating...');

            $.ajax({
                url: `/vouchers/${currentTicketId}/store`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);

                        // Hide modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById(
                            'createVRModal'));
                        modal.hide();

                        // Reload page after delay
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
                    toastr.error(message);
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Initialize
        $(document).ready(function() {
            // Check if there's a ticket parameter in URL
            const urlParams = new URLSearchParams(window.location.search);
            const createForTicket = urlParams.get('create_for_ticket');
            if (createForTicket) {
                // Remove parameter from URL
                const newUrl = window.location.pathname;
                window.history.replaceState({}, document.title, newUrl);

                // Open create modal
                openCreateVRModal(createForTicket);
            }
        });
    </script>
@endpush
