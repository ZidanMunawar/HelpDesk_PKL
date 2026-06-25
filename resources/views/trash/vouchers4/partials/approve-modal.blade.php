<form id="approveVRForm">
    @csrf
    <input type="hidden" name="vr_id" value="{{ $vr->id }}">

    <div class="alert alert-info mb-3">
        <i class="fas fa-info-circle me-2"></i>
        You are about to approve VR #{{ $vr->vr_number }} for ticket #{{ $vr->ticket->ticket_number }}
    </div>

    @if ($hasSignature)
        <!-- Signature Options -->
        <div class="alert alert-warning mb-3">
            <i class="fas fa-exclamation-triangle me-2"></i>
            You have a saved signature. Choose your preferred signing method.
        </div>

        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card h-100 border-2" style="cursor: pointer;"
                    onclick="$('#useSaved').prop('checked', true); $('#newSignatureSection').hide(); $('#passwordSection').hide();">
                    <div class="card-body text-center">
                        <input class="form-check-input" type="radio" name="signature_option" id="useSaved"
                            value="saved" checked style="display: none;">
                        <i class="fas fa-save fa-3x text-primary mb-3"></i>
                        <h6>Use Saved Signature</h6>
                        <p class="small text-muted">Quick approve with your existing signature</p>
                        @if ($hasSignature)
                            <img src="{{ Storage::url(auth()->user()->signature_path) }}" alt="Saved Signature"
                                class="img-fluid mt-2" style="max-height: 50px;">
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card h-100 border-2" style="cursor: pointer;"
                    onclick="$('#createNew').prop('checked', true); $('#newSignatureSection').show();">
                    <div class="card-body text-center">
                        <input class="form-check-input" type="radio" name="signature_option" id="createNew"
                            value="new" style="display: none;">
                        <i class="fas fa-pen fa-3x text-warning mb-3"></i>
                        <h6>Create New Signature</h6>
                        <p class="small text-muted">Draw a new signature for this approval</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- New Signature Canvas -->
    <div id="newSignatureSection" style="{{ $hasSignature ? 'display: none;' : 'display: block;' }}">
        <div class="mb-3">
            <label class="form-label">Draw Your Signature *</label>
            <div class="signature-canvas-container border rounded mb-3 p-2 text-center"
                style="background: transparent;">
                <canvas id="approveSignatureCanvas" width="300" height="200"
                    style="background: transparent; max-width: 100%; height: auto;"></canvas>
            </div>
            <div class="signature-actions d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearSignature()">
                    <i class="fas fa-eraser me-1"></i> Clear
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="undoSignature()">
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
                <label class="form-label">Current Password *</label>
                <input type="password" name="current_password" class="form-control"
                    placeholder="Enter your password to update signature">
                <small class="text-muted">Required to update your saved signature</small>
            </div>
        @endif
    </div>

    <!-- Notes -->
    <div class="mb-3">
        <label class="form-label">Notes (Optional)</label>
        <textarea name="notes" class="form-control" rows="3" placeholder="Add any notes about this approval..."></textarea>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success" style="background: var(--success); border: none;">
            <i class="fas fa-check me-1"></i> Approve
        </button>
    </div>
</form>

<script>
    // Initialize signature pad when modal is shown
    $('#approveVRModal').one('shown.bs.modal', function() {
        const canvas = document.getElementById('approveSignatureCanvas');
        if (canvas && typeof SignaturePad !== 'undefined') {
            canvas.width = 300;
            canvas.height = 200;
            window.signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgba(0,0,0,0)',
                penColor: 'rgb(0, 0, 0)',
                minWidth: 0.5,
                maxWidth: 2.5,
                throttle: 16
            });
        }
    });

    // Toggle signature options
    $('input[name="signature_option"]').on('change', function() {
        if ($(this).val() === 'new') {
            $('#newSignatureSection').show();
            $('#passwordSection').show();
        } else {
            $('#newSignatureSection').hide();
            $('#passwordSection').hide();
        }
    });

    $('#saveSignature').on('change', function() {
        $('#passwordSection').toggle($(this).is(':checked'));
    });

    function clearSignature() {
        if (window.signaturePad) window.signaturePad.clear();
    }

    function undoSignature() {
        if (window.signaturePad) {
            const data = window.signaturePad.toData();
            if (data.length > 0) {
                data.pop();
                window.signaturePad.fromData(data);
            }
        }
    }
</script>
