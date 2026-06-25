<form action="{{ route('vouchers.mark-paid', $vr->id) }}" method="POST">
    @csrf
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        Mark VR #{{ $vr->vr_number }} as paid. This will complete the voucher request.
    </div>

    <div class="mb-3">
        <label class="form-label">Payment Notes (Optional)</label>
        <textarea name="notes" class="form-control" rows="3"
            placeholder="Payment details, reference number, transaction date, etc..."></textarea>
        <small class="text-muted">This information will be stored with the VR record.</small>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-check-double me-1"></i> Mark as Paid
        </button>
    </div>
</form>
