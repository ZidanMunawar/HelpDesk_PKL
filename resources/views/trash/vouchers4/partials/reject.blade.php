<form action="{{ route('vouchers.reject', $vr->id) }}" method="POST">
    @csrf
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle me-2"></i>
        Are you sure you want to reject VR #{{ $vr->vr_number }}?
    </div>

    <div class="mb-3">
        <label class="form-label required">Rejection Reason *</label>
        <textarea name="rejection_reason" class="form-control" rows="3"
            placeholder="Please provide a clear reason for rejection..." required></textarea>
        <small class="text-muted">This will be visible to the VR creator.</small>
    </div>

    <div class="mb-3">
        <label class="form-label">Additional Notes (Optional)</label>
        <textarea name="notes" class="form-control" rows="2" placeholder="Any additional notes..."></textarea>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-danger">
            <i class="fas fa-times me-1"></i> Reject VR
        </button>
    </div>
</form>
