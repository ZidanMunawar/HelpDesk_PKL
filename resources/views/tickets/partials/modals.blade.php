<!-- New Signature Modal (Password Verification) -->
<div class="modal fade" id="newSignatureModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-navy text-white">
                <h5 class="modal-title">
                    <i class="fas fa-key me-2"></i> Create New Signature
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="newSignatureForm">
                @csrf
                <input type="hidden" name="action_type" id="actionType">
                <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">

                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        You already have a saved signature. Creating a new one will replace the old signature.
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Enter your password to confirm</label>
                        <input type="password" name="current_password" class="form-control"
                            placeholder="Your account password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-pen me-1"></i> Proceed
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Approve Modal -->
<div class="modal fade" id="quickApproveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-bolt me-2"></i> Quick Approve
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickApproveForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        You are about to approve this ticket using your saved signature.
                    </div>
                    <div class="text-center mb-3">
                        @if ($user->signature_path && Storage::disk('public')->exists($user->signature_path))
                            <img src="{{ Storage::url($user->signature_path) }}" alt="Your Signature"
                                style="max-height: 100px; border: 1px solid #ddd; padding: 5px;">
                            <p class="small mt-2">Your saved signature will be used</p>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve with Saved Signature</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Ticket Modal -->
<div class="modal fade" id="deleteTicketModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-trash-alt me-2"></i> Delete Ticket
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteTicketForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>WARNING:</strong> This action cannot be undone!
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Enter your password to confirm</label>
                        <input type="password" name="password" class="form-control" placeholder="Your account password"
                            required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Permanently</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Receive Modal -->
<div class="modal fade" id="receiveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-navy text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i> Receive Ticket
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="receiveForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        You are about to receive this ticket. Please provide your signature.
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Draw Your Signature</label>
                        <div class="signature-canvas-container border rounded mb-3">
                            <canvas id="receiveSignatureCanvas" class="modal-signature-canvas" width="500"
                                height="200"></canvas>
                        </div>
                        <div class="signature-actions">
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                onclick="clearReceiveSignature()">
                                <i class="fas fa-eraser me-1"></i> Clear
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                onclick="undoReceiveSignature()">
                                <i class="fas fa-undo me-1"></i> Undo
                            </button>
                        </div>
                    </div>

                    @if ($canSaveSignature)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="save_signature"
                                id="saveReceiveSignature" value="1" {{ !$hasSignature ? 'checked' : '' }}>
                            <label class="form-check-label" for="saveReceiveSignature">
                                <i class="fas fa-save me-1"></i> Save this signature to my profile
                            </label>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-navy">Receive Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- OM Approve Modal -->
<div class="modal fade" id="omApproveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-thumbs-up me-2"></i> OM Approve Ticket
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="omApproveForm">
                @csrf
                <input type="hidden" name="action" value="approve">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        You are about to approve this ticket. Please provide your signature.
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Draw Your Signature</label>
                        <div class="signature-canvas-container border rounded mb-3">
                            <canvas id="omApproveSignatureCanvas" class="modal-signature-canvas" width="500"
                                height="200"></canvas>
                        </div>
                        <div class="signature-actions">
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                onclick="clearOmApproveSignature()">
                                <i class="fas fa-eraser me-1"></i> Clear
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                onclick="undoOmApproveSignature()">
                                <i class="fas fa-undo me-1"></i> Undo
                            </button>
                        </div>
                    </div>

                    @if ($canSaveSignature)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="save_signature"
                                id="saveOmApproveSignature" value="1" {{ !$hasSignature ? 'checked' : '' }}>
                            <label class="form-check-label" for="saveOmApproveSignature">
                                <i class="fas fa-save me-1"></i> Save this signature to my profile
                            </label>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- OM Reject Modal -->
<div class="modal fade" id="omRejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-thumbs-down me-2"></i> OM Reject Ticket
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="omRejectForm">
                @csrf
                <input type="hidden" name="action" value="reject">
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Are you sure you want to reject this ticket?
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Rejection Reason</label>
                        <textarea name="rejection_reason" class="form-control" rows="3"
                            placeholder="Please provide reason for rejection..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Complete Modal -->
