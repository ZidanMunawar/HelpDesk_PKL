<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Models\VoucherRequest;
use App\Models\VoucherAttachment;
use App\Models\Signature;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\TicketApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VoucherController extends Controller
{
    /**
     * Display a listing of voucher requests.
     */
    public function index()
    {
        return view('vouchers.index');
    }

    /**
     * Get PR list for AJAX with pagination and filtering.
     */
    public function list(Request $request)
    {
        $user = Auth::user();
        $filter = $request->get('filter', 'all');
        $perPage = $request->get('per_page', 15);

        $query = VoucherRequest::with(['ticket', 'creator', 'attachments'])
            ->orderBy('created_at', 'desc');

        // Apply filter
        if ($filter !== 'all') {
            $query->where('status', $filter);
        }

        // Filter berdasarkan role
        switch ($user->role) {
            case 'superadmin':
                // Lihat semua
                break;
            case 'admin_eng':
                $query->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)
                        ->orWhereIn('status', ['pending', 'admin_approved', 'gm_approved', 'paid', 'rejected']);
                });
                break;
            case 'om':
                $query->where(function ($q) use ($user) {
                    $q->where('status', 'admin_approved')
                        ->orWhere('om_approved_by', $user->id)
                        ->orWhere('created_by', $user->id);
                });
                break;
            case 'gm':
                $query->where(function ($q) use ($user) {
                    $q->where('status', 'om_approved')
                        ->orWhere('gm_approved_by', $user->id)
                        ->orWhere('created_by', $user->id);
                });
                break;
            case 'technician':
                $query->whereHas('ticket', function ($q) use ($user) {
                    $q->where('assigned_to', $user->id);
                });
                break;
            default:
                $query->whereHas('ticket', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
        }

        $vrs = $query->paginate($perPage);

        $data = [];
        foreach ($vrs as $vr) {
            $firstPhoto = $vr->attachments->first();
            $data[] = [
                'id' => $vr->id,
                'vr_number' => $vr->vr_number,
                'status' => $vr->status,
                'ticket_number' => $vr->ticket->ticket_number,
                'ticket_title' => $vr->ticket->title,
                'created_by_name' => $vr->creator->name,
                'created_at' => $vr->created_at->toISOString(),
                'photo_count' => $vr->attachments->count(),
                'first_photo' => $firstPhoto ? Storage::url($firstPhoto->file_path) : null,
                'created_by' => $vr->created_by,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $vrs->currentPage(),
                'last_page' => $vrs->lastPage(),
                'per_page' => $vrs->perPage(),
                'total' => $vrs->total(),
                'from' => $vrs->firstItem(),
                'to' => $vrs->lastItem(),
            ]
        ]);
    }

    /**
     * Show modal for ticket selection or VR creation.
     */
    public function createModal($ticketId = null)
    {
        if ($ticketId === 'ticket-select') {
            // Return ticket selection modal
            $html = view('vouchers.partials.ticket-selection')->render();

            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        }

        $ticket = Ticket::with(['category', 'priority'])->findOrFail($ticketId);

        // Check if ticket is in pending_vr status
        if ($ticket->status !== 'pending_vr') {
            return response()->json([
                'success' => false,
                'message' => 'Ticket is not in pending PR status. Current status: ' . $ticket->status
            ], 422);
        }

        // Check if there's already a pending PR for this ticket
        $existingPR = VoucherRequest::where('ticket_id', $ticket->id)
            ->whereIn('status', ['pending', 'admin_approved', 'om_approved', 'gm_approved'])
            ->first();

        if ($existingPR) {
            return response()->json([
                'success' => false,
                'message' => 'This ticket already has an active Purchase Request: #' . $existingPR->vr_number
            ], 422);
        }

        // Generate PR number
        $prNumber = $this->generatePRNumber();

        $html = view('vouchers.partials.create-form', compact('ticket', 'prNumber'))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Search tickets for PR creation (AJAX).
     */
    public function searchTickets(Request $request)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);

        $query = Ticket::with(['category', 'priority'])
            ->where('status', 'pending_vr')
            ->where(function ($q) use ($search) {
                $q->where('ticket_number', 'LIKE', "%{$search}%")
                    ->orWhere('title', 'LIKE', "%{$search}%");
            })
            ->orderBy('created_at', 'desc');

        $tickets = $query->paginate(10, ['*'], 'page', $page);

        $results = [];
        foreach ($tickets as $ticket) {
            $results[] = [
                'id' => $ticket->ticket_number,
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'title' => $ticket->title,
                'category' => $ticket->category->name,
                'priority' => $ticket->priority->name,
                'text' => $ticket->ticket_number . ' - ' . $ticket->title
            ];
        }

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => $tickets->hasMorePages()
            ]
        ]);
    }

    /**
     * Find ticket by number for manual search.
     */
    public function findTicketByNumber($ticketNumber)
    {
        $ticket = Ticket::where('ticket_number', $ticketNumber)
            ->where('status', 'pending_vr')
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found or not in pending PR status'
            ], 404);
        }

        // Check if there's already a pending PR for this ticket
        $existingPR = VoucherRequest::where('ticket_id', $ticket->id)
            ->whereIn('status', ['pending', 'admin_approved', 'om_approved', 'gm_approved'])
            ->first();

        if ($existingPR) {
            return response()->json([
                'success' => false,
                'message' => 'This ticket already has an active Purchase Request: #' . $existingPR->vr_number
            ], 422);
        }

        return response()->json([
            'success' => true,
            'ticket_id' => $ticket->id
        ]);
    }

    /**
     * Store a newly created voucher request with photos.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'admin_eng') {
            return response()->json([
                'success' => false,
                'message' => 'Only Admin Engineering can create Purchase Requests'
            ], 403);
        }

        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'vr_number' => 'required|string|unique:voucher_requests,vr_number',
            'notes' => 'nullable|string',
            'photos.*' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'photos' => 'nullable|array|max:5',
        ]);

        $ticket = Ticket::findOrFail($request->ticket_id);

        // Double check ticket status
        if ($ticket->status !== 'pending_vr') {
            return response()->json([
                'success' => false,
                'message' => 'Ticket is not in pending PR status'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Create voucher request
            $vr = VoucherRequest::create([
                'vr_number' => $request->vr_number,
                'ticket_id' => $request->ticket_id,
                'notes' => $request->notes,
                'status' => 'pending',
                'created_by' => $user->id,
            ]);

            // Upload photos
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $fileName = time() . '_' . Str::random(10) . '_' . $photo->getClientOriginalName();
                    $filePath = $photo->storeAs('vouchers/photos', $fileName, 'public');

                    VoucherAttachment::create([
                        'voucher_request_id' => $vr->id,
                        'file_name' => $photo->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => $photo->getClientMimeType(),
                        'file_size' => $photo->getSize(),
                        'uploaded_by' => $user->id,
                    ]);
                }
            }

            // Update ticket approval
            $approval = TicketApproval::firstOrCreate(['ticket_id' => $ticket->id]);
            $approval->update([
                'needs_vr' => true,
                'vr_created_at' => now(),
                'vr_created_by' => $user->id,
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'action' => 'vr_created',
                'description' => 'Purchase Request #' . $vr->vr_number . ' created with ' . ($request->hasFile('photos') ? count($request->file('photos')) : 0) . ' photos',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Add comment to ticket
            $ticket->comments()->create([
                'user_id' => $user->id,
                'comment' => "Purchase Request #{$vr->vr_number} created with " . ($request->hasFile('photos') ? count($request->file('photos')) : 0) . " photos.",
                'is_internal' => 0,
                'is_followup' => true,
            ]);

            // Notify other admin engineers
            $adminUsers = User::where('role', 'admin_eng')->where('status', 'active')->where('id', '!=', $user->id)->get();
            foreach ($adminUsers as $adminUser) {
                $this->sendNotification(
                    $adminUser,
                    $ticket,
                    $vr,
                    'New Purchase Request Created',
                    'Purchase Request #' . $vr->vr_number . ' has been created and needs approval',
                    'vr_request'
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase Request created successfully',
                'vr' => $vr
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Create PR error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create Purchase Request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show modal with PR details.
     */
    public function showModal($id)
    {
        $vr = VoucherRequest::with([
            'ticket',
            'creator',
            'attachments',
            'adminApprover',
            'omApprover',
            'gmApprover',
        ])->findOrFail($id);

        $html = view('vouchers.partials.view-details', compact('vr'))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Show approve modal.
     */
    public function approveModal(Request $request)
    {
        $vrId = $request->get('vr_id');
        $vr = VoucherRequest::findOrFail($vrId);
        $user = Auth::user();

        $hasSignature = !empty($user->signature_path) && Storage::disk('public')->exists($user->signature_path);

        $html = view('vouchers.partials.approve-modal', compact('vr', 'hasSignature'))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Approve voucher request with signature.
     */
    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        $vr = VoucherRequest::with('ticket')->findOrFail($id);

        // Check permission based on current status
        $canApprove = false;
        $nextStatus = '';
        $stage = 0;

        if ($user->role === 'admin_eng' && $vr->status === 'pending') {
            $canApprove = true;
            $nextStatus = 'admin_approved';
            $stage = 1;
        } elseif ($user->role === 'om' && $vr->status === 'admin_approved') {
            $canApprove = true;
            $nextStatus = 'om_approved';
            $stage = 2;
        } elseif ($user->role === 'gm' && $vr->status === 'om_approved') {
            $canApprove = true;
            $nextStatus = 'gm_approved';
            $stage = 3;
        }

        if (!$canApprove) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to approve this Purchase Request at its current stage'
            ], 403);
        }

        $request->validate([
            'signature_data' => 'required_without:use_saved_signature|string',
            'use_saved_signature' => 'nullable|string',
            'save_signature' => 'nullable|boolean',
            'current_password' => 'required_if:save_signature,1|nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Verify password if saving new signature
        if ($request->boolean('save_signature')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            $signaturePath = null;

            // Handle signature
            if ($request->use_saved_signature) {
                if (empty($user->signature_path) || !Storage::disk('public')->exists($user->signature_path)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You do not have a saved signature'
                    ], 422);
                }

                $signaturePath = 'signatures/vr/' . $vr->vr_number . '_' . $user->role . '_' . time() . '.png';
                Storage::disk('public')->copy($user->signature_path, $signaturePath);

            } else {
                $signaturePath = $this->saveSignature(
                    $request->signature_data,
                    $vr->vr_number,
                    $user->id,
                    $stage
                );

                if ($request->boolean('save_signature')) {
                    if ($user->signature_path) {
                        Storage::disk('public')->delete($user->signature_path);
                    }

                    $user->update([
                        'signature_path' => $signaturePath,
                        'has_signature' => true,
                        'signature_updated_at' => now()
                    ]);
                }
            }

            // Create signature record
            Signature::create([
                'ticket_id' => $vr->ticket_id,
                'user_id' => $user->id,
                'signature_type' => 'approver',
                'stage' => $stage + 6,
                'signature_path' => $signaturePath,
                'signed_at' => now(),
                'ip_address' => $request->ip(),
            ]);

            // Update VR status
            $updateData = [
                'status' => $nextStatus,
                'notes' => $vr->notes . ($request->notes ? "\n\n" . $user->role . " notes: " . $request->notes : '')
            ];

            switch ($user->role) {
                case 'admin_eng':
                    $updateData['admin_approved'] = true;
                    $updateData['admin_approved_by'] = $user->id;
                    $updateData['admin_approved_at'] = now();
                    break;
                case 'om':
                    $updateData['om_approved'] = true;
                    $updateData['om_approved_by'] = $user->id;
                    $updateData['om_approved_at'] = now();
                    break;
                case 'gm':
                    $updateData['gm_approved'] = true;
                    $updateData['gm_approved_by'] = $user->id;
                    $updateData['gm_approved_at'] = now();
                    break;
            }

            $vr->update($updateData);

            // If GM approved, update ticket status back to in_progress
            if ($user->role === 'gm') {
                $vr->ticket->update([
                    'status' => 'in_progress',
                    'current_stage' => 4,
                ]);

                $approval = TicketApproval::firstOrCreate(['ticket_id' => $vr->ticket_id]);
                $approval->update([
                    'needs_vr' => false,
                ]);

                if ($vr->ticket->assigned_to) {
                    $technician = User::find($vr->ticket->assigned_to);
                    if ($technician) {
                        $this->sendNotification(
                            $technician,
                            $vr->ticket,
                            $vr,
                            'PR Approved - Work Can Continue',
                            'Purchase Request #' . $vr->vr_number . ' has been fully approved. You can continue work on ticket #' . $vr->ticket->ticket_number,
                            'success'
                        );
                    }
                }
            }

            // Add comment to ticket
            $vr->ticket->comments()->create([
                'user_id' => $user->id,
                'comment' => "Purchase Request #{$vr->vr_number} approved by " . ucfirst($user->role) .
                    ($request->notes ? "\nNotes: " . $request->notes : ''),
                'is_internal' => 0,
                'is_followup' => true,
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $vr->ticket_id,
                'action' => 'vr_' . $user->role . '_approved',
                'description' => 'Purchase Request #' . $vr->vr_number . ' approved by ' . ucfirst($user->role),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Notify next approver
            $this->notifyNextApprover($vr, $user->role);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase Request approved successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approve PR error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve Purchase Request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject voucher request.
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        $vr = VoucherRequest::with('ticket')->findOrFail($id);

        $canReject = false;

        if ($user->role === 'admin_eng' && $vr->status === 'pending') {
            $canReject = true;
        } elseif ($user->role === 'om' && $vr->status === 'admin_approved') {
            $canReject = true;
        } elseif ($user->role === 'gm' && $vr->status === 'om_approved') {
            $canReject = true;
        }

        if (!$canReject) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to reject this Purchase Request'
            ], 403);
        }

        $request->validate([
            'rejection_reason' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $vr->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'notes' => $vr->notes . "\n\nRejected by " . ucfirst($user->role) .
                    "\nReason: " . $request->rejection_reason .
                    ($request->notes ? "\nNotes: " . $request->notes : '')
            ]);

            $vr->ticket->comments()->create([
                'user_id' => $user->id,
                'comment' => "Purchase Request #{$vr->vr_number} rejected by " . ucfirst($user->role) .
                    "\nReason: " . $request->rejection_reason,
                'is_internal' => 0,
                'is_followup' => true,
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $vr->ticket_id,
                'action' => 'vr_' . $user->role . '_rejected',
                'description' => 'Purchase Request #' . $vr->vr_number . ' rejected by ' . ucfirst($user->role),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $this->sendNotification(
                $vr->creator,
                $vr->ticket,
                $vr,
                'Purchase Request Rejected',
                'Your Purchase Request #' . $vr->vr_number . ' was rejected by ' . ucfirst($user->role) .
                "\nReason: " . $request->rejection_reason,
                'rejection'
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase Request rejected successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reject PR error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject Purchase Request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark voucher request as paid.
     */
    public function markPaid(Request $request, $id)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['admin_eng', 'superadmin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only Admin Engineering or Superadmin can mark Purchase Request as paid'
            ], 403);
        }

        $vr = VoucherRequest::with('ticket')->findOrFail($id);

        if ($vr->status !== 'gm_approved') {
            return response()->json([
                'success' => false,
                'message' => 'Purchase Request must be GM approved before marking as paid'
            ], 422);
        }

        $request->validate([
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $vr->update([
                'status' => 'paid',
                'notes' => $vr->notes . "\n\nMarked as paid by " . $user->name .
                    ($request->notes ? "\nPayment notes: " . $request->notes : '')
            ]);

            $vr->ticket->comments()->create([
                'user_id' => $user->id,
                'comment' => "Purchase Request #{$vr->vr_number} marked as paid" .
                    ($request->notes ? "\nPayment notes: " . $request->notes : ''),
                'is_internal' => 0,
                'is_followup' => true,
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $vr->ticket_id,
                'action' => 'vr_paid',
                'description' => 'Purchase Request #' . $vr->vr_number . ' marked as paid',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $this->sendNotification(
                $vr->creator,
                $vr->ticket,
                $vr,
                'Purchase Request Paid',
                'Purchase Request #' . $vr->vr_number . ' has been marked as paid',
                'success'
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase Request marked as paid successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Mark paid PR error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark Purchase Request as paid: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete voucher request.
     */
    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        $vr = VoucherRequest::with('ticket')->findOrFail($id);

        $canDelete = false;

        if ($user->role === 'superadmin') {
            $canDelete = true;
            if (!$request->has('password') || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password verification failed'
                ], 422);
            }
        } elseif ($vr->created_by === $user->id && in_array($vr->status, ['pending', 'rejected'])) {
            $canDelete = true;
        }

        if (!$canDelete) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to delete this Purchase Request'
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Delete attachments
            foreach ($vr->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
                $attachment->delete();
            }

            $vr->ticket->comments()->create([
                'user_id' => $user->id,
                'comment' => "Purchase Request #{$vr->vr_number} deleted by " . $user->name,
                'is_internal' => 0,
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $vr->ticket_id,
                'action' => 'vr_deleted',
                'description' => 'Purchase Request #' . $vr->vr_number . ' deleted',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $vr->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase Request deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete PR error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete Purchase Request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Print PR (dummy for now - will implement PDF later).
     */
    public function print($id)
    {
        $vr = VoucherRequest::with(['ticket', 'creator', 'attachments'])->findOrFail($id);

        // TODO: Implement PDF generation
        return response()->json([
            'success' => true,
            'message' => 'Print feature coming soon'
        ]);
    }

    /**
     * Verify password for signature update.
     */
    public function verifyPassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password verified successfully'
        ]);
    }

    /**
     * Generate unique PR number.
     */
    private function generatePRNumber()
    {
        $year = date('Y');
        $month = date('m');

        $lastPR = VoucherRequest::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastPR) {
            $lastNumber = intval(substr($lastPR->vr_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return 'PR-' . $year . $month . '-' . $newNumber;
    }

    /**
     * Save signature from data URL.
     */
    private function saveSignature($signatureData, $prNumber, $userId, $stage)
    {
        if (!preg_match('#^data:image/\w+;base64,#i', $signatureData)) {
            throw new \Exception('Invalid signature data URL format');
        }

        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signatureData));

        if ($imageData === false) {
            throw new \Exception('Failed to decode base64 signature data');
        }

        $image = imagecreatefromstring($imageData);
        if (!$image) {
            throw new \Exception('Failed to create image from data');
        }

        $width = imagesx($image);
        $height = imagesy($image);

        $targetWidth = 300;
        $targetHeight = 200;

        $newImage = imagecreatetruecolor($targetWidth, $targetHeight);

        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
        imagefill($newImage, 0, 0, $transparent);

        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        ob_start();
        imagepng($newImage);
        $imageData = ob_get_clean();

        imagedestroy($image);
        imagedestroy($newImage);

        $fileName = 'pr_signature_' . $prNumber . '_stage' . $stage . '_user' . $userId . '_' . time() . '.png';
        $filePath = 'signatures/pr/' . $fileName;

        Storage::disk('public')->makeDirectory('signatures/pr', 0755, true);
        $saved = Storage::disk('public')->put($filePath, $imageData);

        if (!$saved) {
            throw new \Exception('Failed to save signature file');
        }

        return $filePath;
    }

    /**
     * Send notification to next approver.
     */
    private function notifyNextApprover($vr, $currentRole)
    {
        $nextRole = '';
        $statusMessage = '';

        switch ($currentRole) {
            case 'admin_eng':
                $nextRole = 'om';
                $statusMessage = 'pending OM approval';
                break;
            case 'om':
                $nextRole = 'gm';
                $statusMessage = 'pending GM approval';
                break;
            default:
                return;
        }

        $nextApprovers = User::where('role', $nextRole)->where('status', 'active')->get();

        foreach ($nextApprovers as $approver) {
            $this->sendNotification(
                $approver,
                $vr->ticket,
                $vr,
                'Purchase Request Needs Your Approval',
                'Purchase Request #' . $vr->vr_number . ' for ticket #' . $vr->ticket->ticket_number . ' is ' . $statusMessage,
                'approval'
            );
        }
    }

    /**
     * Send notification (in-app and email).
     */
    private function sendNotification($user, $ticket, $vr, $title, $message, $type = 'info')
    {
        try {
            Notification::create([
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'is_read' => false,
            ]);

            if (config('mail.mailers.smtp.host')) {
                try {
                    // Mail::to($user->email)->queue(new PurchaseRequestNotification($user, $ticket, $vr, $title, $message, $type));
                } catch (\Exception $e) {
                    Log::warning('Email notification failed: ' . $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            Log::error('Notification creation failed: ' . $e->getMessage());
        }
    }
}
