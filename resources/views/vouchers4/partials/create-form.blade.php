<form id="createVRForm" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
    <input type="hidden" name="vr_number" value="{{ $vrNumber }}">

    <style>
        .photo-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .photo-preview-item {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #ddd;
        }

        .photo-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-preview-item .remove-photo {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
        }

        .photo-preview-item .remove-photo:hover {
            background: #c82333;
        }

        .item-table-container {
            overflow-x: auto;
        }

        .item-table {
            min-width: 700px;
        }
    </style>

    <div class="alert alert-success mb-4">
        <i class="fas fa-check-circle me-2"></i>
        Creating VR for Ticket: <strong>#{{ $ticket->ticket_number }}</strong> - {{ $ticket->title }}
    </div>

    <!-- VR Information -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label class="form-label text-muted small text-uppercase">VR Number</label>
            <div class="fw-bold fs-5 text-primary">{{ $vrNumber }}</div>
        </div>
        <div class="col-md-6">
            <label class="form-label text-muted small text-uppercase">Date</label>
            <div class="fw-bold">{{ now()->format('d F Y, H:i') }}</div>
        </div>
    </div>

    <!-- Ticket Information Card -->
    <div class="card mb-4 border">
        <div class="card-header bg-light">
            <h6 class="mb-0"><i class="fas fa-ticket-alt me-2"></i> Ticket Information</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-2">
                        <small class="text-muted d-block">Ticket Number</small>
                        <strong>#{{ $ticket->ticket_number }}</strong>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Title</small>
                        <span>{{ $ticket->title }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2">
                        <small class="text-muted d-block">Category</small>
                        <span class="badge bg-secondary">{{ $ticket->category->name }}</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Priority</small>
                        <span class="badge"
                            style="background-color: {{ $ticket->priority->color }}">{{ $ticket->priority->name }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- VR Notes -->
    <div class="mb-4">
        <label class="form-label fw-semibold">VR Notes <span class="text-muted fw-normal">(Optional)</span></label>
        <textarea name="notes" class="form-control" rows="3" placeholder="Add any notes about this VR request..."></textarea>
    </div>

    <!-- Photos Upload Section (REPLACES ITEMS) -->
    <div class="mb-4">
        <label class="form-label fw-semibold">Photos / Attachments <span class="text-danger">*</span></label>
        <div class="alert alert-info mb-3 py-2">
            <i class="fas fa-camera me-2"></i> Upload photos of items needed for this VR
        </div>
        <input type="file" name="photos[]" id="vrPhotos" class="form-control" multiple accept="image/*" required>
        <div class="form-text mt-2">You can upload multiple photos. Supported formats: JPG, PNG, GIF, WEBP. Max 5MB per
            file.</div>

        <div id="photoPreviewSection" style="display: none;" class="mt-3">
            <label class="form-label small text-muted">Preview:</label>
            <div id="photoPreviewContainer" class="photo-preview-container"></div>
        </div>
    </div>

    <div class="alert alert-warning mt-3">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Note:</strong> Items and total amount will be managed through the attached photos. After approval, Admin
        Engineering will process the purchase based on these photos.
    </div>

    <div class="d-flex gap-3 mt-4">
        <button type="submit" class="btn btn-success flex-grow-1" style="background: #28a745; border: none;">
            <i class="fas fa-save me-2"></i> Create Voucher Request
        </button>
        <button type="button" class="btn btn-secondary" onclick="openCreateVRModal()">
            <i class="fas fa-arrow-left me-2"></i> Back
        </button>
    </div>
</form>
