@php
    $user = auth()->user();
    $hasSignature = !empty($user->signature_path) && Storage::disk('public')->exists($user->signature_path);
@endphp

<form action="{{ route('vouchers.approve', $vr->id) }}" method="POST" id="approveForm">
    @csrf
    <input type="hidden" name="vr_id" value="{{ $vr->id }}">

    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        Approving VR #{{ $vr->vr_number }} for ticket #{{ $vr->ticket->ticket_number }}
    </div>

    @if ($hasSignature)
        <div class="mb-3">
            <label class="form-label">Signature Method</label>
            <div class="row g-2">
                <div class="col-6">
                    <div class="card h-100 text-center p-3" style="cursor: pointer;"
                        onclick="$('#useSaved').prop('checked', true).trigger('change')">
                        <input class="form-check-input" type="radio" name="signature_method" id="useSaved"
                            value="saved" checked style="display: none;">
                        <i class="fas fa-save fa-2x text-primary mb-2"></i>
                        <div class="fw-bold">Use Saved Signature</div>
                        <small class="text-muted">Quick approve</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card h-100 text-center p-3" style="cursor: pointer;"
                        onclick="$('#createNew').prop('checked', true).trigger('change')">
                        <input class="form-check-input" type="radio" name="signature_method" id="createNew"
                            value="new" style="display: none;">
                        <i class="fas fa-pen fa-2x text-warning mb-2"></i>
                        <div class="fw-bold">Create New Signature</div>
                        <small class="text-muted">Draw new signature</small>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div id="newSignatureSection" style="{{ $hasSignature ? 'display: none;' : '' }}">
        <div class="mb-3">
            <label class="form-label">Draw Your Signature *</label>
            <div class="text-center">
                <canvas id="approveSignatureCanvas" class="signature-canvas" width="300" height="200"></canvas>
            </div>
            <div class="text-center mt-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSignature()">
                    <i class="fas fa-eraser"></i> Clear
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="undoSignature()">
                    <i class="fas fa-undo"></i> Undo
                </button>
            </div>
            <input type="hidden" name="signature_data" id="signatureData">
        </div>

        @if ($hasSignature)
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="save_signature" id="saveSignature"
                        value="1">
                    <label class="form-check-label" for="saveSignature">
                        <i class="fas fa-save me-1"></i> Save this signature to my profile
                    </label>
                </div>
                <div id="passwordSection" style="display: none;" class="mt-2">
                    <label class="form-label">Current Password *</label>
                    <input type="password" name="current_password" class="form-control"
                        placeholder="Enter your password to update signature">
                    <small class="text-muted">Required to update your saved signature</small>
                </div>
            </div>
        @endif
    </div>

    <div class="mb-3">
        <label class="form-label">Notes (Optional)</label>
        <textarea name="notes" class="form-control" rows="2" placeholder="Add any notes about this approval..."></textarea>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success" id="submitApprove">
            <i class="fas fa-check me-1"></i> Approve
        </button>
    </div>
</form>

<script>
    let approveSignaturePad = null;

    $(document).ready(function() {
        const canvas = document.getElementById('approveSignatureCanvas');
        if (canvas) {
            canvas.width = 300;
            canvas.height = 200;
            approveSignaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgba(0,0,0,0)',
                penColor: 'rgb(0,0,0)',
                minWidth: 0.5,
                maxWidth: 2.5
            });
        }

        // Toggle signature method
        $('input[name="signature_method"]').on('change', function() {
            if ($(this).val() === 'new') {
                $('#newSignatureSection').show();
            } else {
                $('#newSignatureSection').hide();
            }
        });

        // Toggle password section
        $('#saveSignature').on('change', function() {
            $('#passwordSection').toggle($(this).is(':checked'));
        });

        // Form submit
        $('#approveForm').on('submit', function(e) {
            e.preventDefault();

            const signatureMethod = $('input[name="signature_method"]:checked').val();
            const formData = new FormData(this);

            if (signatureMethod === 'new') {
                if (!approveSignaturePad || approveSignaturePad.isEmpty()) {
                    toastr.error('Please draw your signature');
                    return;
                }
                formData.append('signature_data', approveSignaturePad.toDataURL());
                formData.append('use_saved_signature', '0');
            } else {
                formData.append('use_saved_signature', '1');
            }

            const submitBtn = $('#submitApprove');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html(
                '<i class="fas fa-spinner fa-spin"></i> Processing...');

            $.ajax({
                url: '{{ route('vouchers.approve', $vr->id) }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#approveVRModal').modal('hide');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Failed to approve VR';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    toastr.error(errorMsg);
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
    });

    function clearSignature() {
        if (approveSignaturePad) approveSignaturePad.clear();
    }

    function undoSignature() {
        if (approveSignaturePad) {
            const data = approveSignaturePad.toData();
            if (data.length > 0) {
                data.pop();
                approveSignaturePad.fromData(data);
            }
        }
    }
</script>
