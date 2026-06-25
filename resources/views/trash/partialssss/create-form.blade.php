<form id="createPRForm" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
    <input type="hidden" name="vr_number" value="{{ $prNumber }}">

    <div class="alert alert-success mb-3">
        <i class="fas fa-check-circle me-2"></i>
        Creating Purchase Request for Ticket: <strong>#{{ $ticket->ticket_number }}</strong> - {{ $ticket->title }}
    </div>

    <!-- PR Information -->
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">PR Number</label>
            <input type="text" class="form-control" value="{{ $prNumber }}" readonly>
        </div>
        <div class="col-md-6">
            <label class="form-label">Date</label>
            <input type="text" class="form-control" value="{{ now()->format('d M Y, H:i') }}" readonly>
        </div>
    </div>

    <!-- Ticket Information -->
    <div class="card mb-3">
        <div class="card-header bg-light">
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

    <!-- PR Notes -->
    <div class="mb-3">
        <label class="form-label">PR Notes (Optional)</label>
        <textarea name="notes" class="form-control" rows="3" placeholder="Add any notes about this purchase request..."></textarea>
    </div>

    <!-- Photos Upload -->
    <div class="mb-3">
        <label class="form-label">Photos (Max 5)</label>
        <input type="file" name="photos[]" id="photosInput" class="form-control" multiple
            accept="image/jpeg,image/jpg,image/png">
        <small class="text-muted">Maximum 5 photos. Allowed formats: JPG, JPEG, PNG. Max 5MB per photo.</small>

        <!-- Preview Container -->
        <div id="photosPreview" class="mt-3 d-flex flex-wrap gap-2"></div>
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save me-2"></i> Create Purchase Request
        </button>
        <button type="button" class="btn btn-secondary" onclick="openCreatePRModal()">
            <i class="fas fa-arrow-left me-2"></i> Back to Ticket Selection
        </button>
    </div>
</form>

<style>
    .photo-preview-item {
        position: relative;
        display: inline-block;
    }

    .btn-remove-photo {
        position: absolute;
        top: -5px;
        right: -5px;
        background: red;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 12px;
        border: none;
        cursor: pointer;
    }
</style>
