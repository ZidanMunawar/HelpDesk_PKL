<form id="approvePRForm">
    @csrf
    <input type="hidden" id="approvePRId" value="{{ $vrId ?? '' }}">

    @php
        $user = auth()->user();
        $hasSignature = !empty($user->signature_path) && Storage::disk('public')->exists($user->signature_path);
    @endphp

    @if ($hasSignature)
        <div class="alert alert-info mb-3">
            <i class="fas fa-info-circle me-2"></i>
            You have a saved signature. Choose your preferred signing method.
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <input class="form-check-input" type="radio" name="signature_option" id="useSaved"
                            value="saved" checked>
                        <label class="form-check-label d-block" for="useSaved">
                            <i class="fas fa-save fa-3x text-primary mb-3"></i>
                            <h6>Use Saved Signature</h6>
                            <p class="small text-muted">Quick approve with your existing signature</p>
                            @if ($hasSignature && $user->signature_path)
                                <img src="{{ Storage::url($user->signature_path) }}" alt="Saved Signature"
                                    class="img-fluid mt-2" style="max-height: 50px;">
                            @endif
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <input class="form-check-input" type="radio" name="signature_option" id="createNew"
                            value="new">
                        <label class="form-check-label d-block" for="createNew">
                            <i class="fas fa-pen fa-3x text-warning mb-3"></i>
                            <h6>Create New Signature</h6>
                            <p class="small text-muted">Draw a new signature for this approval</p>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning mb-3">
            <i class="fas fa-exclamation-triangle me-2"></i>
            You don't have a saved signature. Please draw your signature below.
        </div>
    @endif

    <!-- New Signature Section -->
    <div id="newSignatureSection" style="{{ $hasSignature ? 'display: none;' : 'display: block;' }}">
        <div class="mb-3">
            <label class="form-label">Draw Your Signature <span class="text-danger">*</span></label>
            <div class="border rounded p-2 bg-white">
                <canvas id="approveSignatureCanvas" class="signature-canvas"
                    style="width: 100%; height: 150px; border: 1px solid #ddd;"></canvas>
            </div>
            <div class="mt-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSignature()">
                    <i class="fas fa-eraser me-1"></i> Clear
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="undoSignature()">
                    <i class="fas fa-undo me-1"></i> Undo
                </button>
            </div>
        </div>

        @if ($hasSignature)
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="save_signature" id="saveSignature" value="1">
                <label class="form-check-label" for="saveSignature">
                    <i class="fas fa-save me-1"></i> Save this signature to my profile
                </label>
            </div>

            <div id="passwordSection" style="display: none;" class="mb-3">
                <label class="form-label">Current Password <span class="text-danger">*</span></label>
                <input type="password" name="current_password" class="form-control"
                    placeholder="Enter your password to update signature">
                <small class="text-muted">Required to update your saved signature</small>
            </div>
        @endif
    </div>

    <div class="mb-3">
        <label class="form-label">Notes (Optional)</label>
        <textarea name="notes" class="form-control" rows="2" placeholder="Add any notes about this approval..."></textarea>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success">
            <i class="fas fa-check me-1"></i> Approve
        </button>
    </div>
</form>

<style>
    .signature-canvas {
        touch-action: none;
        background: white;
    }
</style>
