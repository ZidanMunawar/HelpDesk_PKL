<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\VoucherRequest;
use App\Models\VoucherItem;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class VoucherController extends Controller
{
    /**
     * Display a listing of VRs - SIMPLE LIST
     */
    public function index()
    {
        $user = Auth::user();
        $vrs = VoucherRequest::with(['ticket', 'creator', 'items'])
            ->latest()
            ->paginate(20);

        return view('vouchers.index', compact('vrs'));
    }
    // Di VoucherController, pastikan method createModal mengembalikan JSON:
    public function createModal($ticket_id)
    {
        $ticket = Ticket::with(['user', 'category', 'priority', 'approval'])
            ->findOrFail($ticket_id);

        $user = Auth::user();

        // Check permission
        if ($user->role !== 'admin_eng') {
            return response()->json([
                'success' => false,
                'message' => 'Only Admin Engineering can create VRs'
            ], 403);
        }

        // Check if ticket needs VR
        if (!$ticket->approval || !$ticket->approval->needs_vr) {
            return response()->json([
                'success' => false,
                'message' => 'This ticket does not require a VR'
            ], 403);
        }

        // Check ticket status
        if ($ticket->status !== 'pending_vr') {
            return response()->json([
                'success' => false,
                'message' => 'Ticket is not in pending VR status'
            ], 403);
        }

        // Generate VR number
        $vrNumber = 'VR-' . date('Ymd') . '-' . str_pad(VoucherRequest::count() + 1, 4, '0', STR_PAD_LEFT);

        $html = view('vouchers.partials.create-modal', compact('ticket', 'vrNumber'))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    // Dan method showModal:
    public function showModal($id)
    {
        $vr = VoucherRequest::with([
            'ticket.user',
            'ticket.category',
            'ticket.priority',
            'creator',
            'adminApprover',
            'omApprover',
            'gmApprover',
            'items'
        ])->findOrFail($id);

        $user = Auth::user();

        // Check permission
        if (!$this->canViewVR($user, $vr)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this VR'
            ], 403);
        }

        $html = view('vouchers.partials.show-modal', compact('vr'))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Store a new VR via AJAX
     */
    public function store(Request $request, $ticket_id)
    {
        $request->validate([
            'vr_number' => 'required|string|unique:voucher_requests,vr_number',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.vendor' => 'nullable|string|max:255',
        ]);

        $ticket = Ticket::findOrFail($ticket_id);
        $user = Auth::user();

        if ($user->role !== 'admin_eng') {
            return response()->json([
                'success' => false,
                'message' => 'Only Admin Engineering can create VRs'
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Create VR
            $vr = VoucherRequest::create([
                'vr_number' => $request->vr_number,
                'ticket_id' => $ticket->id,
                'total_amount' => 0,
                'status' => 'pending',
                'created_by' => $user->id,
                'notes' => $request->notes,
            ]);

            // Create items
            $totalAmount = 0;
            foreach ($request->items as $itemData) {
                VoucherItem::create([
                    'voucher_request_id' => $vr->id,
                    'item_name' => $itemData['item_name'],
                    'qty' => $itemData['qty'],
                    'unit_price' => $itemData['unit_price'],
                    'vendor' => $itemData['vendor'] ?? null,
                ]);
                $totalAmount += $itemData['qty'] * $itemData['unit_price'];
            }

            // Update total amount
            $vr->update(['total_amount' => $totalAmount]);

            // Update ticket approval record
            if ($ticket->approval) {
                $ticket->approval->update([
                    'vr_created_at' => now(),
                    'vr_created_by' => $user->id,
                ]);
            }

            // Update ticket status back to in_progress
            $ticket->update([
                'status' => 'in_progress',
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'action' => 'vr_created',
                'description' => 'Created VR #' . $vr->vr_number . ' for ticket',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'VR created successfully',
                'vr_id' => $vr->id,
                'vr_number' => $vr->vr_number,
                'redirect' => false
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create VR: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Approve/Reject VR via AJAX
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string',
        ]);

        $vr = VoucherRequest::with(['ticket'])->findOrFail($id);
        $user = Auth::user();

        // Check permission based on role and VR status
        if ($user->role === 'admin_eng' && $vr->status === 'pending') {
            // Admin Eng approval
            $vr->update([
                'admin_approved' => $request->action === 'approve',
                'admin_approved_by' => $request->action === 'approve' ? $user->id : null,
                'admin_approved_at' => $request->action === 'approve' ? now() : null,
                'status' => $request->action === 'approve' ? 'admin_approved' : 'rejected',
            ]);

        } elseif ($user->role === 'om' && $vr->status === 'admin_approved') {
            // OM approval
            $vr->update([
                'om_approved' => $request->action === 'approve',
                'om_approved_by' => $request->action === 'approve' ? $user->id : null,
                'om_approved_at' => $request->action === 'approve' ? now() : null,
                'status' => $request->action === 'approve' ? 'om_approved' : 'rejected',
            ]);

        } elseif ($user->role === 'gm' && $vr->status === 'om_approved') {
            // GM approval
            $vr->update([
                'gm_approved' => $request->action === 'approve',
                'gm_approved_by' => $request->action === 'approve' ? $user->id : null,
                'gm_approved_at' => $request->action === 'approve' ? now() : null,
                'status' => $request->action === 'approve' ? 'gm_approved' : 'rejected',
            ]);

        } else {
            return response()->json([
                'success' => false,
                'message' => 'You cannot perform this action'
            ], 403);
        }

        // Add notes if provided
        if ($request->notes) {
            $vr->update(['notes' => $vr->notes . "\n\n[" . $user->name . " - " . now()->format('Y-m-d H:i') . "]: " . $request->notes]);
        }

        // Log activity
        ActivityLog::create([
            'user_id' => $user->id,
            'ticket_id' => $vr->ticket_id,
            'action' => 'vr_' . ($request->action === 'approve' ? 'approved' : 'rejected'),
            'description' => $user->name . ' ' . ($request->action === 'approve' ? 'approved' : 'rejected') . ' VR #' . $vr->vr_number,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'VR ' . ($request->action === 'approve' ? 'approved' : 'rejected') . ' successfully',
            'vr_id' => $vr->id
        ]);
    }

    /**
     * Mark VR as paid
     */
    public function markAsPaid(Request $request, $id)
    {
        $vr = VoucherRequest::findOrFail($id);
        $user = Auth::user();

        // Only Admin Eng or Superadmin can mark as paid
        if (!in_array($user->role, ['admin_eng', 'superadmin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only Admin Engineering can mark VR as paid'
            ], 403);
        }

        // Check if VR is fully approved
        if ($vr->status !== 'gm_approved') {
            return response()->json([
                'success' => false,
                'message' => 'VR must be fully approved by GM before marking as paid'
            ], 403);
        }

        $vr->update([
            'status' => 'paid',
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => $user->id,
            'ticket_id' => $vr->ticket_id,
            'action' => 'vr_paid',
            'description' => 'Marked VR #' . $vr->vr_number . ' as paid',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'VR marked as paid',
            'vr_id' => $vr->id
        ]);
    }

    /**
     * Delete VR
     */
    public function destroy($id)
    {
        $vr = VoucherRequest::findOrFail($id);
        $user = Auth::user();

        // Only creator or superadmin can delete
        if ($vr->created_by !== $user->id && $user->role !== 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this VR'
            ], 403);
        }

        // Check if VR can be deleted
        if (!in_array($vr->status, ['pending', 'rejected'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete VR in current status'
            ], 403);
        }

        $vr->delete();

        // Update ticket status if needed
        if ($vr->ticket->status === 'in_progress' && $vr->ticket->approval->needs_vr) {
            $vr->ticket->update(['status' => 'pending_vr']);
        }

        return response()->json([
            'success' => true,
            'message' => 'VR deleted successfully',
        ]);
    }

    /**
     * Check if user can view VR
     */
    private function canViewVR($user, $vr)
    {
        // Superadmin can view all
        if ($user->role === 'superadmin')
            return true;

        // Creator can always view
        if ($vr->created_by === $user->id)
            return true;

        // Admin Eng can view if they created or if pending/admin_approved
        if ($user->role === 'admin_eng') {
            return in_array($vr->status, ['pending', 'admin_approved']);
        }

        // OM can view if admin_approved
        if ($user->role === 'om') {
            return $vr->status === 'admin_approved';
        }

        // GM can view if om_approved
        if ($user->role === 'gm') {
            return $vr->status === 'om_approved';
        }

        // Others cannot view
        return false;
    }
}
