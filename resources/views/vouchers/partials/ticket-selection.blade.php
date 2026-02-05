<div class="ticket-selection-form">
    <div class="alert alert-info mb-3">
        <i class="fas fa-info-circle me-2"></i>
        Select a ticket that requires a Voucher Request (VR). Only tickets in <strong>Pending VR</strong> status will be
        shown.
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
            <button type="button" class="btn btn-primary" onclick="continueToVRForm()">
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
