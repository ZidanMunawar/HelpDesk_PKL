<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\TicketNotification;
use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Notification;
use App\Models\Signature;
use App\Models\Ticket;
use App\Models\TicketApproval;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
                return $ticket->user_id === $user->id ||
                    $ticket->department_id === $user->department_id;

            default:
                return false;
        }
    }

    /**
     * Check if user can comment on ticket
     */
    private function canComment($user, $ticket)
    {
        if ($user->role === 'admin_eng') {
            return true;
        }

        if ($user->role === 'manager' && $ticket->department_id === $user->department_id) {
            return true;
        }

        if ($ticket->user_id === $user->id) {
            return true;
        }

        if ($user->role === 'technician' && $ticket->assigned_to === $user->id) {
            return true;
        }

        return false;
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

        if (!in_array($user->role, ['admin_eng', 'om', 'gm'])) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to create new signatures'
            ], 403);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 422);
        }

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
            return true;
        }

        $user = Auth::user();
        return Hash::check($request->current_password, $user->password);
    }

    /**
     * Admin Engineering receive ticket - DENGAN OPSI SIGNATURE BARU
     * PERUBAHAN: Tambah notifikasi ke User bahwa ticket diterima
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
            'current_password' => 'nullable|string'
        ]);

        $ticket = Ticket::findOrFail($id);

        if ($ticket->status !== 'open') {
            return response()->json([
                'success' => false,
                'message' => 'Ticket is not in open status'
            ], 403);
        }

        if ($request->has('current_password') && !$this->verifyPasswordForNewSignature($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $signaturePath = $this->saveSignature(
                $request->signature_data,
                $ticket->ticket_number,
                $user->id,
                2
            );

            if ($request->has('save_signature') && $request->boolean('save_signature')) {
                if ($user->signature_path) {
                    Storage::disk('public')->delete($user->signature_path);
                }

                $user->update([
                    'signature_path' => $signaturePath,
                    'has_signature' => true,
                    'signature_updated_at' => now()
                ]);
            }

            Signature::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'signature_type' => 'approver',
                'stage' => 2,
                'signature_path' => $signaturePath,
                'signed_at' => now(),
                'ip_address' => $request->ip(),
            ]);

            $ticket->update([
                'status' => 'received',
                'current_stage' => 2,
            ]);

            $approval = TicketApproval::firstOrCreate(['ticket_id' => $ticket->id]);
            $approval->update([
                'admin_eng_received' => true,
                'admin_eng_received_by' => $user->id,
                'admin_eng_received_at' => now(),
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'action' => 'received',
                'description' => 'Ticket received by Admin Engineering',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // ✅ PERUBAHAN: Notifikasi ke User bahwa ticket diterima
            $this->sendNotification(
                $ticket->user,
                $ticket,
                'MR Received by Engineering',
                'Your maintenance request #' . $ticket->ticket_number . ' has been received by the Engineering Department and is being processed.',
                'info'
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'MR received successfully',
                'redirect' => route('tickets.show', $ticket->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Receive MR error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to receive MR: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * OM Approve/Reject ticket - DENGAN OPSI SIGNATURE BARU
     * PERUBAHAN:
     * - Hapus notifikasi ke user saat approve
     * - Tambah notifikasi ke Admin Eng saat reject
     * - User dapat notifikasi umum saat reject
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

        if ($request->has('current_password') && !$this->verifyPasswordForNewSignature($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 422);
        }

        DB::beginTransaction();
        try {
            if ($request->action === 'reject') {
                $ticket->update([
                    'status' => 'cancelled',
                    'current_stage' => 1,
                    'approval_status' => 'rejected',
                    'closed_at' => now(),
                ]);

                $approval = TicketApproval::firstOrCreate(['ticket_id' => $ticket->id]);
                $approval->update([
                    'rejection_reason' => $request->rejection_reason,
                    'rejection_note' => 'Rejected by OM: ' . $request->rejection_reason
                ]);

                ActivityLog::create([
                    'user_id' => $user->id,
                    'ticket_id' => $ticket->id,
                    'action' => 'om_rejected',
                    'description' => 'Ticket rejected by OM',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                // ✅ PERUBAHAN: Notifikasi ke Admin Eng bahwa OM menolak
                $adminEngUsers = User::where('role', 'admin_eng')->where('status', 'active')->get();
                foreach ($adminEngUsers as $adminUser) {
                    $this->sendNotification(
                        $adminUser,
                        $ticket,
                        'MR Rejected by OM',
                        'MR #' . $ticket->ticket_number . ' was rejected by OM. Reason: ' . $request->rejection_reason,
                        'rejection'
                    );
                }

                // ✅ PERUBAHAN: Notifikasi umum ke user (tidak detail)
                $this->sendNotification(
                    $ticket->user,
                    $ticket,
                    'Your Maintenance Request Has Been Rejected',
                    'Your MR #' . $ticket->ticket_number . ' has been rejected. Please contact support for more information.',
                    'rejection'
                );

            } else {
                $signaturePath = $this->saveSignature(
                    $request->signature_data,
                    $ticket->ticket_number,
                    $user->id,
                    3
                );

                if ($request->has('save_signature') && $request->boolean('save_signature')) {
                    if ($user->signature_path) {
                        Storage::disk('public')->delete($user->signature_path);
                    }

                    $user->update([
                        'signature_path' => $signaturePath,
                        'has_signature' => true,
                        'signature_updated_at' => now()
                    ]);
                }

                Signature::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'signature_type' => 'approver',
                    'stage' => 3,
                    'signature_path' => $signaturePath,
                    'signed_at' => now(),
                    'ip_address' => $request->ip(),
                ]);

                $ticket->update([
                    'status' => 'pending_om',
                    'current_stage' => 3,
                ]);

                $approval = TicketApproval::firstOrCreate(['ticket_id' => $ticket->id]);
                $approval->update([
                    'om_approved' => true,
                    'om_approved_by' => $user->id,
                    'om_approved_at' => now(),
                ]);

                ActivityLog::create([
                    'user_id' => $user->id,
                    'ticket_id' => $ticket->id,
                    'action' => 'om_approved',
                    'description' => 'Ticket approved by OM',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                // ✅ Notifikasi ke Admin Eng (tetap dipertahankan)
                $adminEngUsers = User::where('role', 'admin_eng')->where('status', 'active')->get();
                foreach ($adminEngUsers as $adminUser) {
                    $this->sendNotification(
                        $adminUser,
                        $ticket,
                        'MR Approved by OM',
                        'MR #' . $ticket->ticket_number . ' has been approved by OM and is ready for technician assignment.',
                        'assignment'
                    );
                }

                // ❌ HAPUS: Tidak ada notifikasi ke user untuk OM Approve
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
     * Technician complete work - DENGAN FOLLOW-UP
     * PERUBAHAN: User tetap dapat notifikasi baik dengan atau tanpa follow-up
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
            'completion_notes' => 'required|string|min:10',
            'is_followup' => 'nullable|boolean'
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
            $signaturePath = $this->saveSignature(
                $request->signature_data,
                $ticket->ticket_number,
                $user->id,
                6
            );

            Signature::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'signature_type' => 'technician',
                'stage' => 6,
                'signature_path' => $signaturePath,
                'signed_at' => now(),
                'ip_address' => $request->ip(),
            ]);

            $ticket->update([
                'status' => 'completed',
                'current_stage' => 6,
                'resolved_at' => now(),
            ]);

            $comment = "Work completed by technician.\n";
            $comment .= "Completion Notes: " . $request->completion_notes;

            $isFollowUp = $request->boolean('is_followup', true);

            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'comment' => $comment,
                'is_internal' => 0,
                'is_followup' => $isFollowUp,
            ]);

            if (!$isFollowUp) {
                $approval = TicketApproval::firstOrCreate(['ticket_id' => $ticket->id]);
                $approval->update([
                    'needs_admin_followup' => true,
                ]);

                ActivityLog::create([
                    'user_id' => $user->id,
                    'ticket_id' => $ticket->id,
                    'action' => 'completion_notes_skipped',
                    'description' => 'Technician skipped follow-up notes, admin needs to add',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                $adminEngUsers = User::where('role', 'admin_eng')->where('status', 'active')->get();
                foreach ($adminEngUsers as $adminUser) {
                    $this->sendNotification(
                        $adminUser,
                        $ticket,
                        'Follow-up Notes Required',
                        'Technician completed MR #' . $ticket->ticket_number . ' without follow-up notes. Please add them.',
                        'warning'
                    );
                }
            } else {
                ActivityLog::create([
                    'user_id' => $user->id,
                    'ticket_id' => $ticket->id,
                    'action' => 'completion_note',
                    'description' => 'Technician added follow-up notes: ' . Str::limit($request->completion_notes, 100),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }

            // ✅ PERUBAHAN: User tetap mendapat notifikasi work completed (baik dengan atau tanpa follow-up)
            $this->sendNotification(
                $ticket->user,
                $ticket,
                'Work Completed - Please Check',
                'The work on your MR #' . $ticket->ticket_number . ' has been completed. Please check and confirm the result.',
                'check'
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Work marked as completed.' . (!$isFollowUp ? ' Admin will add follow-up notes.' : ''),
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
     * Admin add follow-up notes
     */
    public function addFollowUpNotes(Request $request, $id)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['admin_eng', 'superadmin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only Admin Engineering can add follow-up notes'
            ], 403);
        }

        $request->validate([
            'follow_up_notes' => 'required|string|min:10'
        ]);

        $ticket = Ticket::findOrFail($id);

        DB::beginTransaction();
        try {
            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'comment' => "Admin Follow-up Notes:\n" . $request->follow_up_notes,
                'is_internal' => 0,
                'is_followup' => true,
            ]);

            $approval = TicketApproval::firstOrCreate(['ticket_id' => $ticket->id]);
            $approval->update([
                'needs_admin_followup' => false,
                'admin_followup_added_at' => now(),
                'admin_followup_added_by' => $user->id,
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'action' => 'admin_followup_added',
                'description' => 'Admin added follow-up notes',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            if ($ticket->assigned_to) {
                $this->sendNotification(
                    User::find($ticket->assigned_to),
                    $ticket,
                    'Follow-up Notes Added',
                    'Admin has added follow-up notes to MR #' . $ticket->ticket_number,
                    'info'
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Follow-up notes added successfully',
                'redirect' => route('tickets.show', $ticket->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Add follow-up notes error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add follow-up notes: ' . $e->getMessage()
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
            $ticket->update([
                'status' => 'pending_vr',
                'current_stage' => 5,
            ]);

            $approval = TicketApproval::firstOrCreate(['ticket_id' => $ticket->id]);
            $approval->update([
                'needs_vr' => true,
                'vr_reason' => $request->vr_reason,
                'vr_created_by' => $user->id,
                'vr_created_at' => now(),
            ]);

            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'comment' => "VR Requested. Reason: " . $request->vr_reason .
                    ($request->estimated_cost ? "\nEstimated Cost: " . number_format($request->estimated_cost, 2) : "") .
                    ($request->required_items ? "\nRequired Items: " . $request->required_items : ""),
                'is_internal' => 0,
                'is_followup' => true,
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'action' => 'vr_requested',
                'description' => 'VR requested by technician',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $adminEngUsers = User::where('role', 'admin_eng')->where('status', 'active')->get();
            foreach ($adminEngUsers as $adminUser) {
                $this->sendNotification(
                    $adminUser,
                    $ticket,
                    'PR Requested by Technician',
                    'MR #' . $ticket->ticket_number . ' requires PR. Reason: ' . $request->vr_reason,
                    'vr_request'
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'PR request submitted. Admin Engineering has been notified.',
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
     * User/Manager check completion
     * PERUBAHAN: Tambah notifikasi ke Admin Eng saat reject
     */
    public function userCheck(Request $request, $id)
    {
        $user = Auth::user();
        $ticket = Ticket::findOrFail($id);

        $canCheck = false;

        if ($user->role === 'user' && $ticket->user_id === $user->id) {
            $canCheck = true;
        } elseif ($user->role === 'admin_eng' && $ticket->user_id === $user->id) {
            $canCheck = true;
        } elseif ($user->role === 'manager' && $ticket->department_id === $user->department_id) {
            $canCheck = true;
        }

        if (!$canCheck) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to check this ticket'
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
                $ticket->update([
                    'status' => 'in_progress',
                    'current_stage' => 4,
                ]);

                Signature::where('ticket_id', $ticket->id)
                    ->where('stage', 6)
                    ->where('signature_type', 'technician')
                    ->delete();

                TicketComment::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'comment' => "Completion rejected by " . $user->name . ". Reason: " . $request->rejection_reason,
                    'is_internal' => 0,
                ]);

                ActivityLog::create([
                    'user_id' => $user->id,
                    'ticket_id' => $ticket->id,
                    'action' => 'rejected',
                    'description' => 'Completion rejected by reporter',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                if ($ticket->assigned_to) {
                    $technician = User::find($ticket->assigned_to);
                    $this->sendNotification(
                        $technician,
                        $ticket,
                        'Completion Rejected',
                        'Your work on MR #' . $ticket->ticket_number . ' has been rejected. Reason: ' . $request->rejection_reason,
                        'rejection'
                    );
                }

                // ✅ PERUBAHAN: Tambah notifikasi ke Admin Eng
                $adminEngUsers = User::where('role', 'admin_eng')->where('status', 'active')->get();
                foreach ($adminEngUsers as $adminUser) {
                    $this->sendNotification(
                        $adminUser,
                        $ticket,
                        'Completion Rejected by User',
                        'User/Manager rejected the completion on MR #' . $ticket->ticket_number . '. Reason: ' . $request->rejection_reason,
                        'rejection'
                    );
                }

            } else {
                if ($user->role === 'manager' && !empty($user->signature_path)) {
                    $signaturePath = 'signatures/quick_' . $ticket->ticket_number . '_stage7_manager_' . $user->id . '_' . time() . '.png';
                    Storage::disk('public')->copy($user->signature_path, $signaturePath);
                } else {
                    $signaturePath = $this->saveSignature(
                        $request->signature_data,
                        $ticket->ticket_number,
                        $user->id,
                        7
                    );
                }

                Signature::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'signature_type' => 'reporter',
                    'stage' => 7,
                    'signature_path' => $signaturePath,
                    'signed_at' => now(),
                    'ip_address' => $request->ip(),
                ]);

                $ticket->update([
                    'status' => 'pending_gm',
                    'current_stage' => 7,
                ]);

                $approval = TicketApproval::firstOrCreate(['ticket_id' => $ticket->id]);
                $approval->update([
                    'user_checked' => true,
                    'user_checked_by' => $user->id,
                    'user_checked_at' => now(),
                ]);

                TicketComment::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'comment' => ucfirst($user->role) . " accepted the completion",
                    'is_internal' => 0,
                ]);

                ActivityLog::create([
                    'user_id' => $user->id,
                    'ticket_id' => $ticket->id,
                    'action' => 'accepted',
                    'description' => 'Completion accepted by ' . $user->role,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                $gmUsers = User::where('role', 'gm')->where('status', 'active')->get();
                foreach ($gmUsers as $gmUser) {
                    $this->sendNotification(
                        $gmUser,
                        $ticket,
                        'MR Needs GM Approval',
                        'MR #' . $ticket->ticket_number . ' has been confirmed by the user and needs your final approval.',
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
     * PERUBAHAN:
     * - Hapus notifikasi ke User dan Technician saat approve
     * - Hanya Admin Eng yang dapat notifikasi approve
     * - Tambah notifikasi ke Admin Eng saat reject
     * - User dapat notifikasi umum saat reject
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

        if ($request->has('current_password') && !$this->verifyPasswordForNewSignature($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 422);
        }

        DB::beginTransaction();
        try {
            if ($request->action === 'reject') {
                $ticket->update([
                    'status' => 'cancelled',
                    'current_stage' => 1,
                    'approval_status' => 'rejected',
                    'closed_at' => now(),
                ]);

                $approval = TicketApproval::firstOrCreate(['ticket_id' => $ticket->id]);
                $approval->update([
                    'rejection_reason' => $request->rejection_reason,
                    'rejection_note' => 'Rejected by GM: ' . $request->rejection_reason
                ]);

                ActivityLog::create([
                    'user_id' => $user->id,
                    'ticket_id' => $ticket->id,
                    'action' => 'gm_rejected',
                    'description' => 'Ticket rejected by GM',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                // ✅ PERUBAHAN: Notifikasi ke Admin Eng bahwa GM menolak
                $adminEngUsers = User::where('role', 'admin_eng')->where('status', 'active')->get();
                foreach ($adminEngUsers as $adminUser) {
                    $this->sendNotification(
                        $adminUser,
                        $ticket,
                        'MR Rejected by GM',
                        'MR #' . $ticket->ticket_number . ' was rejected by GM. Reason: ' . $request->rejection_reason,
                        'rejection'
                    );
                }

                // ✅ PERUBAHAN: Notifikasi umum ke user (tidak detail)
                $this->sendNotification(
                    $ticket->user,
                    $ticket,
                    'Your Maintenance Request Has Been Rejected',
                    'Your MR #' . $ticket->ticket_number . ' has been rejected. Please contact support for more information.',
                    'rejection'
                );

            } else {
                $signaturePath = $this->saveSignature(
                    $request->signature_data,
                    $ticket->ticket_number,
                    $user->id,
                    8
                );

                if ($request->has('save_signature') && $request->boolean('save_signature')) {
                    if ($user->signature_path) {
                        Storage::disk('public')->delete($user->signature_path);
                    }

                    $user->update([
                        'signature_path' => $signaturePath,
                        'has_signature' => true,
                        'signature_updated_at' => now()
                    ]);
                }

                Signature::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'signature_type' => 'approver',
                    'stage' => 8,
                    'signature_path' => $signaturePath,
                    'signed_at' => now(),
                    'ip_address' => $request->ip(),
                ]);

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
                ]);

                ActivityLog::create([
                    'user_id' => $user->id,
                    'ticket_id' => $ticket->id,
                    'action' => 'gm_approved',
                    'description' => 'Ticket approved by GM - Ready for administrative closure',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                // ✅ PERUBAHAN: Hanya Admin Eng yang mendapat notifikasi approve
                $adminEngUsers = User::where('role', 'admin_eng')->where('status', 'active')->get();
                foreach ($adminEngUsers as $adminUser) {
                    $this->sendNotification(
                        $adminUser,
                        $ticket,
                        'MR Ready for Administrative Closure',
                        'MR #' . $ticket->ticket_number . ' has been approved by GM and is ready for administrative closure.',
                        'closure'
                    );
                }

                // ❌ HAPUS: Notifikasi ke User dan Technician untuk GM Approve
                // User akan mendapat notifikasi di Tahap 15 saat ticket di-close
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

        $assignableStatuses = ['in_progress', 'pending_vr'];

        if (!in_array($ticket->status, $assignableStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket cannot be assigned in current status: ' . $ticket->status
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

            $newStatus = $ticket->status === 'pending_vr' ? 'pending_vr' : 'in_progress';

            $ticket->update([
                'assigned_to' => $request->assigned_to,
                'status' => $newStatus,
                'current_stage' => $ticket->current_stage,
                'due_date' => $request->due_date ?: $ticket->due_date,
            ]);

            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'comment' => "Ticket assigned to " . $technician->name .
                    ($request->due_date ? " with due date: " . \Carbon\Carbon::parse($request->due_date)->format('d M Y, H:i') : ""),
                'is_internal' => 0,
            ]);

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

            $this->sendNotification(
                $technician,
                $ticket,
                'New MR Assigned to You',
                'MR #' . $ticket->ticket_number . ' has been assigned to you' .
                ($request->due_date ? ". Due date: " . \Carbon\Carbon::parse($request->due_date)->format('d M Y, H:i') : ""),
                'assignment'
            );

            $this->sendNotification(
                $ticket->user,
                $ticket,
                'Technician Assigned to Your Request',
                'A technician has been assigned to your MR #' . $ticket->ticket_number . '. They will contact you if needed.',
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
     * PERUBAHAN: Hapus notifikasi ke Technician
     */
    public function cancelTicket(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $user = Auth::user();

        if (!in_array($user->role, ['admin_eng', 'superadmin']) && $ticket->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to cancel this ticket'
            ], 403);
        }

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
                'current_stage' => 1,
                'closed_at' => now(),
                'approval_status' => 'rejected',
            ]);

            $approval = TicketApproval::firstOrCreate(['ticket_id' => $ticket->id]);
            $approval->update([
                'rejection_reason' => $request->cancellation_reason
            ]);

            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'comment' => "Ticket cancelled. Reason: " . $request->cancellation_reason,
                'is_internal' => 0,
            ]);

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

            // ✅ PERUBAHAN: Hanya notifikasi ke User (hapus Technician)
            $this->sendNotification(
                $ticket->user,
                $ticket,
                'Your Maintenance Request Has Been Cancelled',
                'Your MR #' . $ticket->ticket_number . ' has been cancelled. Reason: ' . $request->cancellation_reason,
                'cancellation'
            );

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
     * Close ticket (final step by admin)
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

        if ($ticket->status !== 'ready_for_closure') {
            return response()->json([
                'success' => false,
                'message' => 'Ticket must be approved by GM and in "Ready for Closure" status'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $ticket->update([
                'status' => 'closed',
                'current_stage' => 9,
                'closed_at' => now(),
            ]);

            $approval = TicketApproval::firstOrCreate(['ticket_id' => $ticket->id]);
            $approval->update([
                'admin_check' => true,
                'admin_checked_by' => $user->id,
                'admin_checked_at' => now(),
            ]);

            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'comment' => "Ticket administratively closed by " . $user->name,
                'is_internal' => 0,
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'action' => 'admin_closed',
                'description' => 'Ticket administratively closed by ' . $user->name,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $this->sendNotification(
                $ticket->user,
                $ticket,
                'Your Maintenance Request Has Been Closed',
                'Your MR #' . $ticket->ticket_number . ' has been completed and closed. Thank you for using our service.',
                'closure'
            );

            if ($ticket->assigned_to) {
                $this->sendNotification(
                    User::find($ticket->assigned_to),
                    $ticket,
                    'Maintenance Request Closed',
                    'MR #' . $ticket->ticket_number . ' has been closed administratively.',
                    'closure'
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'MR administratively closed',
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

        if (!$this->canComment($user, $ticket)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to comment on this ticket'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $comment = TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'comment' => $request->comment,
                'is_internal' => 0,
            ]);

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

            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'action' => 'commented',
                'description' => $user->name . ' added a comment',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $notifiedUsers = [];

            if ($ticket->user_id !== $user->id) {
                $this->sendNotification(
                    $ticket->user,
                    $ticket,
                    'New Comment on Your Request',
                    'There is a new comment on your maintenance request #' . $ticket->ticket_number,
                    'comment'
                );
                $notifiedUsers[] = $ticket->user_id;
            }

            if ($ticket->assigned_to && $ticket->assigned_to !== $user->id) {
                $technician = User::find($ticket->assigned_to);
                if ($technician && !in_array($technician->id, $notifiedUsers)) {
                    $this->sendNotification(
                        $technician,
                        $ticket,
                        'New Comment on Maintenance Request',
                        'There is a new comment on your MR #' . $ticket->ticket_number,
                        'comment'
                    );
                    $notifiedUsers[] = $technician->id;
                }
            }

            if ($ticket->department && $ticket->department->manager_id && $ticket->department->manager_id !== $user->id) {
                $manager = User::find($ticket->department->manager_id);
                if ($manager && !in_array($manager->id, $notifiedUsers)) {
                    $this->sendNotification(
                        $manager,
                        $ticket,
                        'New Comment on Department Maintenance Request',
                        'There is a new comment on MR #' . $ticket->ticket_number . ' from department ' . ($ticket->department->name ?? 'N/A'),
                        'comment'
                    );
                    $notifiedUsers[] = $manager->id;
                }
            }

            $adminEngUsers = User::where('role', 'admin_eng')->where('status', 'active')->get();
            foreach ($adminEngUsers as $adminUser) {
                if (!in_array($adminUser->id, $notifiedUsers) && $adminUser->id !== $user->id) {
                    $this->sendNotification(
                        $adminUser,
                        $ticket,
                        'New Comment on Maintenance Request',
                        'There is a new comment on MR #' . $ticket->ticket_number . ' by ' . $user->name,
                        'comment'
                    );
                }
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
            $ticket->update([
                'status' => 'pending_om',
                'current_stage' => 3,
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'action' => 'continued_to_om',
                'description' => 'Ticket continued to OM for approval',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $omUsers = User::where('role', 'om')->where('status', 'active')->get();
            foreach ($omUsers as $omUser) {
                $this->sendNotification(
                    $omUser,
                    $ticket,
                    'MR Needs Your Approval',
                    'MR #' . $ticket->ticket_number . ' needs your approval. Priority: ' . ($ticket->priority->name ?? 'N/A'),
                    'approval'
                );
            }

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
     * Delete ticket (Superadmin only)
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

        if (!$request->has('password') || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password verification failed'
            ], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($ticket->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            foreach ($ticket->comments as $comment) {
                foreach ($comment->attachments as $attachment) {
                    Storage::disk('public')->delete($attachment->file_path);
                }
            }

            foreach ($ticket->signatures as $signature) {
                if ($signature->signature_path) {
                    Storage::disk('public')->delete($signature->signature_path);
                }
            }

            foreach ($ticket->voucherRequests as $vr) {
                $vr->items()->delete();
                $vr->delete();
            }

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
     * PERUBAHAN: Sesuaikan dengan alur notifikasi yang baru
     */
    public function quickApprove(Request $request, $id)
    {
        $user = Auth::user();
        $role = $user->role;

        if (!in_array($role, ['admin_eng', 'om', 'gm', 'manager'])) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized for quick approval'
            ], 403);
        }

        $ticket = Ticket::findOrFail($id);

        $validStatus = [];
        $stage = 0;
        $status = '';
        $actionType = '';

        switch ($role) {
            case 'admin_eng':
                $validStatus = ['open'];
                $stage = 2;
                $status = 'received';
                $actionType = 'receive';
                break;
            case 'om':
                $validStatus = ['pending_om'];
                $stage = 3;
                $status = 'in_progress';
                $actionType = 'om_approve';
                break;
            case 'gm':
                $validStatus = ['pending_gm'];
                $stage = 8;
                $status = 'ready_for_closure';
                $actionType = 'gm_approve';
                break;
            case 'manager':
                if ($ticket->department_id === $user->department_id && $ticket->status === 'completed') {
                    $validStatus = ['completed'];
                    $stage = 7;
                    $actionType = 'manager_check';
                }
                break;
        }

        if (!in_array($ticket->status, $validStatus)) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket is not in correct status for ' . strtoupper($role) . ' approval.'
            ], 403);
        }

        if (empty($user->signature_path) || !Storage::disk('public')->exists($user->signature_path)) {
            return response()->json([
                'success' => false,
                'message' => 'You need to have a saved signature first'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $signaturePath = 'signatures/quick_' . $ticket->ticket_number . '_stage' . $stage . '_user' . $user->id . '_' . time() . '.png';
            Storage::disk('public')->copy($user->signature_path, $signaturePath);

            Signature::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'signature_type' => $role === 'manager' ? 'reporter' : 'approver',
                'stage' => $stage,
                'signature_path' => $signaturePath,
                'signed_at' => now(),
                'ip_address' => $request->ip(),
            ]);

            switch ($role) {
                case 'admin_eng':
                    $ticket->update([
                        'status' => 'received',
                        'current_stage' => 2,
                    ]);

                    $approval = TicketApproval::firstOrCreate(['ticket_id' => $ticket->id]);
                    $approval->update([
                        'admin_eng_received' => true,
                        'admin_eng_received_by' => $user->id,
                        'admin_eng_received_at' => now(),
                    ]);

                    // ✅ Notifikasi ke User bahwa ticket diterima
                    $this->sendNotification(
                        $ticket->user,
                        $ticket,
                        'MR Received by Engineering',
                        'Your maintenance request #' . $ticket->ticket_number . ' has been received by the Engineering Department.',
                        'info'
                    );
                    break;

                case 'om':
                    $ticket->update([
                        'status' => 'in_progress',
                        'current_stage' => 4,
                    ]);

                    $approval = TicketApproval::firstOrCreate(['ticket_id' => $ticket->id]);
                    $approval->update([
                        'om_approved' => true,
                        'om_approved_by' => $user->id,
                        'om_approved_at' => now(),
                    ]);

                    // ✅ Notifikasi ke Admin Eng (tidak ke user)
                    $adminEngUsers = User::where('role', 'admin_eng')->where('status', 'active')->get();
                    foreach ($adminEngUsers as $adminUser) {
                        $this->sendNotification(
                            $adminUser,
                            $ticket,
                            'MR Approved by OM',
                            'MR #' . $ticket->ticket_number . ' has been approved by OM and is ready for technician assignment.',
                            'assignment'
                        );
                    }
                    break;

                case 'gm':
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
                    ]);

                    // ✅ Hanya Admin Eng yang mendapat notifikasi
                    $adminEngUsers = User::where('role', 'admin_eng')->where('status', 'active')->get();
                    foreach ($adminEngUsers as $adminUser) {
                        $this->sendNotification(
                            $adminUser,
                            $ticket,
                            'MR Ready for Administrative Closure',
                            'MR #' . $ticket->ticket_number . ' has been approved by GM and is ready for closure.',
                            'closure'
                        );
                    }
                    break;

                case 'manager':
                    $ticket->update([
                        'status' => 'pending_gm',
                        'current_stage' => 7,
                    ]);

                    $approval = TicketApproval::firstOrCreate(['ticket_id' => $ticket->id]);
                    $approval->update([
                        'user_checked' => true,
                        'user_checked_by' => $user->id,
                        'user_checked_at' => now(),
                    ]);

                    TicketComment::create([
                        'ticket_id' => $ticket->id,
                        'user_id' => $user->id,
                        'comment' => "Manager accepted the completion using saved signature",
                        'is_internal' => 0,
                    ]);

                    $gmUsers = User::where('role', 'gm')->where('status', 'active')->get();
                    foreach ($gmUsers as $gmUser) {
                        $this->sendNotification(
                            $gmUser,
                            $ticket,
                            'MR Needs GM Approval',
                            'MR #' . $ticket->ticket_number . ' has been confirmed and needs your final approval.',
                            'approval'
                        );
                    }
                    break;
            }

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
