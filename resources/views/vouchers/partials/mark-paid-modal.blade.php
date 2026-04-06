<div class="modal-header">
    <h5 class="modal-title">
        <i class="fas fa-check-double me-2"></i> Mark as Paid
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>
<form id="markPaidForm">
    @csrf
    <input type="hidden" id="markPaidVRId" value="{{ $vrId ?? '' }}">
    <div class="modal-body">
        <div class="alert alert-info mb-3" style="border-left: 4px solid var(--orange);">
            <i class="fas fa-info-circle me-2" style="color: var(--orange);"></i>
            This will mark the VR as paid. Make sure payment has been processed.
        </div>

        <div class="mb-3">
            <label class="form-label"><i class="fas fa-sticky-note me-1"></i> Payment Notes (Optional)</label>
            <textarea name="notes" class="form-control" rows="3"
                placeholder="Payment details, reference number, transaction date, etc..."></textarea>
            <small class="text-muted">This information will be stored with the VR record</small>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary" style="background: var(--navy); border: none;">
            <i class="fas fa-check-double me-1"></i> Mark as Paid
        </button>
    </div>
</form>
