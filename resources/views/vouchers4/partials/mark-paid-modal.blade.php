<form id="markPaidForm">
    @csrf
    <input type="hidden" name="vr_id" value="{{ $vr->id }}">

    <div class="alert alert-info mb-3">
        <i class="fas fa-info-circle me-2"></i>
        You are about to mark VR #{{ $vr->vr_number }} as paid.
    </div>

    <div class="alert alert-warning mb-3">
        <i class="fas fa-exclamation-triangle me-2"></i>
        Make sure payment has been processed before marking as paid.
    </div>

    <div class="mb-3">
        <label class="form-label">Payment Notes (Optional)</label>
        <textarea name="notes" class="form-control" rows="3"
            placeholder="Payment details, reference number, transaction date, etc..."></textarea>
        <small class="text-muted">This information will be stored with the VR record</small>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success" style="background: var(--success); border: none;">
            <i class="fas fa-check-double me-1"></i> Mark as Paid
        </button>
    </div>
</form>
