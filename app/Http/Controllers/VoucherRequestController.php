<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Models\VoucherRequest;
use App\Models\VoucherAttachment;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Mail\VRNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class VoucherRequestController extends Controller
{
    /**
     * Display main index page with filters
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // ========== RESTRICTION: HANYA ROLE TERTENTU ==========
        $allowedRoles = ['admin_eng', 'om', 'gm', 'superadmin'];

        if (!in_array($user->role, $allowedRoles)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to access purchase requests.'
                ], 403);
            }
            abort(403, 'You do not have permission to access purchase requests.');
        }

        // Get tickets with status pending_vr that DO NOT have any voucher request yet
        $pendingVrTickets = Ticket::where('status', 'pending_vr')
            ->where('current_stage', 5)
            ->whereDoesntHave('voucherRequests')
            ->with(['user', 'priority', 'category'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get voucher requests with filters
        $query = VoucherRequest::with(['ticket', 'creator', 'attachments']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('vr_number', 'LIKE', "%{$search}%")
                    ->orWhere('notes', 'LIKE', "%{$search}%")
                    ->orWhereHas('ticket', function ($q2) use ($search) {
                        $q2->where('ticket_number', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $voucherRequests = $query->orderBy('created_at', 'desc')->paginate(10);

        // For AJAX request
        if ($request->ajax()) {
            if ($request->has('export')) {
                return $this->export($request);
            }
            return view('voucher-requests.partials.pr-cards', compact('voucherRequests'))->render();
        }

        return view('voucher-requests.index', compact('voucherRequests', 'pendingVrTickets'));
    }

    /**
     * Get filtered list of PRs via AJAX
     */
    public function getList(Request $request)
    {
        return $this->index($request);
    }

    /**
     * Get statistics for all PR statuses
     */
    public function getStats()
    {
        $stats = [
            'all' => VoucherRequest::count(),
            'pending' => VoucherRequest::where('status', 'pending')->count(),
            'admin_approved' => VoucherRequest::where('status', 'admin_approved')->count(),
            'om_approved' => VoucherRequest::where('status', 'om_approved')->count(),
            'gm_approved' => VoucherRequest::where('status', 'gm_approved')->count(),
            'paid' => VoucherRequest::where('status', 'paid')->count(),
            'rejected' => VoucherRequest::where('status', 'rejected')->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Generate new PR number
     */
    public function generateNumber()
    {
        $vrNumber = VoucherRequest::generateVrNumber();

        return response()->json([
            'success' => true,
            'vr_number' => $vrNumber
        ]);
    }

    /**
     * Store new Purchase Request
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['admin_eng'])) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to create purchase requests'
            ], 403);
        }

        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'notes' => 'nullable|string',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        $ticket = Ticket::findOrFail($request->ticket_id);

        if ($ticket->status !== 'pending_vr') {
            return response()->json([
                'success' => false,
                'message' => 'Ticket is not in PR Approval status'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $vrNumber = VoucherRequest::generateVrNumber();

            $voucherRequest = VoucherRequest::create([
                'vr_number' => $vrNumber,
                'ticket_id' => $ticket->id,
                'notes' => $request->notes,
                'status' => 'pending',
                'created_by' => $user->id,
                'admin_approved' => false,
                'om_approved' => false,
                'gm_approved' => false,
            ]);

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $fileName = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                    $filePath = $photo->storeAs('vouchers/photos', $fileName, 'public');

                    VoucherAttachment::create([
                        'voucher_request_id' => $voucherRequest->id,
                        'file_name' => $photo->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => $photo->getClientMimeType(),
                        'file_size' => $photo->getSize(),
                        'uploaded_by' => $user->id,
                        'created_at' => now(),
                    ]);
                }
            }

            $approval = $ticket->approval;
            if ($approval) {
                $approval->update([
                    'vr_created_at' => now(),
                    'vr_created_by' => $user->id,
                ]);
            }

            \App\Models\TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'comment' => "PR #{$vrNumber} created with " . ($request->hasFile('photos') ? count($request->file('photos')) : 0) . " photos.",
                'is_internal' => 0,
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'action' => 'created',
                'description' => "Purchase Request #{$vrNumber} created with " . ($request->hasFile('photos') ? count($request->file('photos')) : 0) . " photos",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            $adminEngUsers = User::where('role', 'admin_eng')
                ->where('status', 'active')
                ->get();

            foreach ($adminEngUsers as $adminUser) {
                $this->sendNotification(
                    $adminUser,
                    $ticket,
                    $voucherRequest,
                    'New Purchase Request Created',
                    "A new purchase request #{$vrNumber} has been created for ticket #{$ticket->ticket_number} and needs your approval.",
                    'vr_request'
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase request created successfully',
                'vr_id' => $voucherRequest->id,
                'vr_number' => $vrNumber
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create PR: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create purchase request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show PR details (for modal)
     */
    public function show($id)
    {
        $vr = VoucherRequest::with([
            'ticket',
            'ticket.user',
            'ticket.priority',
            'ticket.category',
            'creator',
            'attachments',
            'adminApprover',
            'omApprover',
            'gmApprover'
        ])->findOrFail($id);

        $html = view('voucher-requests.partials.detail-modal', compact('vr'))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Approve Purchase Request - FIXED!
     */
    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        $vr = VoucherRequest::with('ticket')->findOrFail($id);

        // Cek apakah user bisa approve
        if (!$vr->canApprove($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to approve this purchase request'
            ], 403);
        }

        $useSavedSignature = $request->boolean('use_saved_signature');

        // Validasi berdasarkan opsi signature
        if (!$useSavedSignature) {
            $request->validate([
                'signature_data' => 'required|string',
                'current_password' => 'required|string'
            ]);

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 422);
            }
        } else {
            // Cek apakah user punya saved signature
            if (!$user->has_signature || !$user->signature_path) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have a saved signature. Please upload one in your profile first.'
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            $signaturePath = null;
            $currentStage = $vr->getCurrentApproverRole();

            if ($useSavedSignature) {
                // Gunakan saved signature
                $signaturePath = 'signatures/pr/' . $vr->vr_number . '_' . $currentStage . '_' . $user->id . '_' . time() . '.png';
                Storage::disk('public')->makeDirectory('signatures/pr');
                Storage::disk('public')->copy($user->signature_path, $signaturePath);
            } else {
                // Draw new signature
                $signaturePath = $this->saveSignature(
                    $request->signature_data,
                    $vr->vr_number,
                    $user->id,
                    $currentStage
                );
            }

            $newStatus = $vr->status;

            if ($user->role === 'admin_eng') {
                $vr->update([
                    'admin_approved' => true,
                    'admin_approved_by' => $user->id,
                    'admin_approved_at' => now(),
                    'status' => 'admin_approved'
                ]);
                $newStatus = 'admin_approved';
            } elseif ($user->role === 'om') {
                $vr->update([
                    'om_approved' => true,
                    'om_approved_by' => $user->id,
                    'om_approved_at' => now(),
                    'status' => 'om_approved'
                ]);
                $newStatus = 'om_approved';
            } elseif ($user->role === 'gm') {
                $vr->update([
                    'gm_approved' => true,
                    'gm_approved_by' => $user->id,
                    'gm_approved_at' => now(),
                    'status' => 'gm_approved'
                ]);
                $newStatus = 'gm_approved';
            }

            // Add comment ke ticket
            \App\Models\TicketComment::create([
                'ticket_id' => $vr->ticket_id,
                'user_id' => $user->id,
                'comment' => "PR #{$vr->vr_number} approved by " . ucfirst($user->role),
                'is_internal' => 0,
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $vr->ticket_id,
                'action' => 'updated',
                'description' => "PR #{$vr->vr_number} approved by " . ucfirst($user->role),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $this->notifyNextApprover($vr, $newStatus);

            if ($vr->isFullyApproved()) {
                $vr->ticket->update([
                    'status' => 'in_progress',
                    'current_stage' => 4,
                ]);

                if ($vr->ticket->assigned_to) {
                    $technician = User::find($vr->ticket->assigned_to);
                    if ($technician) {
                        $this->sendNotification(
                            $technician,
                            $vr->ticket,
                            $vr,
                            'PR Approved - Work Can Continue',
                            "PR #{$vr->vr_number} has been fully approved. You can continue working on ticket #{$vr->ticket->ticket_number}.",
                            'success'
                        );
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase request approved successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve PR: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject Purchase Request - FIXED!
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        $vr = VoucherRequest::with('ticket')->findOrFail($id);

        // Cek apakah user bisa reject
        if (!$vr->canReject($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to reject this purchase request'
            ], 403);
        }

        $request->validate([
            'rejection_reason' => 'required|string|min:5'
        ]);

        DB::beginTransaction();
        try {
            // UPDATE status menjadi REJECTED
            $vr->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason
            ]);

            // Add comment ke ticket
            \App\Models\TicketComment::create([
                'ticket_id' => $vr->ticket_id,
                'user_id' => $user->id,
                'comment' => "PR #{$vr->vr_number} rejected by " . ucfirst($user->role) . ". Reason: " . $request->rejection_reason,
                'is_internal' => 0,
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $vr->ticket_id,
                'action' => 'updated',
                'description' => "PR #{$vr->vr_number} rejected by " . ucfirst($user->role) . ". Reason: " . $request->rejection_reason,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Notifikasi ke creator
            $this->sendNotification(
                $vr->creator,
                $vr->ticket,
                $vr,
                'Purchase Request Rejected',
                "Your purchase request #{$vr->vr_number} has been rejected by " . ucfirst($user->role) . ". Reason: " . $request->rejection_reason,
                'rejection'
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase request rejected'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject PR: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark Purchase Request as Paid
     */
    public function markPaid(Request $request, $id)
    {
        $user = Auth::user();
        $vr = VoucherRequest::with('ticket')->findOrFail($id);

        if (!$vr->canMarkPaid($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to mark this purchase request as paid'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $vr->update([
                'status' => 'paid',
                'notes' => $request->notes ? $vr->notes . "\n\n[PAID] " . $request->notes : $vr->notes
            ]);

            \App\Models\TicketComment::create([
                'ticket_id' => $vr->ticket_id,
                'user_id' => $user->id,
                'comment' => "PR #{$vr->vr_number} marked as paid. " . ($request->notes ? "Notes: " . $request->notes : ""),
                'is_internal' => 0,
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $vr->ticket_id,
                'action' => 'updated',
                'description' => "PR #{$vr->vr_number} marked as paid",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            if ($vr->ticket->assigned_to) {
                $technician = User::find($vr->ticket->assigned_to);
                if ($technician) {
                    $this->sendNotification(
                        $technician,
                        $vr->ticket,
                        $vr,
                        'Items Purchased',
                        "Items for PR #{$vr->vr_number} have been purchased. You can now proceed with the repair.",
                        'info'
                    );
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase request marked as paid'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to mark PR as paid: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark as paid: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete Purchase Request
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $vr = VoucherRequest::with('attachments')->findOrFail($id);

        if ($user->role !== 'superadmin' && $vr->created_by !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to delete this purchase request'
            ], 403);
        }

        if (!$vr->canDelete()) {
            return response()->json([
                'success' => false,
                'message' => 'This purchase request cannot be deleted in its current status'
            ], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($vr->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
                $attachment->delete();
            }

            $vr->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase request deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete PR: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export Purchase Requests to CSV or Print View
     */
    public function export(Request $request)
    {
        $user = Auth::user();

        $query = VoucherRequest::with(['ticket', 'creator', 'attachments', 'adminApprover', 'omApprover', 'gmApprover']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('vr_number', 'LIKE', "%{$search}%")
                    ->orWhere('notes', 'LIKE', "%{$search}%")
                    ->orWhereHas('ticket', function ($q2) use ($search) {
                        $q2->where('ticket_number', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $prs = $query->orderBy('created_at', 'desc')->get();

        $format = $request->export;

        if ($format === 'csv') {
            return $this->exportToCSV($prs);
        } elseif ($format === 'pdf') {
            return view('voucher-requests.exports.print', compact('prs'));
        }

        return redirect()->route('voucher-requests.index')->with('error', 'Invalid export format');
    }

    /**
     * Export to CSV
     */
    private function exportToCSV($prs)
    {
        $filename = 'purchase_requests_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($prs) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'PR Number',
                'Ticket Number',
                'Ticket Title',
                'Status',
                'Notes',
                'Created By',
                'Created At',
                'Admin Approved By',
                'Admin Approved At',
                'OM Approved By',
                'OM Approved At',
                'GM Approved By',
                'GM Approved At',
                'Rejection Reason',
                'Photos Count'
            ]);

            foreach ($prs as $pr) {
                $statusDisplay = $this->getStatusDisplay($pr->status);

                fputcsv($file, [
                    $pr->vr_number,
                    $pr->ticket->ticket_number ?? 'N/A',
                    $pr->ticket->title ?? 'N/A',
                    $statusDisplay,
                    $pr->notes ?? '-',
                    $pr->creator->name ?? 'Unknown',
                    $pr->created_at->format('Y-m-d H:i:s'),
                    $pr->adminApprover->name ?? '-',
                    $pr->admin_approved_at ? $pr->admin_approved_at->format('Y-m-d H:i:s') : '-',
                    $pr->omApprover->name ?? '-',
                    $pr->om_approved_at ? $pr->om_approved_at->format('Y-m-d H:i:s') : '-',
                    $pr->gmApprover->name ?? '-',
                    $pr->gm_approved_at ? $pr->gm_approved_at->format('Y-m-d H:i:s') : '-',
                    $pr->rejection_reason ?? '-',
                    $pr->attachments->count()
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get status display name
     */
    private function getStatusDisplay($status)
    {
        $statusMap = [
            'pending' => 'Pending Admin',
            'admin_approved' => 'Admin Approved',
            'om_approved' => 'OM Approved',
            'gm_approved' => 'GM Approved',
            'paid' => 'Paid',
            'rejected' => 'Rejected',
        ];
        return $statusMap[$status] ?? ucfirst($status);
    }

    /**
     * Save signature from data URL - Ukuran 300x200 (3:2 ratio)
     */
    private function saveSignature($signatureData, $vrNumber, $userId, $stage)
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

        // Target ukuran 300x200 (3:2 ratio)
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

        $fileName = 'signature_pr_' . $vrNumber . '_' . $stage . '_user' . $userId . '_' . time() . '.png';
        $filePath = 'signatures/pr/' . $fileName;

        Storage::disk('public')->makeDirectory('signatures/pr', 0755, true);
        $saved = Storage::disk('public')->put($filePath, $imageData);

        if (!$saved) {
            throw new \Exception('Failed to save signature file');
        }

        return $filePath;
    }

    /**
     * Notify next approver
     */
    private function notifyNextApprover($vr, $currentStatus)
    {
        $nextRole = null;

        switch ($currentStatus) {
            case 'admin_approved':
                $nextRole = 'om';
                $title = 'PR Needs OM Approval';
                $message = "PR #{$vr->vr_number} for ticket #{$vr->ticket->ticket_number} needs your approval.";
                break;
            case 'om_approved':
                $nextRole = 'gm';
                $title = 'PR Needs GM Approval';
                $message = "PR #{$vr->vr_number} for ticket #{$vr->ticket->ticket_number} needs your final approval.";
                break;
            default:
                return;
        }

        $nextUsers = User::where('role', $nextRole)->where('status', 'active')->get();

        foreach ($nextUsers as $user) {
            $this->sendNotification($user, $vr->ticket, $vr, $title, $message, 'approval');
        }
    }

    /**
     * Send notification (in-app + email)
     */
    /**
     * Send notification (in-app + email)
     */
    private function sendNotification($user, $ticket, $vr, $title, $message, $type = 'info')
    {
        try {
            // In-app notification
            Notification::create([
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'is_read' => false,
            ]);

            // Email notification - PAKAI VRNotification!
            if (config('mail.mailers.smtp.host')) {
                try {
                    Mail::to($user->email)->queue(new \App\Mail\VRNotification(
                        $user,
                        $ticket,
                        $title,
                        $message,
                        $type,
                        $vr
                    ));
                } catch (\Exception $e) {
                    Log::warning('Email notification failed for user ' . $user->id . ': ' . $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            Log::error('Failed to send notification: ' . $e->getMessage());
        }
    }
}
