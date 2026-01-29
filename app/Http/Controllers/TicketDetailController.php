<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Signature;
use App\Models\TicketApproval;
use App\Models\Notification;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;
use App\Mail\TicketNotification;

class TicketDetailController extends Controller
{
    /**
     * Display ticket detail with permission check
     */
    public function show($id)
    {
        $ticket = Ticket::with([
            'user',
            'category',
            'priority',
            'location',
            'department',
            'assignedUser',
            'attachments',
            'comments.user',
            'comments.attachments',
            'activities.user',
            'signatures.user',
            'voucherRequests.items',
            'approval'
        ])->findOrFail($id);

        $user = Auth::user();

        // Check permission based on role
        $canView = $this->canViewTicket($user, $ticket);

        if (!$canView) {
            abort(403, 'Unauthorized access to this ticket');
        }

        // Get technicians for assign modal (HANYA ADMIN_ENG)
        $technicians = [];
        $departments = [];
        $hasSignature = false;
        $canSaveSignature = false;

        if ($user->role === 'admin_eng') {
            $technicians = User::where('role', 'technician')
                ->where('status', 'active')
                ->with('department')
                ->get();
            $departments = Department::where('status', 'active')->get();
        }

        // Check if user has signature
        $hasSignature = !empty($user->signature_path) && Storage::disk('public')->exists($user->signature_path);

        // Hanya AdminEng, OM, GM yang bisa save signature
        $canSaveSignature = in_array($user->role, ['admin_eng', 'om', 'gm']);

        return view('tickets.show', compact(
            'ticket',
            'technicians',
            'departments',
            'hasSignature',
            'canSaveSignature'
        ));
    }

    /**
     * Check if user can view ticket
     */
    private function canViewTicket($user, $ticket)
    {
        switch ($user->role) {
            case 'superadmin':
            case 'admin_eng':
            case 'om':
            case 'gm':
                return true;

            case 'user':
                return $ticket->user_id === $user->id;

            case 'technician':
                return $ticket->assigned_to === $user->id || $ticket->user_id === $user->id;

            case 'manager':
                return $ticket->department_id === $user->department_id;

            default:
                return false;
        }
    }

