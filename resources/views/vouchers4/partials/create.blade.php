@extends('layouts.main')

@section('title', 'Create Voucher Request | ' . config('app.name'))

@section('page-title', 'Create Voucher Request')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Voucher Requests', 'url' => route('vouchers.index')],
            ['title' => 'Create', 'url' => 'javascript:void(0)'],
        ];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/select2/css/select2-bootstrap-5-theme.min.css') }}">

    <style>
        /* Prevent FOUT - inline critical styles */
        .vr-form-container {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        .section-title {
            color: #003366;
            border-bottom: 2px solid #ff6600;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 18px;
        }

        .ticket-card {
            background: #f8f9fa;
            border-left: 4px solid #ff6600;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .item-row input,
        .item-row select {
            width: 100%;
            min-width: 100px;
        }

        @media (max-width: 768px) {
            .vr-form-container {
                padding: 15px;
            }

            .table-responsive {
                font-size: 13px;
            }

            .item-row input,
            .item-row select {
                min-width: 80px;
            }

            .btn-sm {
                padding: 4px 8px;
                font-size: 12px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="vr-form-container">
        <form id="createVRForm" enctype="multipart/form-data">
            @csrf

            <!-- Step 1: Ticket Selection -->
            <div id="step1">
                <h5 class="section-title">
                    <i class="fas fa-ticket-alt me-2"></i> Step 1: Select Ticket
                </h5>

                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    Select a ticket that requires a Voucher Request (VR). Only tickets in <strong>Pending VR</strong>
                    status will be shown.
                </div>

                <!-- Search Type Selection -->
                <div class="mb-4">
                    <label class="form-label mb-3">Select search method:</label>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <input type="radio" name="search_type" id="search_select2" value="select2" checked
                                        onchange="toggleSearchSection('select2')">
                                    <label class="form-check-label d-block" for="search_select2">
                                        <i class="fas fa-search fa-3x text-primary mb-3"></i>
                                        <h6>Search Ticket</h6>
                                        <p class="small text-muted">Search from pending VR tickets</p>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <input type="radio" name="search_type" id="search_manual" value="manual"
                                        onchange="toggleSearchSection('manual')">
                                    <label class="form-check-label d-block" for="search_manual">
                                        <i class="fas fa-keyboard fa-3x text-warning mb-3"></i>
                                        <h6>Enter Ticket Number</h6>
                                        <p class="small text-muted">Type ticket number manually</p>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Select2 Search Section -->
                <div class="search-section active" id="select2SearchSection">
                    <div class="mb-3">
                        <label class="form-label">Search Ticket</label>
                        <select id="ticketSelect" class="form-select" style="width: 100%;">
                            <option value="">Type to search tickets...</option>
                        </select>
                        <small class="text-muted">Only shows tickets that are in pending VR status</small>
                    </div>

                    <div class="d-grid">
                        <button type="button" class="btn btn-primary" onclick="continueToStep2()">
                            <i class="fas fa-arrow-right me-2"></i> Continue with Selected Ticket
                        </button>
                    </div>
                </div>

                <!-- Manual Search Section -->
                <div class="search-section" id="manualSearchSection">
                    <div class="mb-3">
                        <label class="form-label">Ticket Number</label>
                        <div class="input-group">
                            <input type="text" id="manualTicketNumber" class="form-control"
                                placeholder="Enter ticket number (e.g., TKT-2026-01-0001)">
                            <button class="btn btn-outline-secondary" type="button" onclick="searchManualTicket()">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                        <small class="text-muted">Enter the exact ticket number from pending VR tickets</small>
                    </div>
                </div>
            </div>

            <!-- Step 2: VR Form (initially hidden) -->
            <div id="step2" style="display: none;">
                <input type="hidden" name="ticket_id" id="selectedTicketId">
                <input type="hidden" name="vr_number" id="vrNumber">

                <h5 class="section-title">
                    <i class="fas fa-file-invoice-dollar me-2"></i> Step 2: VR Details
                </h5>

                <!-- Ticket Info Card -->
                <div class="ticket-card" id="ticketInfoCard">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong class="text-navy">Ticket Information</strong>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="goBackToStep1()">
                            <i class="fas fa-arrow-left me-1"></i> Change Ticket
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Ticket Number</small>
                            <div class="fw-bold" id="displayTicketNumber">-</div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Title</small>
                            <div id="displayTicketTitle">-</div>
                        </div>
                        <div class="col-md-6 mt-2">
                            <small class="text-muted">Category</small>
                            <div id="displayTicketCategory">-</div>
                        </div>
                        <div class="col-md-6 mt-2">
                            <small class="text-muted">Priority</small>
                            <div id="displayTicketPriority">-</div>
                        </div>
                    </div>
                </div>

                <!-- VR Notes -->
                <div class="mb-3">
                    <label class="form-label">VR Notes (Optional)</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Add any notes about this VR request..."></textarea>
                </div>

                <!-- Photos Upload -->
                <div class="mb-3">
                    <label class="form-label">Upload Photos (Optional)</label>
                    <input type="file" name="photos[]" class="form-control" multiple accept="image/*">
                    <small class="text-muted">You can upload multiple photos. Max 5MB per file.</small>
                    <div id="photoPreview" class="mt-2 d-flex flex-wrap gap-2"></div>
                </div>

                <!-- Items Section -->
                <div class="mb-3">
                    <label class="form-label">Items * <span class="text-danger">(At least one item
                            required)</span></label>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="30%">Item Name *</th>
                                    <th width="10%">Qty *</th>
                                    <th width="20%">Unit Price (Rp) *</th>
                                    <th width="20%">Vendor</th>
                                    <th width="15%">Description</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="item-row">
                                    <td><input type="text" name="items[0][item_name]"
                                            class="form-control form-control-sm" placeholder="e.g., Light Bulb" required>
                                    </td>
                                    <td><input type="number" name="items[0][qty]" class="form-control form-control-sm"
                                            min="1" value="1" required></td>
                                    <td><input type="number" name="items[0][unit_price]"
                                            class="form-control form-control-sm" min="0" step="1000"
                                            placeholder="0" required></td>
                                    <td><input type="text" name="items[0][vendor]"
                                            class="form-control form-control-sm" placeholder="Vendor name"></td>
                                    <td><input type="text" name="items[0][description]"
                                            class="form-control form-control-sm" placeholder="Item description"></td>
                                    <td class="text-center"></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="text-end">
                                        <button type="button" class="btn btn-sm btn-primary" onclick="addItemRow()">
                                            <i class="fas fa-plus me-1"></i> Add Item
                                        </button>
                                    </td>
                                </tr>
                                <tr class="table-success">
                                    <td colspan="2" class="text-end fw-bold">TOTAL AMOUNT:</td>
                                    <td colspan="4" class="fw-bold" id="totalAmount">Rp 0</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <small class="text-muted">Click "Add Item" to add more items. Click the trash icon to remove an
                        item.</small>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save me-2"></i> Create Voucher Request
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="goBackToStep1()">
                        <i class="fas fa-arrow-left me-2"></i> Back to Ticket Selection
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/select2/js/select2.full.min.js') }}"></script>

    <script>
        let selectedTicketData = null;
        let itemCounter = 1;

        // Toastr configuration
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "4000"
        };

        $(document).ready(function() {
            // Initialize Select2
            initTicketSelect2();
        });

        // Initialize Select2 for ticket search
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
                            <div class="text-muted small">${ticket.title}</div>
                            <div class="small">
                                <span class="badge bg-info">${ticket.category}</span>
                                <span class="badge bg-warning">${ticket.priority}</span>
                            </div>
                        </div>
                    `);
                },
                templateSelection: function(ticket) {
                    if (!ticket.id) return ticket.text;
                    return `#${ticket.ticket_number} - ${ticket.title.substring(0, 40)}`;
                }
            });
        }

        // Toggle search sections
        function toggleSearchSection(type) {
            $('.search-section').removeClass('active');
            if (type === 'select2') {
                $('#select2SearchSection').addClass('active');
            } else {
                $('#manualSearchSection').addClass('active');
            }
        }

        // Continue to Step 2 (VR Form)
        function continueToStep2() {
            const selectedTicket = $('#ticketSelect').select2('data')[0];

            if (!selectedTicket || !selectedTicket.ticket_id) {
                toastr.error('Please select a ticket first');
                return;
            }

            loadVRForm(selectedTicket.ticket_id);
        }

        // Search manual ticket
        function searchManualTicket() {
            const ticketNumber = $('#manualTicketNumber').val().trim();

            if (!ticketNumber) {
                toastr.error('Please enter a ticket number');
                return;
            }

            $('#manualSearchSection').html(`
                <div class="text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Searching for ticket...</p>
                </div>
            `);

            $.ajax({
                url: '{{ route('vouchers.find-ticket', '') }}/' + encodeURIComponent(ticketNumber),
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        loadVRForm(response.ticket_id);
                    } else {
                        $('#manualSearchSection').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> ${response.message}
                            </div>
                            <div class="input-group mt-2">
                                <input type="text" id="manualTicketNumber" class="form-control"
                                    placeholder="Enter ticket number (e.g., TKT-2026-01-0001)">
                                <button class="btn btn-outline-secondary" type="button" onclick="searchManualTicket()">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                        `);
                    }
                },
                error: function(xhr) {
                    $('#manualSearchSection').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> Failed to find ticket
                        </div>
                        <div class="input-group mt-2">
                            <input type="text" id="manualTicketNumber" class="form-control"
                                placeholder="Enter ticket number (e.g., TKT-2026-01-0001)">
                            <button class="btn btn-outline-secondary" type="button" onclick="searchManualTicket()">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    `);
                }
            });
        }

        // Load VR Form for selected ticket
        function loadVRForm(ticketId) {
            // Show loading
            $('#step2').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Loading VR form...</p>
                </div>
            `);
            $('#step2').show();
            $('#step1').hide();

            $.ajax({
                url: '{{ route('vouchers.create-form', '') }}/' + ticketId,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        // Update ticket info
                        selectedTicketData = response.ticket;
                        $('#selectedTicketId').val(response.ticket.id);
                        $('#vrNumber').val(response.vr_number);
                        $('#displayTicketNumber').text('#' + response.ticket.ticket_number);
                        $('#displayTicketTitle').text(response.ticket.title);
                        $('#displayTicketCategory').text(response.ticket.category_name);
                        $('#displayTicketPriority').text(response.ticket.priority_name);

                        // Reset items table to single row
                        resetItemsTable();

                        $('#step2').html($('#step2').html()); // Keep current content
                        $('#step2').show();
                    } else {
                        toastr.error(response.message);
                        goBackToStep1();
                    }
                },
                error: function(xhr) {
                    toastr.error('Failed to load VR form');
                    goBackToStep1();
                }
            });
        }

        // Reset items table to single row
        function resetItemsTable() {
            itemCounter = 1;
            const tbody = $('#itemsTable tbody');
            tbody.html(`
                <tr class="item-row">
                    <td><input type="text" name="items[0][item_name]" class="form-control form-control-sm"
                            placeholder="e.g., Light Bulb" required></td>
                    <td><input type="number" name="items[0][qty]" class="form-control form-control-sm"
                            min="1" value="1" required></td>
                    <td><input type="number" name="items[0][unit_price]" class="form-control form-control-sm"
                            min="0" step="1000" placeholder="0" required></td>
                    <td><input type="text" name="items[0][vendor]" class="form-control form-control-sm"
                            placeholder="Vendor name"></td>
                    <td><input type="text" name="items[0][description]" class="form-control form-control-sm"
                            placeholder="Item description"></td>
                    <td class="text-center"></td>
                </tr>
            `);
            calculateTotal();
        }

        // Add new item row
        function addItemRow() {
            const newIndex = itemCounter;
            const newRow = `
                <tr class="item-row">
                    <td><input type="text" name="items[${newIndex}][item_name]" class="form-control form-control-sm"
                            placeholder="e.g., Light Bulb" required></td>
                    <td><input type="number" name="items[${newIndex}][qty]" class="form-control form-control-sm"
                            min="1" value="1" required></td>
                    <td><input type="number" name="items[${newIndex}][unit_price]" class="form-control form-control-sm"
                            min="0" step="1000" placeholder="0" required></td>
                    <td><input type="text" name="items[${newIndex}][vendor]" class="form-control form-control-sm"
                            placeholder="Vendor name"></td>
                    <td><input type="text" name="items[${newIndex}][description]" class="form-control form-control-sm"
                            placeholder="Item description"></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeItemRow(this)">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#itemsTable tbody').append(newRow);
            itemCounter++;
            calculateTotal();
        }

        // Remove item row
        function removeItemRow(button) {
            if ($('.item-row').length > 1) {
                $(button).closest('tr').remove();
                calculateTotal();
            } else {
                toastr.error('At least one item is required');
            }
        }

        // Calculate total amount
        function calculateTotal() {
            let total = 0;
            $('.item-row').each(function() {
                const qty = parseInt($(this).find('input[name*="[qty]"]').val()) || 0;
                const price = parseInt($(this).find('input[name*="[unit_price]"]').val()) || 0;
                total += qty * price;
            });
            $('#totalAmount').text('Rp ' + total.toLocaleString('id-ID'));
        }

        // Auto-calculate total on input change
        $(document).on('input', 'input[name*="[qty]"], input[name*="[unit_price]"]', function() {
            calculateTotal();
        });

        // Go back to step 1
        function goBackToStep1() {
            $('#step2').hide();
            $('#step1').show();
            $('#step2').html(`
                <input type="hidden" name="ticket_id" id="selectedTicketId">
                <input type="hidden" name="vr_number" id="vrNumber">

                <h5 class="section-title">
                    <i class="fas fa-file-invoice-dollar me-2"></i> Step 2: VR Details
                </h5>

                <div class="ticket-card" id="ticketInfoCard">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong class="text-navy">Ticket Information</strong>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="goBackToStep1()">
                            <i class="fas fa-arrow-left me-1"></i> Change Ticket
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><small class="text-muted">Ticket Number</small><div class="fw-bold" id="displayTicketNumber">-</div></div>
                        <div class="col-md-6"><small class="text-muted">Title</small><div id="displayTicketTitle">-</div></div>
                        <div class="col-md-6 mt-2"><small class="text-muted">Category</small><div id="displayTicketCategory">-</div></div>
                        <div class="col-md-6 mt-2"><small class="text-muted">Priority</small><div id="displayTicketPriority">-</div></div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">VR Notes (Optional)</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Add any notes about this VR request..."></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Upload Photos (Optional)</label>
                    <input type="file" name="photos[]" class="form-control" multiple accept="image/*">
                    <small class="text-muted">You can upload multiple photos. Max 5MB per file.</small>
                    <div id="photoPreview" class="mt-2 d-flex flex-wrap gap-2"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Items * <span class="text-danger">(At least one item required)</span></label>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered" id="itemsTable">
                            <thead class="table-light"><tr><th width="30%">Item Name *</th><th width="10%">Qty *</th><th width="20%">Unit Price (Rp) *</th><th width="20%">Vendor</th><th width="15%">Description</th><th width="5%"></th></tr></thead>
                            <tbody><tr class="item-row"><td><input type="text" name="items[0][item_name]" class="form-control form-control-sm" placeholder="e.g., Light Bulb" required></td><td><input type="number" name="items[0][qty]" class="form-control form-control-sm" min="1" value="1" required></td><td><input type="number" name="items[0][unit_price]" class="form-control form-control-sm" min="0" step="1000" placeholder="0" required></td><td><input type="text" name="items[0][vendor]" class="form-control form-control-sm" placeholder="Vendor name"></td><td><input type="text" name="items[0][description]" class="form-control form-control-sm" placeholder="Item description"></td><td class="text-center"></td></tr></tbody>
                            <tfoot><tr><td colspan="6" class="text-end"><button type="button" class="btn btn-sm btn-primary" onclick="addItemRow()"><i class="fas fa-plus me-1"></i> Add Item</button></td></tr><tr class="table-success"><td colspan="2" class="text-end fw-bold">TOTAL AMOUNT:</td><td colspan="4" class="fw-bold" id="totalAmount">Rp 0</td></tr></tfoot>
                        </table>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save me-2"></i> Create Voucher Request</button>
                    <button type="button" class="btn btn-secondary" onclick="goBackToStep1()"><i class="fas fa-arrow-left me-2"></i> Back to Ticket Selection</button>
                </div>
            `);
        }

        // Submit Create VR Form
        $('#createVRForm').on('submit', function(e) {
            e.preventDefault();

            // Validate at least one item
            if ($('.item-row').length === 0) {
                toastr.error('Please add at least one item');
                return;
            }

            // Validate all items have required fields
            let valid = true;
            $('.item-row').each(function(index) {
                const itemName = $(this).find('input[name*="[item_name]"]').val();
                const qty = $(this).find('input[name*="[qty]"]').val();
                const price = $(this).find('input[name*="[unit_price]"]').val();

                if (!itemName) {
                    toastr.error(`Item ${index + 1}: Item name is required`);
                    valid = false;
                    return false;
                }
                if (!qty || qty < 1) {
                    toastr.error(`Item ${index + 1}: Quantity must be at least 1`);
                    valid = false;
                    return false;
                }
                if (!price || price < 0) {
                    toastr.error(`Item ${index + 1}: Unit price must be valid`);
                    valid = false;
                    return false;
                }
            });

            if (!valid) return;

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
                        setTimeout(() => {
                            window.location.href = '{{ route('vouchers.index') }}';
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
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        Object.values(xhr.responseJSON.errors).forEach(error => {
                            if (Array.isArray(error)) {
                                error.forEach(err => toastr.error(err));
                            } else {
                                toastr.error(error);
                            }
                        });
                    } else {
                        toastr.error(message);
                    }
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
    </script>

    <style>
        .search-section {
            display: none;
        }

        .search-section.active {
            display: block;
        }

        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px;
        }

        @media (max-width: 768px) {
            .select2-container--bootstrap-5 .select2-selection__rendered {
                font-size: 14px;
            }
        }
    </style>
@endpush
