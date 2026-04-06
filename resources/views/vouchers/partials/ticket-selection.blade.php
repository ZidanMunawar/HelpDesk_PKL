<div class="ticket-selection-form">
    <div class="alert alert-info mb-3">
        <i class="fas fa-info-circle me-2"></i>
        Select a ticket that requires a Purchase Request (PR). Only tickets in <strong>Pending PR</strong> status will
        be shown.
    </div>

    <!-- Select2 Search -->
    <div class="mb-3">
        <label class="form-label">Search Ticket</label>
        <select id="ticketSelect" class="form-select" style="width: 100%;">
            <option value="">Type to search tickets...</option>
        </select>
        <small class="text-muted">Search by ticket number or title. Only tickets in pending PR status are shown.</small>
    </div>

    <div class="d-grid">
        <button type="button" class="btn btn-primary" onclick="continueToVRForm()">
            <i class="fas fa-arrow-right me-2"></i> Continue with Selected Ticket
        </button>
    </div>
</div>
