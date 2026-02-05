<form id="createVRForm">
    @csrf
    <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
    <input type="hidden" name="vr_number" value="{{ $vrNumber }}">

    <div class="alert alert-success mb-3">
        <i class="fas fa-check-circle me-2"></i>
        Creating VR for Ticket: <strong>#{{ $ticket->ticket_number }}</strong> - {{ $ticket->title }}
    </div>

    <!-- VR Information -->
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">VR Number</label>
            <input type="text" class="form-control" value="{{ $vrNumber }}" readonly>
        </div>
        <div class="col-md-6">
            <label class="form-label">Date</label>
            <input type="text" class="form-control" value="{{ now()->format('d M Y, H:i') }}" readonly>
        </div>
    </div>

    <!-- Ticket Information -->
    <div class="card mb-3">
        <div class="card-header">
            <h6 class="mb-0">Ticket Information</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-2">
                        <small class="text-muted">Ticket Number</small>
                        <div class="fw-bold">#{{ $ticket->ticket_number }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Title</small>
                        <div>{{ $ticket->title }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2">
                        <small class="text-muted">Category</small>
                        <div>{{ $ticket->category->name }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Priority</small>
                        <div>{{ $ticket->priority->name }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- VR Notes -->
    <div class="mb-3">
        <label class="form-label">VR Notes (Optional)</label>
        <textarea name="notes" class="form-control" rows="3" placeholder="Add any notes about this VR request..."></textarea>
    </div>

    <!-- Items Table -->
    <div class="mb-3">
        <label class="form-label">Items * <span class="text-danger">(At least one item required)</span></label>
        <div class="table-responsive">
            <table class="table table-sm" id="itemsTable">
                <thead>
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
                        <td></td>
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
        <small class="text-muted">Click "Add Item" to add more items. Click the trash icon to remove an item.</small>
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save me-2"></i> Create Voucher Request
        </button>
        <button type="button" class="btn btn-secondary" onclick="openCreateVRModal()">
            <i class="fas fa-arrow-left me-2"></i> Back to Ticket Selection
        </button>
    </div>
</form>
