<div class="create-vr-form">
    <form id="createVRForm">
        @csrf
        <input type="hidden" name="vr_number" value="{{ $vrNumber }}">

        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Creating VR for Ticket: <strong>#{{ $ticket->ticket_number }}</strong> - {{ $ticket->title }}
        </div>

        <div class="mb-3">
            <label class="form-label">VR Number</label>
            <input type="text" class="form-control" value="{{ $vrNumber }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Notes (Optional)</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Add any notes about this VR..."></textarea>
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="form-label mb-0"><strong>Items</strong></label>
                <button type="button" class="btn btn-sm btn-primary" onclick="addItemRow()">
                    <i class="fas fa-plus me-1"></i> Add Item
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-sm" id="itemsTable">
                    <thead>
                        <tr>
                            <th width="40%">Item Name *</th>
                            <th width="15%">Qty *</th>
                            <th width="25%">Unit Price (Rp) *</th>
                            <th width="15%">Vendor</th>
                            <th width="5%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="item-row">
                            <td>
                                <input type="text" name="items[0][item_name]" class="form-control form-control-sm"
                                    placeholder="Item name" required>
                            </td>
                            <td>
                                <input type="number" name="items[0][qty]" class="form-control form-control-sm"
                                    min="1" value="1" required>
                            </td>
                            <td>
                                <input type="number" name="items[0][unit_price]" class="form-control form-control-sm"
                                    min="0" step="1000" placeholder="0" required>
                            </td>
                            <td>
                                <input type="text" name="items[0][vendor]" class="form-control form-control-sm"
                                    placeholder="Vendor (optional)">
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm" onclick="removeItemRow(this)"
                                    disabled>
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-end">
                                <strong>Total Amount: </strong>
                                <span id="totalAmount" class="text-success fw-bold">Rp 0</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-check me-1"></i> Create VR
            </button>
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                Cancel
            </button>
        </div>
    </form>
</div>

<script>
    // Calculate initial total
    $(document).ready(function() {
        calculateTotal();
    });

    function calculateTotal() {
        let total = 0;
        $('.item-row').each(function() {
            const qty = $(this).find('input[name*="[qty]"]').val() || 0;
            const price = $(this).find('input[name*="[unit_price]"]').val() || 0;
            total += qty * price;
        });
        $('#totalAmount').text('Rp ' + total.toLocaleString('id-ID'));
    }

    function addItemRow() {
        const rowCount = $('.item-row').length;
        const newRow = `
            <tr class="item-row">
                <td>
                    <input type="text" name="items[${rowCount}][item_name]"
                           class="form-control form-control-sm" placeholder="Item name" required>
                </td>
                <td>
                    <input type="number" name="items[${rowCount}][qty]"
                           class="form-control form-control-sm" min="1" value="1" required>
                </td>
                <td>
                    <input type="number" name="items[${rowCount}][unit_price]"
                           class="form-control form-control-sm" min="0" step="1000" placeholder="0" required>
                </td>
                <td>
                    <input type="text" name="items[${rowCount}][vendor]"
                           class="form-control form-control-sm" placeholder="Vendor (optional)">
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
            alert('At least one item is required');
        }
    }

    // Auto-calculate when inputs change
    $(document).on('input', 'input[name*="[qty]"], input[name*="[unit_price]"]', function() {
        calculateTotal();
    });
</script>