<div class="modal fade" id="completeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-double me-2"></i> Mark Work as Complete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="completeForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        You are about to mark this work as complete.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Completion Notes (Optional)</label>
                        <textarea name="completion_notes" id="completion_notes" class="form-control" rows="3"
                            placeholder="Describe what work was done, parts replaced, etc."></textarea>
                        <div class="form-text">Optional - can be left blank if no notes needed.</div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_followup" id="is_followup" class="form-check-input"
                            value="1">
                        <label class="form-check-label" for="is_followup">
                            <i class="fas fa-arrow-right me-1 text-warning"></i>
                            Mark as Follow-up (requires action from Admin)
                        </label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Your Signature</label>
                        <div class="signature-canvas-container border rounded mb-3">
                            <canvas id="completeSignatureCanvas" class="modal-signature-canvas" width="500"
                                height="200"></canvas>
                        </div>
                        <div class="signature-actions">
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                onclick="clearCompleteSignature()">
                                <i class="fas fa-eraser me-1"></i> Clear
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                onclick="undoCompleteSignature()">
                                <i class="fas fa-undo me-1"></i> Undo
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Submit Completion</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- VR Modal -->
<div class="modal fade" id="vrModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">
                    <i class="fas fa-file-invoice-dollar me-2"></i> Request VR
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="vrForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Requesting a VR will pause this ticket until the VR is approved.
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Reason for VR</label>
                        <textarea name="vr_reason" class="form-control" rows="3"
                            placeholder="Explain why you need a voucher request (parts needed, materials, etc.)" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Estimated Cost (Optional)</label>
                        <input type="number" name="estimated_cost" class="form-control" placeholder="Rp"
                            min="0" step="1000">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Required Items (Optional)</label>
                        <textarea name="required_items" class="form-control" rows="2"
                            placeholder="List items needed (e.g., 2x Light bulbs, 1x Switch)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Request VR</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- User Check Accept Modal -->
<div class="modal fade" id="userCheckAcceptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-clipboard-check me-2"></i> Accept Completion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="userCheckAcceptForm">
                @csrf
                <input type="hidden" name="action" value="accept">
                <div class="modal-body">
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        Are you satisfied with the completed work?
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Your Signature</label>
                        <div class="signature-canvas-container border rounded mb-3">
                            <canvas id="userAcceptSignatureCanvas" class="modal-signature-canvas" width="500"
                                height="200"></canvas>
                        </div>
                        <div class="signature-actions">
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                onclick="clearUserAcceptSignature()">
                                <i class="fas fa-eraser me-1"></i> Clear
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                onclick="undoUserAcceptSignature()">
                                <i class="fas fa-undo me-1"></i> Undo
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Accept Completion</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- User Check Reject Modal -->
<div class="modal fade" id="userCheckRejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle me-2"></i> Reject Completion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="userCheckRejectForm">
                @csrf
                <input type="hidden" name="action" value="reject">
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Please explain why you are rejecting the completion.
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Rejection Reason</label>
                        <textarea name="rejection_reason" class="form-control" rows="3"
                            placeholder="Please explain what needs to be improved..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Completion</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- GM Approve Modal -->
<div class="modal fade" id="gmApproveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-gavel me-2"></i> GM Approve Ticket
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="gmApproveForm">
                @csrf
                <input type="hidden" name="action" value="approve">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        You are about to give final approval to this ticket.
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Draw Your Signature</label>
                        <div class="signature-canvas-container border rounded mb-3">
                            <canvas id="gmApproveSignatureCanvas" class="modal-signature-canvas" width="500"
                                height="200"></canvas>
                        </div>
                        <div class="signature-actions">
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                onclick="clearGmApproveSignature()">
                                <i class="fas fa-eraser me-1"></i> Clear
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                onclick="undoGmApproveSignature()">
                                <i class="fas fa-undo me-1"></i> Undo
                            </button>
                        </div>
                    </div>

                    @if ($canSaveSignature)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="save_signature"
                                id="saveGmApproveSignature" value="1" {{ !$hasSignature ? 'checked' : '' }}>
                            <label class="form-check-label" for="saveGmApproveSignature">
                                <i class="fas fa-save me-1"></i> Save this signature to my profile
                            </label>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- GM Reject Modal -->
<div class="modal fade" id="gmRejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-ban me-2"></i> GM Reject Ticket
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="gmRejectForm">
                @csrf
                <input type="hidden" name="action" value="reject">
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Are you sure you want to reject this ticket?
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Rejection Reason</label>
                        <textarea name="rejection_reason" class="form-control" rows="3"
                            placeholder="Please provide reason for rejection..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Modal -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-navy text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus me-2"></i> Assign Technician
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="assignForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Select Technician</label>
                        <select name="assigned_to" class="form-select" required>
                            <option value="">-- Select Technician --</option>
                            @foreach ($technicians as $technician)
                                <option value="{{ $technician->id }}"
                                    {{ $ticket->assigned_to == $technician->id ? 'selected' : '' }}>
                                    {{ $technician->name }}
                                    @if ($technician->department)
                                        ({{ $technician->department->name }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Due Date (Optional)</label>
                        <input type="datetime-local" name="due_date" class="form-control"
                            value="{{ $ticket->due_date ? $ticket->due_date->format('Y-m-d\TH:i') : '' }}">
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="notify_user" id="notifyUser"
                            value="1" checked>
                        <label class="form-check-label" for="notifyUser">
                            <i class="fas fa-bell me-1"></i> Notify technician
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-navy">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-ban me-2"></i> Cancel Ticket
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="cancelForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Are you sure you want to cancel this ticket? This action cannot be undone.
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Cancellation Reason</label>
                        <textarea name="cancellation_reason" class="form-control" rows="3"
                            placeholder="Please provide reason for cancellation..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Go Back</button>
                    <button type="submit" class="btn btn-danger">Cancel Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>
