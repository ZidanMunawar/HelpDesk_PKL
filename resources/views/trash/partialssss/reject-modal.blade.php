<div class="modal-header">
    <h5 class="modal-title">
        <i class="fas fa-times-circle me-2"></i> Reject Voucher Request
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>
<form id="rejectVRForm">
    @csrf
    <input type="hidden" id="rejectVRId" value="{{ $vrId ?? '' }}">
    <div class="modal-body">
        <div class="alert alert-danger mb-3" style="border-left: 4px solid var(--danger);">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Are you sure you want to reject this VR?
        </div>

        <div class="mb-3">
            <label class="form-label required"><i class="fas fa-comment me-1"></i> Rejection Reason *</label>
            <textarea name="rejection_reason" class="form-control" rows="3"
                placeholder="Please provide a clear reason for rejection..." required></textarea>
            <small class="text-muted">This will be visible to the VR creator</small>
        </div>

        <div class="mb-3">
            <label class="form-label"><i class="fas fa-sticky-note me-1"></i> Additional Notes (Optional)</label>
            <textarea name="notes" class="form-control" rows="2" placeholder="Any additional notes..."></textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-danger" style="background: var(--danger); border: none;">
            <i class="fas fa-times me-1"></i> Reject VR
        </button>
    </div>
</form>
