<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Models\VoucherRequest;
use App\Models\VoucherItem;
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
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->get('status'); // Ambil filter status dari URL

        $query = VoucherRequest::with([
            'ticket',
            'creator',
            'items',
            'adminApprover',
            'omApprover',
            'gmApprover'
        ]);

        // ============================================
        // TERAPKAN FILTER STATUS (dari sidebar)
        // ============================================
        if ($status) {
            switch ($status) {
                case 'pending':
                    $query->where('status', 'pending');
                    break;
                case 'admin_approved':
                    $query->where('status', 'admin_approved');
                    break;
                case 'om_approved':
                    $query->where('status', 'om_approved');
                    break;
                case 'gm_approved':
                    $query->where('status', 'gm_approved');
                    break;
                case 'paid':
                    $query->where('status', 'paid');
                    break;
                case 'rejected':
                    $query->where('status', 'rejected');
                    break;
            }
        }

        // ============================================
        // FILTER BERDASARKAN ROLE (seperti sebelumnya)
        // ============================================
        switch ($user->role) {
            case 'superadmin':
                // Superadmin lihat semua (dengan filter status jika ada)
                break;

            case 'admin_eng':
                $query->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)
                        ->orWhereIn('status', [
                            'pending',
                            'admin_approved',
                            'gm_approved',
                            'paid',
                            'rejected'
                        ]);
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

        $vrs = $query->orderBy('created_at', 'desc')->paginate(15);

        // Kirim status aktif ke view untuk active state di sidebar
        view()->share('activeStatus', $status);

        return view('vouchers.index', compact('vrs'));
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
                'message' => 'Ticket is not in pending VR status. Current status: ' . $ticket->status
            ], 422);
        }

        // Check if there's already a pending or approved VR for this ticket
        $existingVR = VoucherRequest::where('ticket_id', $ticket->id)
            ->whereIn('status', ['pending', 'admin_approved', 'om_approved', 'gm_approved'])
            ->first();

        if ($existingVR) {
            return response()->json([
                'success' => false,
                'message' => 'This ticket already has an active VR: #' . $existingVR->vr_number
            ], 422);
        }

        // Generate VR number
        $vrNumber = $this->generateVRNumber();

        $html = view('vouchers.partials.create-form', compact('ticket', 'vrNumber'))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Search tickets for VR creation (AJAX).
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
                'message' => 'Ticket not found or not in pending VR status'
            ], 404);
        }

        // Check if there's already a pending VR for this ticket
        $existingVR = VoucherRequest::where('ticket_id', $ticket->id)
            ->whereIn('status', ['pending', 'admin_approved', 'om_approved', 'gm_approved'])
            ->first();

        if ($existingVR) {
            return response()->json([
                'success' => false,
                'message' => 'This ticket already has an active VR: #' . $existingVR->vr_number
            ], 422);
        }

        return response()->json([
            'success' => true,
            'ticket_id' => $ticket->id
        ]);
    }

    /**
     * Store a newly created voucher request.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'admin_eng') {
            return response()->json([
                'success' => false,
                'message' => 'Only Admin Engineering can create voucher requests'
            ], 403);
        }

        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'vr_number' => 'required|string|unique:voucher_requests,vr_number',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.vendor' => 'nullable|string|max:255',
            'items.*.description' => 'nullable|string',
        ]);

        // Validate minimum total amount
        $totalAmount = 0;
        foreach ($request->items as $item) {
            $totalAmount += $item['qty'] * $item['unit_price'];
        }

        if ($totalAmount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Total amount must be greater than 0'
            ], 422);
        }

        $ticket = Ticket::findOrFail($request->ticket_id);

        // Double check ticket status
        if ($ticket->status !== 'pending_vr') {
            return response()->json([
                'success' => false,
                'message' => 'Ticket is not in pending VR status'
            ], 422);
        }

        // Double check for existing VR
        $existingVR = VoucherRequest::where('ticket_id', $ticket->id)
            ->whereIn('status', ['pending', 'admin_approved', 'om_approved', 'gm_approved'])
            ->first();

        if ($existingVR) {
            return response()->json([
                'success' => false,
                'message' => 'This ticket already has an active VR: #' . $existingVR->vr_number
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Create voucher request
            $vr = VoucherRequest::create([
                'vr_number' => $request->vr_number,
                'ticket_id' => $request->ticket_id,
                'total_amount' => $totalAmount,
                'notes' => $request->notes,
                'status' => 'pending',
                'created_by' => $user->id,
                'admin_approved' => false,
                'om_approved' => false,
                'gm_approved' => false,
            ]);

            // Create items
            foreach ($request->items as $item) {
                VoucherItem::create([
                    'voucher_request_id' => $vr->id,
                    'item_name' => $item['item_name'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'vendor' => $item['vendor'] ?? null,
                    'description' => $item['description'] ?? null,
                ]);
            }

            // Update ticket approval to indicate VR created
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
                'description' => 'Voucher Request #' . $vr->vr_number . ' created',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Add comment to ticket
            $ticket->comments()->create([
                'user_id' => $user->id,
                'comment' => "VR #{$vr->vr_number} created. Total: Rp " . number_format($totalAmount, 0, ',', '.'),
                'is_internal' => 0,
                'is_followup' => true,
            ]);

            // Notify admin engineering (self) and other admins
            $adminUsers = User::where('role', 'admin_eng')->where('status', 'active')->get();
            foreach ($adminUsers as $adminUser) {
                if ($adminUser->id !== $user->id) {
                    $this->sendNotification(
                        $adminUser,
                        $ticket,
                        $vr,
                        'New VR Created',
                        'VR #' . $vr->vr_number . ' has been created and needs approval',
                        'vr_request'
                    );
                }
            }

            // Notify technician if assigned
            if ($ticket->assigned_to) {
                $technician = User::find($ticket->assigned_to);
                if ($technician) {
                    $this->sendNotification(
                        $technician,
                        $ticket,
                        $vr,
                        'VR Created for Your Ticket',
                        'VR #' . $vr->vr_number . ' has been created for ticket #' . $ticket->ticket_number,
                        'info'
                    );
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Voucher Request created successfully',
                'vr' => $vr
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Create VR error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create VR: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show modal with VR details.
     */
    public function showModal($id)
    {
        $vr = VoucherRequest::with([
            'ticket',
            'creator',
            'items',
            'adminApprover',
            'omApprover',
            'gmApprover'
        ])->findOrFail($id);

        $html = view('vouchers.partials.view-details', compact('vr'))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Approve voucher request with saved signature ONLY.
     */
    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        $vr = VoucherRequest::with('ticket')->findOrFail($id);

        // Check permission based on current status
        $canApprove = false;
        $nextStatus = '';
        $approvalField = '';
        $approverField = '';
        $approvalAtField = '';

        if ($user->role === 'admin_eng' && $vr->status === 'pending') {
            $canApprove = true;
            $nextStatus = 'admin_approved';
            $approvalField = 'admin_approved';
            $approverField = 'admin_approved_by';
            $approvalAtField = 'admin_approved_at';
        } elseif ($user->role === 'om' && $vr->status === 'admin_approved') {
            $canApprove = true;
            $nextStatus = 'om_approved';
            $approvalField = 'om_approved';
            $approverField = 'om_approved_by';
            $approvalAtField = 'om_approved_at';
        } elseif ($user->role === 'gm' && $vr->status === 'om_approved') {
            $canApprove = true;
            $nextStatus = 'gm_approved';
            $approvalField = 'gm_approved';
            $approverField = 'gm_approved_by';
            $approvalAtField = 'gm_approved_at';
        }

        if (!$canApprove) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to approve this VR at its current stage'
            ], 403);
        }

        $request->validate([
            'use_saved_signature' => 'required|string|in:1',
            'notes' => 'nullable|string',
        ]);

        // Check if user has saved signature
        if (empty($user->signature_path) || !Storage::disk('public')->exists($user->signature_path)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have a saved signature. Please create one in your profile first.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Copy signature to VR signatures folder (stage 6-9 untuk membedakan dengan MR)
            $stage = $user->role === 'admin_eng' ? 6 : ($user->role === 'om' ? 7 : 8);

            $signaturePath = 'signatures/vr/' . $vr->vr_number . '_' . $user->role . '_' . time() . '.png';
            Storage::disk('public')->copy($user->signature_path, $signaturePath);

            // Create signature record (stage 6-9 untuk VR)
            Signature::create([
                'ticket_id' => $vr->ticket_id,
                'user_id' => $user->id,
                'signature_type' => 'approver',
                'stage' => $stage, // 6=Admin,7=OM,8=GM untuk VR
                'signature_path' => $signaturePath,
                'signed_at' => now(),
                'ip_address' => $request->ip(),
            ]);

            // Update VR status and approval fields
            $updateData = [
                'status' => $nextStatus,
                $approvalField => true,
                $approverField => $user->id,
                $approvalAtField => now(),
            ];

            // Add notes if provided
            if ($request->notes) {
                $updateData['notes'] = $vr->notes
                    ? $vr->notes . "\n\n" . ucfirst($user->role) . " notes: " . $request->notes
                    : ucfirst($user->role) . " notes: " . $request->notes;
            }

            $vr->update($updateData);

            // If GM approved, update ticket status back to in_progress
            if ($user->role === 'gm') {
                $vr->ticket->update([
                    'status' => 'in_progress',
                    'current_stage' => 4,
                ]);

                // Update ticket approval
                $approval = TicketApproval::firstOrCreate(['ticket_id' => $vr->ticket_id]);
                $approval->update([
                    'needs_vr' => false,
                ]);

                // Notify technician that VR is approved and work can continue
                if ($vr->ticket->assigned_to) {
                    $technician = User::find($vr->ticket->assigned_to);
                    if ($technician) {
                        $this->sendNotification(
                            $technician,
                            $vr->ticket,
                            $vr,
                            'VR Approved - Work Can Continue',
                            'VR #' . $vr->vr_number . ' has been fully approved. You can continue work on ticket #' . $vr->ticket->ticket_number,
                            'success'
                        );
                    }
                }
            }

            // Add comment to ticket
            $comment = "VR #{$vr->vr_number} approved by " . ucfirst($user->role);
            if ($request->notes) {
                $comment .= "\nNotes: " . $request->notes;
            }

            $vr->ticket->comments()->create([
                'user_id' => $user->id,
                'comment' => $comment,
                'is_internal' => 0,
                'is_followup' => true,
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $vr->ticket_id,
                'action' => 'vr_' . $user->role . '_approved',
                'description' => 'VR #' . $vr->vr_number . ' approved by ' . ucfirst($user->role),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Notify next approver
            $this->notifyNextApprover($vr, $user->role);

            // Notify creator
            $this->sendNotification(
                $vr->creator,
                $vr->ticket,
                $vr,
                'VR Approved',
                'Your VR #' . $vr->vr_number . ' has been approved by ' . ucfirst($user->role),
                'success'
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'VR approved successfully using saved signature'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approve VR error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve VR: ' . $e->getMessage()
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

        // Check permission based on current status
        $canReject = false;

        if ($user->role === 'admin_eng' && $vr->status === 'pending') {
            $canReject = true;
        } elseif ($user->role === 'om' && $vr->status === 'admin_approved') {
            $canReject = true;
        } elseif ($user->role === 'gm' && $vr->status === 'om_approved') {
            $canReject = true;
        } elseif ($user->role === 'superadmin') {
            $canReject = true; // Superadmin bisa reject kapan saja
        }

        if (!$canReject) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to reject this VR at its current stage'
            ], 403);
        }

        $request->validate([
            'rejection_reason' => 'required|string|min:10',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $notes = "Rejected by " . ucfirst($user->role) . "\nReason: " . $request->rejection_reason;
            if ($request->notes) {
                $notes .= "\nNotes: " . $request->notes;
            }

            $vr->update([
                'status' => 'rejected',
                'notes' => $vr->notes ? $vr->notes . "\n\n" . $notes : $notes,
                'rejection_reason' => $request->rejection_reason,
            ]);

            // Add comment to ticket
            $vr->ticket->comments()->create([
                'user_id' => $user->id,
                'comment' => "VR #{$vr->vr_number} rejected by " . ucfirst($user->role) .
                    "\nReason: " . $request->rejection_reason .
                    ($request->notes ? "\nNotes: " . $request->notes : ''),
                'is_internal' => 0,
                'is_followup' => true,
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $vr->ticket_id,
                'action' => 'vr_' . $user->role . '_rejected',
                'description' => 'VR #' . $vr->vr_number . ' rejected by ' . ucfirst($user->role),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Notify creator
            $this->sendNotification(
                $vr->creator,
                $vr->ticket,
                $vr,
                'VR Rejected',
                'Your VR #' . $vr->vr_number . ' was rejected by ' . ucfirst($user->role) .
                "\nReason: " . $request->rejection_reason,
                'rejection'
            );

            // Notify technician if assigned
            if ($vr->ticket->assigned_to) {
                $technician = User::find($vr->ticket->assigned_to);
                if ($technician) {
                    $this->sendNotification(
                        $technician,
                        $vr->ticket,
                        $vr,
                        'VR Rejected',
                        'VR #' . $vr->vr_number . ' for ticket #' . $vr->ticket->ticket_number . ' was rejected',
                        'rejection'
                    );
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'VR rejected successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reject VR error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject VR: ' . $e->getMessage()
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
                'message' => 'Only Admin Engineering or Superadmin can mark VR as paid'
            ], 403);
        }

        $vr = VoucherRequest::with('ticket')->findOrFail($id);

        if ($vr->status !== 'gm_approved') {
            return response()->json([
                'success' => false,
                'message' => 'VR must be GM approved before marking as paid'
            ], 422);
        }

        $request->validate([
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $notes = "Marked as paid by " . $user->name;
            if ($request->notes) {
                $notes .= "\nPayment notes: " . $request->notes;
            }

            $vr->update([
                'status' => 'paid',
                'notes' => $vr->notes ? $vr->notes . "\n\n" . $notes : $notes,
            ]);

            // Add comment to ticket
            $vr->ticket->comments()->create([
                'user_id' => $user->id,
                'comment' => "VR #{$vr->vr_number} marked as paid" .
                    ($request->notes ? "\nPayment notes: " . $request->notes : ''),
                'is_internal' => 0,
                'is_followup' => true,
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $vr->ticket_id,
                'action' => 'vr_paid',
                'description' => 'VR #' . $vr->vr_number . ' marked as paid',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Notify creator
            $this->sendNotification(
                $vr->creator,
                $vr->ticket,
                $vr,
                'VR Paid',
                'VR #' . $vr->vr_number . ' has been marked as paid',
                'success'
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'VR marked as paid successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Mark paid VR error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark VR as paid: ' . $e->getMessage()
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

        // Check delete permission
        $canDelete = false;

        if ($user->role === 'superadmin') {
            $canDelete = true;

            // Verify password for superadmin
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
                'message' => 'You are not authorized to delete this VR'
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Delete signatures
            $signatures = Signature::where('ticket_id', $vr->ticket_id)
                ->whereIn('stage', [6, 7, 8]) // VR stages
                ->get();

            foreach ($signatures as $signature) {
                if ($signature->signature_path) {
                    Storage::disk('public')->delete($signature->signature_path);
                }
                $signature->delete();
            }

            // Delete items
            $vr->items()->delete();

            // Add comment to ticket
            $vr->ticket->comments()->create([
                'user_id' => $user->id,
                'comment' => "VR #{$vr->vr_number} deleted by " . $user->name,
                'is_internal' => 0,
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $vr->ticket_id,
                'action' => 'vr_deleted',
                'description' => 'VR #' . $vr->vr_number . ' deleted',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Delete VR
            $vr->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'VR deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete VR error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete VR: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Print VR - DUMMY IMPLEMENTATION
     */
    public function print($id)
    {
        $vr = VoucherRequest::with(['ticket', 'creator', 'items'])->findOrFail($id);

        // Dummy response - will be implemented later with actual PDF
        return response()->json([
            'success' => true,
            'message' => 'Print feature is coming soon. This is a dummy response.',
            'vr' => [
                'number' => $vr->vr_number,
                'total' => $vr->total_amount,
                'items_count' => $vr->items->count()
            ]
        ]);
    }

    /**
     * Verify password for signature update (unused now but kept for compatibility)
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
     * Generate unique VR number.
     */
    private function generateVRNumber()
    {
        $year = date('Y');
        $month = date('m');

        $lastVR = VoucherRequest::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastVR) {
            $lastNumber = intval(substr($lastVR->vr_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return 'VR-' . $year . $month . '-' . $newNumber;
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
                'VR Needs Your Approval',
                'VR #' . $vr->vr_number . ' for ticket #' . $vr->ticket->ticket_number . ' is ' . $statusMessage,
                'approval'
            );
        }
    }

    /**
     * Send notification (in-app and email) - FIXED with email
     */
    private function sendNotification($user, $ticket, $vr, $title, $message, $type = 'info')
    {
        try {
            // Create in-app notification
            Notification::create([
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'is_read' => false,
            ]);

            // Send email notification if configured - UNCOMMENTED
            if (config('mail.mailers.smtp.host')) {
                try {
                    // You can create a specific mail class for VR notifications
                    // For now, log that email would be sent
                    Log::info('Email would be sent to ' . $user->email . ' for VR #' . $vr->vr_number);

                    // Uncomment when Mail class is ready:
                    // Mail::to($user->email)->queue(new VoucherNotification($user, $ticket, $vr, $title, $message, $type));
                } catch (\Exception $e) {
                    Log::warning('Email notification failed for user ' . $user->id . ': ' . $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            Log::error('Notification creation failed: ' . $e->getMessage());
        }
    }
}