    /**
     * Verify password for new signature creation
     */
    public function verifyPassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'action_type' => 'required|in:receive,om_approve,gm_approve'
        ]);

        $user = Auth::user();

        // Hanya AdminEng, OM, GM yang bisa ganti signature
        if (!in_array($user->role, ['admin_eng', 'om', 'gm'])) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to create new signatures'
            ], 403);
        }

        // Verifikasi password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 422);
        }

        // Cek apakah user sudah punya signature
        $hasSignature = !empty($user->signature_path) && Storage::disk('public')->exists($user->signature_path);

        return response()->json([
            'success' => true,
            'message' => 'Password verified successfully',
            'has_signature' => $hasSignature,
            'action_type' => $request->action_type
        ]);
    }

    /**
     * Helper function to verify password for new signature
     */
    private function verifyPasswordForNewSignature($request)
    {
        if (!$request->has('current_password')) {
            return true; // Tidak perlu verifikasi jika tidak minta ganti
        }

        $user = Auth::user();
        return Hash::check($request->current_password, $user->password);
    }

    /**
     * Admin Engineering receive ticket - DENGAN OPSI SIGNATURE BARU
     */
    public function receiveTicket(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->role !== 'admin_eng') {
            return response()->json([
                'success' => false,
                'message' => 'Only Admin Engineering can receive tickets'
            ], 403);
        }

        $request->validate([
            'signature_data' => 'required|string',
            'save_signature' => 'nullable|boolean',
            'current_password' => 'nullable|string' // Untuk verifikasi jika ganti signature
        ]);

        $ticket = Ticket::findOrFail($id);

        if ($ticket->status !== 'open') {
            return response()->json([
                'success' => false,
                'message' => 'Ticket is not in open status'
            ], 403);
        }

        // Verifikasi password jika ingin ganti signature yang sudah ada
        if ($request->has('current_password') && !$this->verifyPasswordForNewSignature($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Save signature
            $signaturePath = $this->saveSignature(
                $request->signature_data,
                $ticket->ticket_number,
                $user->id,
                2 // Stage 2: Received
            );

            // Save to user profile if requested (HANYA ADMIN_ENG)
            if ($request->has('save_signature') && $request->boolean('save_signature')) {
                // Hapus signature lama jika ada
                if ($user->signature_path) {
                    Storage::disk('public')->delete($user->signature_path);
                }

                $user->update([
                    'signature_path' => $signaturePath,
                    'has_signature' => true,
                    'signature_updated_at' => now()
                ]);
            }

            // Create signature record
            Signature::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'signature_type' => 'approver',
                'stage' => 2, // Received stage
                'signature_path' => $signaturePath,
                'signed_at' => now(),
                'ip_address' => $request->ip(),
            ]);

            // PERBAIKAN: Update ticket status ke received
            $ticket->update([
                'status' => 'received', // Status: received
                'current_stage' => 2, // Stage 2: Received by Admin
            ]);

            // Update ticket approvals
            $approval = TicketApproval::firstOrCreate(['ticket_id' => $ticket->id]);
            $approval->update([
                'admin_eng_received' => true,
                'admin_eng_received_by' => $user->id,
                'admin_eng_received_at' => now(),
                'status' => 'pending'
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'action' => 'received',
                'description' => 'Ticket received by Admin Engineering',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Send notification to OM
            $omUsers = User::where('role', 'om')->where('status', 'active')->get();
            foreach ($omUsers as $omUser) {
                $this->sendNotification(
                    $omUser,
                    $ticket,
                    'Ticket Needs OM Approval',
                    'Ticket #' . $ticket->ticket_number . ' needs your approval',
                    'approval'
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ticket received successfully',
                'redirect' => route('tickets.show', $ticket->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Receive ticket error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to receive ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * OM Approve/Reject ticket - DENGAN OPSI SIGNATURE BARU
     */
    public function omAction(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->role !== 'om') {
            return response()->json([
                'success' => false,
                'message' => 'Only Operation Manager can perform this action'
            ], 403);
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'signature_data' => 'required_if:action,approve|string',
            'save_signature' => 'nullable|boolean',
            'current_password' => 'nullable|string',
            'rejection_reason' => 'required_if:action,reject|string'
        ]);

        $ticket = Ticket::findOrFail($id);

        if ($ticket->status !== 'received') {
            return response()->json([
                'success' => false,
                'message' => 'Ticket is not received status'
            ], 403);
        }

        // Verifikasi password jika ingin ganti signature yang sudah ada
        if ($request->has('current_password') && !$this->verifyPasswordForNewSignature($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 422);
        }

        DB::beginTransaction();
        try {
            if ($request->action === 'reject') {
                // Handle rejection
                $ticket->update([
                    'status' => 'cancelled',
                    'current_stage' => 1, // Kembali ke stage 1
                    'approval_status' => 'rejected',
                    'closed_at' => now(),
                ]);

                $approval = TicketApproval::firstOrCreate(['ticket_id' => $ticket->id]);
                $approval->update([
                    'status' => 'rejected',
                    'rejection_reason' => $request->rejection_reason,
                    'rejection_note' => 'Rejected by OM: ' . $request->rejection_reason
                ]);

                // Log activity
                ActivityLog::create([
                    'user_id' => $user->id,
                    'ticket_id' => $ticket->id,
                    'action' => 'om_rejected',
                    'description' => 'Ticket rejected by OM',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                // Notify user
                $this->sendNotification(
                    $ticket->user,
                    $ticket,
                    'Ticket Rejected by OM',
                    'Your ticket #' . $ticket->ticket_number . ' was rejected by OM. Reason: ' . $request->rejection_reason,
                    'rejection'
                );

            } else {
                // Handle approval
                $signaturePath = $this->saveSignature(
                    $request->signature_data,
                    $ticket->ticket_number,
                    $user->id,
                    3 // Stage 3: OM Approval
                );

                // Save to user profile if requested (HANYA OM)
                if ($request->has('save_signature') && $request->boolean('save_signature')) {
                    // Hapus signature lama jika ada
                    if ($user->signature_path) {
                        Storage::disk('public')->delete($user->signature_path);
                    }

                    $user->update([
                        'signature_path' => $signaturePath,
                        'has_signature' => true,
                        'signature_updated_at' => now()
                    ]);
                }

                // Create signature record
                Signature::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'signature_type' => 'approver',
                    'stage' => 3,
                    'signature_path' => $signaturePath,
                    'signed_at' => now(),
                    'ip_address' => $request->ip(),
                ]);

                // PERBAIKAN: Set status pending_om dan stage 3
                $ticket->update([
                    'status' => 'pending_om', // Status: waiting for OM approval
                    'current_stage' => 3, // Stage 3: OM Approval
                ]);

                // Update ticket approvals
                $approval = TicketApproval::firstOrCreate(['ticket_id' => $ticket->id]);
                $approval->update([
                    'om_approved' => true,
                    'om_approved_by' => $user->id,
                    'om_approved_at' => now(),
                ]);

                // Log activity
                ActivityLog::create([
                    'user_id' => $user->id,
                    'ticket_id' => $ticket->id,
                    'action' => 'om_approved',
                    'description' => 'Ticket approved by OM',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                // Notify Admin Engineering
                $adminEngUsers = User::where('role', 'admin_eng')->where('status', 'active')->get();
                foreach ($adminEngUsers as $adminUser) {
                    $this->sendNotification(
                        $adminUser,
                        $ticket,
                        'Ticket Approved by OM',
                        'Ticket #' . $ticket->ticket_number . ' has been approved by OM and is ready for assignment',
                        'assignment'
                    );
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ticket ' . ($request->action === 'approve' ? 'approved' : 'rejected') . ' successfully',
                'redirect' => route('tickets.show', $ticket->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('OM action error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process OM action: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Technician complete work
     */
    public function technicianComplete(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->role !== 'technician') {
            return response()->json([
                'success' => false,
                'message' => 'Only technicians can complete work'
            ], 403);
        }

        $request->validate([
            'signature_data' => 'required|string',
            'completion_notes' => 'nullable|string'
        ]);

        $ticket = Ticket::findOrFail($id);

        if ($ticket->assigned_to !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not assigned to this ticket'
            ], 403);
        }

        if ($ticket->status !== 'in_progress') {
            return response()->json([
                'success' => false,
                'message' => 'Ticket is not in progress'
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Save signature
            $signaturePath = $this->saveSignature(
                $request->signature_data,
                $ticket->ticket_number,
                $user->id,
                6 // Stage 6: Completed by technician
            );

            // Create signature record
            Signature::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'signature_type' => 'technician',
                'stage' => 6,
                'signature_path' => $signaturePath,
                'signed_at' => now(),
                'ip_address' => $request->ip(),
            ]);

            // PERBAIKAN: Update ticket status ke completed
            $ticket->update([
                'status' => 'completed', // Status: completed
                'current_stage' => 6, // Stage 6: Completed
                'resolved_at' => now(),
            ]);

            // Add completion comment
            $comment = "Work completed by technician.";
            if ($request->completion_notes) {
                $comment .= "\nNotes: " . $request->completion_notes;
            }

            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'comment' => $comment,
                'is_internal' => 0,
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'action' => 'completed',
                'description' => 'Work completed by technician',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Notify reporter untuk checking
            $reporter = $ticket->user;
            $this->sendNotification(
                $reporter,
                $ticket,
                'Work Completed - Please Check',
                'The work on ticket #' . $ticket->ticket_number . ' has been completed. Please check and confirm.',
                'check'
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Work marked as completed. Reporter has been notified to check.',
                'redirect' => route('tickets.show', $ticket->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Technician complete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete work: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Technician request VR
     */
    public function requestVR(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->role !== 'technician') {
            return response()->json([
                'success' => false,
                'message' => 'Only technicians can request VR'
            ], 403);
        }

        $request->validate([
            'vr_reason' => 'required|string',
            'estimated_cost' => 'nullable|numeric',
            'required_items' => 'nullable|string'
        ]);

        $ticket = Ticket::findOrFail($id);

        if ($ticket->assigned_to !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not assigned to this ticket'
            ], 403);
        }

        if ($ticket->status !== 'in_progress') {
            return response()->json([
                'success' => false,
                'message' => 'Ticket is not in progress'
            ], 403);
        }

        DB::beginTransaction();
        try {
            // PERBAIKAN: Update ticket status ke pending_vr
            $ticket->update([
                'status' => 'pending_vr', // Status: pending VR
                'current_stage' => 5, // Stage 5: Waiting VR
            ]);

            // Update ticket approvals
            $approval = TicketApproval::firstOrCreate(['ticket_id' => $ticket->id]);
            $approval->update([
                'needs_vr' => true,
                'vr_reason' => $request->vr_reason
            ]);

            // Add VR request comment
            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'comment' => "VR Requested. Reason: " . $request->vr_reason .
                    ($request->estimated_cost ? "\nEstimated Cost: " . number_format($request->estimated_cost, 2) : "") .
                    ($request->required_items ? "\nRequired Items: " . $request->required_items : ""),
                'is_internal' => 0,
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'action' => 'vr_requested',
                'description' => 'VR requested by technician',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Notify Admin Engineering
            $adminEngUsers = User::where('role', 'admin_eng')->where('status', 'active')->get();
            foreach ($adminEngUsers as $adminUser) {
                $this->sendNotification(
                    $adminUser,
                    $ticket,
                    'VR Requested by Technician',
                    'Ticket #' . $ticket->ticket_number . ' requires VR. Reason: ' . $request->vr_reason,
                    'vr_request'
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'VR request submitted. Admin Engineering has been notified.',
                'redirect' => route('tickets.show', $ticket->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Request VR error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to request VR: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * User check completion
     */
    public function userCheck(Request $request, $id)
    {
        $user = Auth::user();
        $ticket = Ticket::findOrFail($id);

        // PERBAIKAN: Bolehkan user ATAU admin_eng yang create ticket
        if ($user->role === 'user') {
            // User biasa hanya bisa check ticket miliknya sendiri
            if ($ticket->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'This is not your ticket'
                ], 403);
            }
        } elseif ($user->role === 'admin_eng') {
            // Admin_eng hanya bisa check jika dia yang create ticket
            if ($ticket->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only the reporter can check completion'
                ], 403);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Only reporter (user or admin_eng) can check completion'
            ], 403);
        }

        $request->validate([
            'action' => 'required|in:accept,reject',
            'signature_data' => 'required_if:action,accept|string',
            'rejection_reason' => 'required_if:action,reject|string'
        ]);

        if ($ticket->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Ticket is not in completed status'
            ], 403);
        }

        DB::beginTransaction();
        try {
            if ($request->action === 'reject') {
                // User/Admin rejects the completion
                $ticket->update([
                    'status' => 'in_progress',
                    'current_stage' => 4, // Kembali ke stage 4: In Progress
                ]);

                // Remove technician's completion signature
                Signature::where('ticket_id', $ticket->id)
                    ->where('stage', 6)
                    ->where('signature_type', 'technician')
                    ->delete();

                // Add comment
                TicketComment::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'comment' => "Completion rejected by reporter. Reason: " . $request->rejection_reason,
                    'is_internal' => 0,
                ]);

                // Log activity
                ActivityLog::create([
                    'user_id' => $user->id,
                    'ticket_id' => $ticket->id,
                    'action' => 'rejected',
                    'description' => 'Completion rejected by reporter',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                // Notify technician
                if ($ticket->assigned_to) {
                    $technician = User::find($ticket->assigned_to);
                    $this->sendNotification(
                        $technician,
                        $ticket,
                        'Completion Rejected by Reporter',
                        'Your completion on ticket #' . $ticket->ticket_number . ' was rejected. Reason: ' . $request->rejection_reason,
                        'rejection'
                    );
                }

            } else {
                // User/Admin accepts the completion
                $signaturePath = $this->saveSignature(
                    $request->signature_data,
                    $ticket->ticket_number,
                    $user->id,
                    7 // Stage 7: Checked by reporter
                );

                // Create signature record
                Signature::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'signature_type' => 'reporter',
                    'stage' => 7,
                    'signature_path' => $signaturePath,
                    'signed_at' => now(),
                    'ip_address' => $request->ip(),
                ]);

                // PERBAIKAN: Set status ke pending_gm
                $ticket->update([
                    'status' => 'pending_gm', // Status: pending GM approval
                    'current_stage' => 7, // Stage 7: User Check Done
                ]);

                // Update ticket approvals
                $approval = TicketApproval::firstOrCreate(['ticket_id' => $ticket->id]);
                $approval->update([
                    'user_checked' => true,
                    'user_checked_by' => $user->id,
                    'user_checked_at' => now(),
                ]);

                // Add comment
                TicketComment::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'comment' => "Reporter accepted the completion",
                    'is_internal' => 0,
                ]);

                // Log activity
                ActivityLog::create([
                    'user_id' => $user->id,
                    'ticket_id' => $ticket->id,
                    'action' => 'accepted',
                    'description' => 'Completion accepted by reporter',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                // Notify GM
                $gmUsers = User::where('role', 'gm')->where('status', 'active')->get();
                foreach ($gmUsers as $gmUser) {
                    $this->sendNotification(
                        $gmUser,
                        $ticket,
                        'Ticket Needs GM Approval',
                        'Ticket #' . $ticket->ticket_number . ' needs your final approval',
                        'approval'
                    );
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Completion ' . ($request->action === 'accept' ? 'accepted' : 'rejected'),
                'redirect' => route('tickets.show', $ticket->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User check error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process check: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GM Approve/Reject - DENGAN OPSI SIGNATURE BARU
     */
    public function gmAction(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->role !== 'gm') {
            return response()->json([
                'success' => false,
                'message' => 'Only General Manager can perform this action'
            ], 403);
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'signature_data' => 'required_if:action,approve|string',
            'save_signature' => 'nullable|boolean',
            'current_password' => 'nullable|string',
            'rejection_reason' => 'required_if:action,reject|string'
        ]);

        $ticket = Ticket::findOrFail($id);

        if ($ticket->status !== 'pending_gm') {
            return response()->json([
                'success' => false,
                'message' => 'Ticket is not pending GM approval'
            ], 403);
        }

        // Verifikasi password jika ingin ganti signature yang sudah ada
        if ($request->has('current_password') && !$this->verifyPasswordForNewSignature($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 422);
        }

        DB::beginTransaction();
        try {
            if ($request->action === 'reject') {
                // Handle rejection
                $ticket->update([
                    'status' => 'cancelled',
                    'current_stage' => 1, // Kembali ke stage 1
                    'approval_status' => 'rejected',
                    'closed_at' => now(),
                ]);

                $approval = TicketApproval::firstOrCreate(['ticket_id' => $ticket->id]);
                $approval->update([
                    'status' => 'rejected',
                    'rejection_reason' => $request->rejection_reason,
                    'rejection_note' => 'Rejected by GM: ' . $request->rejection_reason
                ]);

                // Log activity
                ActivityLog::create([
                    'user_id' => $user->id,
                    'ticket_id' => $ticket->id,
                    'action' => 'gm_rejected',
                    'description' => 'Ticket rejected by GM',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                // Notify reporter dan admin
                $this->sendNotification(
                    $ticket->user,
                    $ticket,
                    'Ticket Rejected by GM',
                    'Your ticket #' . $ticket->ticket_number . ' was rejected by GM',
                    'rejection'
                );

            } else {
                // Handle approval
                $signaturePath = $this->saveSignature(
                    $request->signature_data,
                    $ticket->ticket_number,
                    $user->id,
                    8 // Stage 8: GM Approval
                );

                // Save to user profile if requested (HANYA GM)
                if ($request->has('save_signature') && $request->boolean('save_signature')) {
                    // Hapus signature lama jika ada
                    if ($user->signature_path) {
                        Storage::disk('public')->delete($user->signature_path);
                    }

                    $user->update([
                        'signature_path' => $signaturePath,
                        'has_signature' => true,
                        'signature_updated_at' => now()
                    ]);
                }

                // Create signature record
                Signature::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'signature_type' => 'approver',
                    'stage' => 8,
                    'signature_path' => $signaturePath,
                    'signed_at' => now(),
                    'ip_address' => $request->ip(),
                ]);

                // PERBAIKAN: Set status ke ready_for_closure
                $ticket->update([
                    'status' => 'ready_for_closure', // Status: ready for closure
                    'current_stage' => 8, // Stage 8: GM Approved
                    'approval_status' => 'approved',
                ]);

                // Update ticket approvals
                $approval = TicketApproval::firstOrCreate(['ticket_id' => $ticket->id]);
                $approval->update([
                    'gm_approved' => true,
                    'gm_approved_by' => $user->id,
                    'gm_approved_at' => now(),
                    'status' => 'approved'
                ]);

                // Log activity
                ActivityLog::create([
                    'user_id' => $user->id,
                    'ticket_id' => $ticket->id,
                    'action' => 'gm_approved',
                    'description' => 'Ticket approved by GM - Ready for administrative closure',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                // Notify all involved parties
                $this->sendNotification(
                    $ticket->user,
                    $ticket,
                    'Ticket Approved by GM',
                    'Your ticket #' . $ticket->ticket_number . ' has been approved by GM',
                    'closure'
                );

                if ($ticket->assigned_to) {
                    $this->sendNotification(
                        User::find($ticket->assigned_to),
                        $ticket,
                        'Ticket Approved by GM',
                        'Ticket #' . $ticket->ticket_number . ' has been approved by GM',
                        'closure'
                    );
                }

                $adminEngUsers = User::where('role', 'admin_eng')->where('status', 'active')->get();
                foreach ($adminEngUsers as $adminUser) {
                    $this->sendNotification(
                        $adminUser,
                        $ticket,
                        'Ticket Ready for Administrative Closure',
                        'Ticket #' . $ticket->ticket_number . ' has been approved by GM and is ready for administrative closure.',
                        'closure'
                    );
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ticket ' . ($request->action === 'approve' ? 'approved' : 'rejected') . ' by GM',
                'redirect' => route('tickets.show', $ticket->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('GM action error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process GM action: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign ticket to technician - PERBAIKAN: Status in_progress setelah OM approve
     */
    /**
     * Assign ticket to technician
     */
    public function assignTicket(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->role !== 'admin_eng') {
            return response()->json([
                'success' => false,
                'message' => 'Only Admin Engineering can assign technicians'
            ], 403);
        }

        $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'nullable|date|after:now'
        ]);

        $ticket = Ticket::findOrFail($id);

        // PERBAIKAN: Validasi status yang benar untuk assign
        // Bisa assign jika status in_progress (setelah OM approve) atau pending_vr
        $assignableStatuses = ['in_progress', 'pending_vr'];

        if (!in_array($ticket->status, $assignableStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket cannot be assigned in current status: ' . $ticket->status .
                    '. Ticket must be in progress or pending VR.'
            ], 403);
        }

        $technician = User::find($request->assigned_to);

        if ($technician->role !== 'technician') {
            return response()->json([
                'success' => false,
                'message' => 'Selected user is not a technician'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $oldAssignee = $ticket->assigned_to;

            // Jika dari pending_vr, tetap di pending_vr (tunggu VR approval)
            // Jika dari in_progress, tetap in_progress
            $newStatus = $ticket->status === 'pending_vr' ? 'pending_vr' : 'in_progress';

            $ticket->update([
                'assigned_to' => $request->assigned_to,
                'status' => $newStatus, // Tetap di status yang sama
                'current_stage' => $ticket->current_stage, // Stage tetap sama
                'due_date' => $request->due_date ?: $ticket->due_date,
            ]);

            // Add comment
            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'comment' => "Ticket assigned to " . $technician->name .
                    ($request->due_date ? " with due date: " . \Carbon\Carbon::parse($request->due_date)->format('d M Y, H:i') : ""),
                'is_internal' => 0,
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'action' => 'assigned',
                'description' => "Ticket assigned to {$technician->name}",
                'old_values' => json_encode(['assigned_to' => $oldAssignee]),
                'new_values' => json_encode([
                    'assigned_to' => $request->assigned_to,
                    'due_date' => $request->due_date
                ]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Notify technician
            $this->sendNotification(
                $technician,
                $ticket,
                'New Ticket Assigned',
                'Ticket #' . $ticket->ticket_number . ' has been assigned to you' .
                ($request->due_date ? ". Due date: " . \Carbon\Carbon::parse($request->due_date)->format('d M Y, H:i') : ""),
                'assignment'
            );

            // Juga notify reporter bahwa technician sudah diassign
            $this->sendNotification(
                $ticket->user,
                $ticket,
                'Technician Assigned',
                'A technician has been assigned to your ticket #' . $ticket->ticket_number,
                'assignment'
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Ticket assigned to {$technician->name}",
                'redirect' => route('tickets.show', $ticket->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Assign ticket error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel ticket
     */
    public function cancelTicket(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $user = Auth::user();

        // Check permission
        if (!in_array($user->role, ['admin_eng', 'superadmin']) && $ticket->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to cancel this ticket'
            ], 403);
        }

        // Only allow cancelling in certain statuses
        $cancellableStatuses = ['open', 'received', 'pending_om', 'in_progress', 'pending_vr'];
        if (!in_array($ticket->status, $cancellableStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket cannot be cancelled at this stage.'
            ], 403);
        }

        $request->validate([
            'cancellation_reason' => 'required|string|min:10'
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $ticket->status;

            $ticket->update([
                'status' => 'cancelled',
                'current_stage' => 1, // Kembali ke stage 1
                'closed_at' => now(),
                'approval_status' => 'rejected',
            ]);

            // Update approval record
            $approval = TicketApproval::firstOrCreate(['ticket_id' => $ticket->id]);
            $approval->update([
                'status' => 'rejected',
                'rejection_reason' => $request->cancellation_reason
            ]);

            // Add cancellation comment
            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'comment' => "Ticket cancelled. Reason: " . $request->cancellation_reason,
                'is_internal' => 0,
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'action' => 'cancelled',
                'description' => 'Ticket cancelled by ' . $user->name,
                'old_values' => json_encode(['status' => $oldStatus]),
                'new_values' => json_encode(['status' => 'cancelled']),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Notify involved parties
            $notifyUsers = [$ticket->user];

            if ($ticket->assigned_to) {
                $notifyUsers[] = User::find($ticket->assigned_to);
            }

            foreach ($notifyUsers as $notifyUser) {
                if ($notifyUser) {
                    $this->sendNotification(
                        $notifyUser,
                        $ticket,
                        'Ticket Cancelled',
                        'Ticket #' . $ticket->ticket_number . ' has been cancelled. Reason: ' . $request->cancellation_reason,
                        'cancellation'
                    );
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ticket cancelled successfully!',
                'redirect' => route('tickets.show', $ticket->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cancel ticket error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Close ticket (final step by admin) - PERBAIKAN: Stage menjadi 9
     */
    public function closeTicket(Request $request, $id)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['admin_eng', 'superadmin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only Admin Engineering can close tickets administratively'
            ], 403);
        }

        $ticket = Ticket::findOrFail($id);

        // Hanya bisa close jika status 'ready_for_closure'
        if ($ticket->status !== 'ready_for_closure') {
            return response()->json([
                'success' => false,
                'message' => 'Ticket must be approved by GM and in "Ready for Closure" status'
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Mark ticket as administratively closed
            $ticket->update([
                'status' => 'closed', // Status: closed
                'current_stage' => 9, // Stage 9: Closed
                'closed_at' => now(),
            ]);

            // Update approval record
            $approval = TicketApproval::firstOrCreate(['ticket_id' => $ticket->id]);
            $approval->update([
                'admin_check' => true,
                'admin_checked_by' => $user->id,
                'admin_checked_at' => now(),
            ]);

            // Add comment
            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'comment' => "Ticket administratively closed by " . $user->name,
                'is_internal' => 0,
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'action' => 'admin_closed',
                'description' => 'Ticket administratively closed by ' . $user->name,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Notify reporter dan technician
            $this->sendNotification(
                $ticket->user,
                $ticket,
                'Ticket Closed',
                'Ticket #' . $ticket->ticket_number . ' has been closed administratively',
                'closure'
            );

            if ($ticket->assigned_to) {
                $this->sendNotification(
                    User::find($ticket->assigned_to),
                    $ticket,
                    'Ticket Closed',
                    'Ticket #' . $ticket->ticket_number . ' has been closed administratively',
                    'closure'
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ticket administratively closed',
                'redirect' => route('tickets.show', $ticket->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Close ticket error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to close ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add comment to ticket
     */
    public function addComment(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        $ticket = Ticket::findOrFail($id);
        $user = Auth::user();

        // Check permission to comment
        $allowedRoles = ['user', 'admin_eng', 'technician'];
        if (!in_array($user->role, $allowedRoles)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to comment on this ticket'
            ], 403);
        }

        // Jika user biasa, harus pemilik ticket
        if ($user->role === 'user' && $ticket->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to comment on this ticket'
            ], 403);
        }

        // Jika technician, harus yang assigned
        if ($user->role === 'technician' && $ticket->assigned_to !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to comment on this ticket'
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Create comment
            $comment = TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'comment' => $request->comment,
                'is_internal' => 0,
            ]);

            // Handle attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('comment_attachments', $fileName, 'public');

                    $comment->attachments()->create([
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'action' => 'commented',
                'description' => $user->name . ' added a comment',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Notify relevant parties
            if ($ticket->user_id !== $user->id) {
                // Notify reporter jika bukan dia yang comment
                $this->sendNotification(
                    $ticket->user,
                    $ticket,
                    'New Comment on Ticket',
                    'There is a new comment on ticket #' . $ticket->ticket_number,
                    'comment'
                );
            }

            if ($ticket->assigned_to && $ticket->assigned_to !== $user->id) {
                // Notify technician jika bukan dia yang comment
                $this->sendNotification(
                    User::find($ticket->assigned_to),
                    $ticket,
                    'New Comment on Ticket',
                    'There is a new comment on ticket #' . $ticket->ticket_number,
                    'comment'
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'comment' => $comment->load('user', 'attachments')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Add comment error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add comment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin continue to OM approval (tombol lanjut setelah receive)
     */
    public function continueToOM(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->role !== 'admin_eng') {
            return response()->json([
                'success' => false,
                'message' => 'Only Admin Engineering can continue to OM'
            ], 403);
        }

        $ticket = Ticket::findOrFail($id);

        if ($ticket->status !== 'received') {
            return response()->json([
                'success' => false,
                'message' => 'Ticket is not in received status'
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Update ticket status ke pending_om
            $ticket->update([
                'status' => 'pending_om',
                'current_stage' => 3, // Stage 3: OM Approval
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'action' => 'continued_to_om',
                'description' => 'Ticket continued to OM for approval',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Send notification to OM
            $omUsers = User::where('role', 'om')->where('status', 'active')->get();
            foreach ($omUsers as $omUser) {
                $this->sendNotification(
                    $omUser,
                    $ticket,
                    'Ticket Needs OM Approval',
                    'Ticket #' . $ticket->ticket_number . ' needs your approval',
                    'approval'
                );
            }

            // Juga notify reporter
            $this->sendNotification(
                $ticket->user,
                $ticket,
                'Ticket Sent to OM',
                'Your ticket #' . $ticket->ticket_number . ' has been sent to Operation Manager for approval',
                'info'
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ticket sent to OM for approval',
                'redirect' => route('tickets.show', $ticket->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Continue to OM error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to continue to OM: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete ticket (Superadmin only) -
     */
    public function destroy(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->role !== 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $ticket = Ticket::withTrashed()->findOrFail($id);

        // Verifikasi password untuk delete
        if (!$request->has('password') || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password verification failed'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Delete attachments from storage
            foreach ($ticket->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            foreach ($ticket->comments as $comment) {
                foreach ($comment->attachments as $attachment) {
                    Storage::disk('public')->delete($attachment->file_path);
                }
            }

            // Delete signatures
            foreach ($ticket->signatures as $signature) {
                if ($signature->signature_path) {
                    Storage::disk('public')->delete($signature->signature_path);
                }
            }

            // Delete voucher requests and items
            foreach ($ticket->voucherRequests as $vr) {
                $vr->items()->delete();
                $vr->delete();
            }

            // Force delete ticket
            $ticket->forceDelete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ticket permanently deleted successfully',
                'redirect' => route('tickets.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete ticket error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * QUICK APPROVE - Untuk user yang sudah punya signature
     */
    public function quickApprove(Request $request, $id)
    {
        $user = Auth::user();
        $role = $user->role;

        // Validasi role yang bisa quick approve
        if (!in_array($role, ['admin_eng', 'om', 'gm'])) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized for quick approval'
            ], 403);
        }

        $ticket = Ticket::findOrFail($id);

        // PERBAIKAN: Validasi status yang benar untuk setiap role
        $validStatus = [];
        $stage = 0;
        $status = '';
        $actionType = '';

        switch ($role) {
            case 'admin_eng':
                $validStatus = ['open']; // Quick approve dari open ke received
                $stage = 2;
                $status = 'received';
                $actionType = 'receive';
                break;
            case 'om':
                $validStatus = ['pending_om']; // OM hanya bisa quick approve dari pending_om
                $stage = 3;
                $status = 'in_progress'; // Setelah OM approve, langsung in_progress
                $actionType = 'om_approve';
                break;
            case 'gm':
                $validStatus = ['pending_gm']; // GM hanya bisa quick approve dari pending_gm
                $stage = 8;
                $status = 'ready_for_closure';
                $actionType = 'gm_approve';
                break;
        }

        if (!in_array($ticket->status, $validStatus)) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket is not in correct status for ' . strtoupper($role) . ' approval. Current status: ' . $ticket->status
            ], 403);
        }

        // Validasi user punya signature
        if (empty($user->signature_path) || !Storage::disk('public')->exists($user->signature_path)) {
            return response()->json([
                'success' => false,
                'message' => 'You need to have a saved signature first'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Copy signature dari profile ke signature record
            $signaturePath = 'signatures/quick_' . $ticket->ticket_number . '_stage' . $stage . '_user' . $user->id . '_' . time() . '.png';
            Storage::disk('public')->copy($user->signature_path, $signaturePath);

            // Create signature record
            Signature::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'signature_type' => 'approver',
                'stage' => $stage,
                'signature_path' => $signaturePath,
                'signed_at' => now(),
                'ip_address' => $request->ip(),
            ]);

            // Update ticket berdasarkan role
            switch ($role) {
                case 'admin_eng':
                    // Quick approve dari open ke received
                    $ticket->update([
                        'status' => 'received',
                        'current_stage' => 2,
                    ]);

                    $approval = TicketApproval::firstOrCreate(['ticket_id' => $ticket->id]);
                    $approval->update([
                        'admin_eng_received' => true,
                        'admin_eng_received_by' => $user->id,
                        'admin_eng_received_at' => now(),
                        'status' => 'pending'
                    ]);

                    // TIDAK notify OM di sini, karena perlu admin klik "Continue to OM"
                    // Hanya notify reporter
                    $this->sendNotification(
                        $ticket->user,
                        $ticket,
                        'Ticket Received',
                        'Your ticket #' . $ticket->ticket_number . ' has been received by Engineering Department',
                        'info'
                    );
                    break;

                case 'om':
                    // Quick approve dari pending_om ke in_progress
                    $ticket->update([
                        'status' => 'in_progress',
                        'current_stage' => 4, // Stage 4: In Progress (setelah OM approve)
                    ]);

                    $approval = TicketApproval::firstOrCreate(['ticket_id' => $ticket->id]);
                    $approval->update([
                        'om_approved' => true,
                        'om_approved_by' => $user->id,
                        'om_approved_at' => now(),
                    ]);

                    // Notify Admin Engineering bahwa OM sudah approve
                    $adminEngUsers = User::where('role', 'admin_eng')->where('status', 'active')->get();
                    foreach ($adminEngUsers as $adminUser) {
                        $this->sendNotification(
                            $adminUser,
                            $ticket,
                            'Ticket Approved by OM',
                            'Ticket #' . $ticket->ticket_number . ' has been approved by OM and is ready for technician assignment',
                            'assignment'
                        );
                    }

                    // Juga notify reporter
                    $this->sendNotification(
                        $ticket->user,
                        $ticket,
                        'Ticket Approved by OM',
                        'Your ticket #' . $ticket->ticket_number . ' has been approved by OM. Engineering will assign a technician soon.',
                        'info'
                    );
                    break;

                case 'gm':
                    // Quick approve dari pending_gm ke ready_for_closure
                    $ticket->update([
                        'status' => 'ready_for_closure',
                        'current_stage' => 8,
                        'approval_status' => 'approved',
                    ]);

                    $approval = TicketApproval::firstOrCreate(['ticket_id' => $ticket->id]);
                    $approval->update([
                        'gm_approved' => true,
                        'gm_approved_by' => $user->id,
                        'gm_approved_at' => now(),
                        'status' => 'approved'
                    ]);

                    // Notify Admin Engineering
                    $adminEngUsers = User::where('role', 'admin_eng')->where('status', 'active')->get();
                    foreach ($adminEngUsers as $adminUser) {
                        $this->sendNotification(
                            $adminUser,
                            $ticket,
                            'Ticket Ready for Administrative Closure',
                            'Ticket #' . $ticket->ticket_number . ' has been approved by GM and is ready for administrative closure.',
                            'closure'
                        );
                    }

                    // Notify reporter dan technician
                    $this->sendNotification(
                        $ticket->user,
                        $ticket,
                        'Ticket Approved by GM',
                        'Your ticket #' . $ticket->ticket_number . ' has been approved by GM',
                        'closure'
                    );

                    if ($ticket->assigned_to) {
                        $this->sendNotification(
                            User::find($ticket->assigned_to),
                            $ticket,
                            'Ticket Approved by GM',
                            'Ticket #' . $ticket->ticket_number . ' has been approved by GM',
                            'closure'
                        );
                    }
                    break;
            }

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'action' => $role . '_approved_quick',
                'description' => ucfirst($role) . ' approved ticket using saved signature',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ticket approved successfully using saved signature',
                'redirect' => route('tickets.show', $ticket->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Quick approve error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to quick approve: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save signature from data URL
     */
    private function saveSignature($signatureData, $ticketNumber, $userId, $stage)
    {
        if (!preg_match('#^data:image/\w+;base64,#i', $signatureData)) {
            throw new \Exception('Invalid signature data URL format');
        }

        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signatureData));

        if ($imageData === false) {
            throw new \Exception('Failed to decode base64 signature data');
        }

        $fileName = 'signature_' . $ticketNumber . '_stage' . $stage . '_user' . $userId . '_' . time() . '.png';
        $filePath = 'signatures/' . $fileName;

        Storage::disk('public')->makeDirectory('signatures', 0755, true);
        $saved = Storage::disk('public')->put($filePath, $imageData);

        if (!$saved) {
            throw new \Exception('Failed to save signature file');
        }

        return $filePath;
    }

    /**
     * Send notification (both in-app and email)
     */
    private function sendNotification($user, $ticket, $title, $message, $type = 'info')
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

            // Send email notification
            if (config('mail.mailers.smtp.host')) {
                try {
                    Mail::to($user->email)->queue(new TicketNotification(
                        $user,
                        $ticket,
                        $title,
                        $message,
                        $type
                    ));
                } catch (\Exception $e) {
                    Log::warning('Email notification failed for user ' . $user->id . ': ' . $e->getMessage());
                }
            } else {
                Log::info('Email not configured, skipping email notification for user ' . $user->id);
            }

        } catch (\Exception $e) {
            Log::error('Notification creation failed: ' . $e->getMessage());
        }
    }
}
