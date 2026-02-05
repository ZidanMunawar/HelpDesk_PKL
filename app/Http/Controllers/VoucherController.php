<?php

namespace App\Http\Controllers;

use App\Models\VoucherRequest;
use App\Models\VoucherItem;
use App\Models\Ticket;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\Signature;
use App\Models\TicketComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Mail\VRNotification;

class VoucherController extends Controller
{
    /**
     * Display listing of VRs - SIMPLE tanpa filter
     */
    public function index()
    {
        $user = Auth::user();

        // Check permission - HANYA superadmin, admin_eng, om, gm
        if (!in_array($user->role, ['superadmin', 'admin_eng', 'om', 'gm'])) {
            abort(403, 'Unauthorized access to voucher requests');
        }

        $query = VoucherRequest::with([
            'ticket',
            'creator',
            'items',
            'adminApprover',
            'omApprover',
            'gmApprover'
        ])->latest();

        // Filter berdasarkan role
        switch ($user->role) {
            case 'admin_eng':
                // Admin bisa lihat semua VR yang dia buat atau pending untuk dia approve
                $query->where('created_by', $user->id)
                    ->orWhere(function ($q) use ($user) {
                        $q->where('status', 'pending');
                    });
                break;

            case 'om':
                // OM bisa lihat VR yang sudah di-approve admin
                $query->where('status', 'admin_approved')
                    ->where('admin_approved', true);
                break;

            case 'gm':
                // GM bisa lihat VR yang sudah di-approve OM
                $query->where('status', 'om_approved')
                    ->where('om_approved', true);
                break;

            // Superadmin bisa lihat semua (no filter)
        }

        $vrs = $query->paginate(20);

        return view('vouchers.index', compact('vrs'));
    }

    /**
     * Show modal for creating VR - HANYA admin_eng
     */
    public function createModal($ticketId = null)
    {
        $user = Auth::user();

        if ($user->role !== 'admin_eng') {
            return response()->json([
                'success' => false,
                'message' => 'Only Admin Engineering can create VRs'
            ], 403);
        }

        if ($ticketId === 'ticket-select') {
            // Show ticket selection
            $html = view('vouchers.partials.ticket-selection')->render();

            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        }

        // Load specific ticket
        $ticket = Ticket::with(['user', 'category', 'priority', 'approval'])
            ->findOrFail($ticketId);

        // Validasi: ticket harus pending_vr
        if ($ticket->status !== 'pending_vr') {
            return response()->json([
                'success' => false,
                'message' => 'Ticket is not in pending VR status. Current status: ' . $ticket->status
            ], 422);
        }

        // Check if ticket needs VR
        if (!$ticket->approval || !$ticket->approval->needs_vr) {
            return response()->json([
                'success' => false,
                'message' => 'This ticket does not require a VR'
            ], 422);
        }

        // Check if VR already exists for this ticket
        $existingVR = VoucherRequest::where('ticket_id', $ticket->id)
            ->whereIn('status', ['pending', 'admin_approved', 'om_approved', 'gm_approved', 'paid'])
            ->first();

        if ($existingVR) {
            return response()->json([
                'success' => false,
                'message' => 'A VR already exists for this ticket: #' . $existingVR->vr_number
            ], 422);
        }

        // Generate VR number
        $today = date('Ymd');
        $count = VoucherRequest::whereDate('created_at', today())->count() + 1;
        $vrNumber = 'VR-' . $today . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $html = view('vouchers.partials.create-form', compact('ticket', 'vrNumber'))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Search tickets for VR creation
     */
    public function searchTickets(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'admin_eng') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $perPage = 10;

        $query = Ticket::with(['user', 'category', 'priority'])
            ->where('status', 'pending_vr')
            ->whereHas('approval', function ($q) {
                $q->where('needs_vr', true);
            })
            ->whereDoesntHave('voucherRequests', function ($q) {
                $q->whereIn('status', ['pending', 'admin_approved', 'om_approved', 'gm_approved', 'paid']);
            });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $results = $tickets->map(function ($ticket) {
            return [
                'id' => $ticket->ticket_number,
                'text' => '#' . $ticket->ticket_number . ' - ' . $ticket->title,
                'ticket_number' => $ticket->ticket_number,
                'title' => $ticket->title,
                'category' => $ticket->category->name,
                'priority' => $ticket->priority->name,
                'ticket_id' => $ticket->id
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => count($results) === $perPage
            ],
            'total_count' => $query->count()
        ]);
    }

    /**
     * Find ticket by number
     */
    public function findTicketByNumber($ticketNumber)
    {
        $user = Auth::user();

        if ($user->role !== 'admin_eng') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $ticket = Ticket::with(['user', 'category', 'priority', 'approval'])
            ->where('ticket_number', $ticketNumber)
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found'
            ], 404);
        }

        // Validasi: ticket harus pending_vr dan butuh VR
        if ($ticket->status !== 'pending_vr') {
            return response()->json([
                'success' => false,
                'message' => 'Ticket is not in pending VR status. Current status: ' . $ticket->status
            ], 422);
        }

        if (!$ticket->approval || !$ticket->approval->needs_vr) {
            return response()->json([
                'success' => false,
                'message' => 'This ticket does not require a VR'
            ], 422);
        }

        // Check if VR already exists
        $existingVR = VoucherRequest::where('ticket_id', $ticket->id)
            ->whereIn('status', ['pending', 'admin_approved', 'om_approved', 'gm_approved', 'paid'])
            ->first();

        if ($existingVR) {
            return response()->json([
                'success' => false,
                'message' => 'A VR already exists for this ticket: #' . $existingVR->vr_number
            ], 422);
        }

        return response()->json([
            'success' => true,
            'ticket_id' => $ticket->id,
            'ticket' => $ticket
        ]);
    }

    /**
     * Store new VR - HANYA admin_eng
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'admin_eng') {
            return response()->json([
                'success' => false,
                'message' => 'Only Admin Engineering can create VRs'
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

        $ticket = Ticket::with(['approval'])->findOrFail($request->ticket_id);

        DB::beginTransaction();
        try {
            // Create VR
            $vr = VoucherRequest::create([
                'vr_number' => $request->vr_number,
                'ticket_id' => $ticket->id,
                'total_amount' => 0,
                'status' => 'pending',
                'notes' => $request->notes,
                'created_by' => $user->id,
            ]);

            // Create items
            $totalAmount = 0;
            foreach ($request->items as $itemData) {
                $item = VoucherItem::create([
                    'voucher_request_id' => $vr->id,
                    'item_name' => $itemData['item_name'],
                    'qty' => $itemData['qty'],
                    'unit_price' => $itemData['unit_price'],
                    'vendor' => $itemData['vendor'] ?? null,
                    'description' => $itemData['description'] ?? null,
                ]);
                $totalAmount += $item->qty * $item->unit_price;
            }

            // Update total
            $vr->update(['total_amount' => $totalAmount]);

            // Update ticket approval record
            if ($ticket->approval) {
                $ticket->approval->update([
                    'vr_created_at' => now(),
                    'vr_created_by' => $user->id,
                ]);
            }

            // Update ticket status ke in_progress (karena VR sudah dibuat)
            $ticket->update(['status' => 'in_progress']);

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'action' => 'vr_created',
                'description' => 'Created VR #' . $vr->vr_number . ' with ' . count($request->items) . ' items. Total: Rp ' . number_format($totalAmount, 0, ',', '.'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Notify technician (jika ada yang assigned)
            if ($ticket->assigned_to) {
                $technician = User::find($ticket->assigned_to);
                if ($technician) {
                    $this->sendNotification(
                        $technician,
                        $ticket,
                        'VR Created - Work Can Continue',
                        'VR #' . $vr->vr_number . ' has been created for ticket #' . $ticket->ticket_number . '. You can now continue work.',
                        'vr_created'
                    );
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'VR created successfully',
                'vr_id' => $vr->id
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
     * Show VR details in modal
     */
    public function showModal($id)
    {
        $vr = VoucherRequest::with([
            'ticket.user',
            'ticket.category',
            'ticket.priority',
            'ticket.assignedUser',
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

        $html = view('vouchers.partials.view-details', compact('vr'))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Approve VR (Admin, OM, GM) dengan signature option
     */
    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        $vr = VoucherRequest::with(['ticket'])->findOrFail($id);

        // Check permission
        $canApprove = false;
        $stage = 0;

        if ($user->role === 'admin_eng' && $vr->status === 'pending') {
            $canApprove = true;
            $stage = 2; // Admin approval stage
        } elseif ($user->role === 'om' && $vr->status === 'admin_approved') {
            $canApprove = true;
            $stage = 3; // OM approval stage
        } elseif ($user->role === 'gm' && $vr->status === 'om_approved') {
            $canApprove = true;
            $stage = 4; // GM approval stage
        }

        if (!$canApprove) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot approve this VR. Current status: ' . $vr->status
            ], 403);
        }

        $request->validate([
            'signature_data' => 'nullable|string',
            'use_saved_signature' => 'nullable|boolean',
            'save_signature' => 'nullable|boolean',
            'current_password' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Handle signature
            $signaturePath = null;
            $hasSignature = !empty($user->signature_path) && Storage::disk('public')->exists($user->signature_path);

            // Jika user memilih pakai saved signature
            if ($request->has('use_saved_signature') && $request->boolean('use_saved_signature') && $hasSignature) {
                // Copy dari saved signature
                $signaturePath = 'signatures/vr_' . $vr->vr_number . '_stage' . $stage . '_user' . $user->id . '_' . time() . '.png';
                Storage::disk('public')->copy($user->signature_path, $signaturePath);

            } elseif ($request->has('signature_data')) {
                // Buat signature baru
                $signaturePath = $this->saveSignature(
                    $request->signature_data,
                    $vr->vr_number,
                    $user->id,
                    $stage
                );

                // Save ke profile jika diminta
                if ($request->has('save_signature') && $request->boolean('save_signature')) {
                    // Verifikasi password jika ingin replace signature lama
                    if ($hasSignature && $request->has('current_password')) {
                        if (!Hash::check($request->current_password, $user->password)) {
                            throw new \Exception('Current password is incorrect');
                        }
                    }

                    // Hapus signature lama jika ada
                    if ($hasSignature && $user->signature_path) {
                        Storage::disk('public')->delete($user->signature_path);
                    }

                    $user->update([
                        'signature_path' => $signaturePath,
                        'has_signature' => true,
                        'signature_updated_at' => now()
                    ]);
                }
            }

            // Save signature record
            if ($signaturePath) {
                Signature::create([
                    'ticket_id' => $vr->ticket_id,
                    'user_id' => $user->id,
                    'signature_type' => 'approver',
                    'stage' => $stage,
                    'signature_path' => $signaturePath,
                    'signed_at' => now(),
                    'ip_address' => $request->ip(),
                ]);
            }

            // Update VR berdasarkan role
            $oldStatus = $vr->status;
            $newStatus = '';

            switch ($user->role) {
                case 'admin_eng':
                    $vr->update([
                        'admin_approved' => true,
                        'admin_approved_by' => $user->id,
                        'admin_approved_at' => now(),
                        'status' => 'admin_approved',
                    ]);
                    $newStatus = 'admin_approved';

                    // Notify OM
                    $omUsers = User::where('role', 'om')->where('status', 'active')->get();
                    foreach ($omUsers as $omUser) {
                        $this->sendNotification(
                            $omUser,
                            $vr->ticket,
                            'VR Needs OM Approval',
                            'VR #' . $vr->vr_number . ' needs your approval for ticket #' . $vr->ticket->ticket_number,
                            'vr_approval'
                        );
                    }
                    break;

                case 'om':
                    $vr->update([
                        'om_approved' => true,
                        'om_approved_by' => $user->id,
                        'om_approved_at' => now(),
                        'status' => 'om_approved',
                    ]);
                    $newStatus = 'om_approved';

                    // Notify GM
                    $gmUsers = User::where('role', 'gm')->where('status', 'active')->get();
                    foreach ($gmUsers as $gmUser) {
                        $this->sendNotification(
                            $gmUser,
                            $vr->ticket,
                            'VR Needs GM Approval',
                            'VR #' . $vr->vr_number . ' needs your final approval for ticket #' . $vr->ticket->ticket_number,
                            'vr_approval'
                        );
                    }
                    break;

                case 'gm':
                    $vr->update([
                        'gm_approved' => true,
                        'gm_approved_by' => $user->id,
                        'gm_approved_at' => now(),
                        'status' => 'gm_approved',
                    ]);
                    $newStatus = 'gm_approved';

                    // Notify creator dan technician
                    $this->sendNotification(
                        $vr->creator,
                        $vr->ticket,
                        'VR Fully Approved',
                        'VR #' . $vr->vr_number . ' has been fully approved by GM for ticket #' . $vr->ticket->ticket_number,
                        'vr_approved'
                    );

                    if ($vr->ticket->assigned_to) {
                        $technician = User::find($vr->ticket->assigned_to);
                        if ($technician) {
                            $this->sendNotification(
                                $technician,
                                $vr->ticket,
                                'VR Fully Approved',
                                'VR #' . $vr->vr_number . ' has been fully approved by GM for ticket #' . $vr->ticket->ticket_number,
                                'vr_approved'
                            );
                        }
                    }
                    break;
            }

            // Add notes
            if ($request->notes) {
                $vr->update([
                    'notes' => $vr->notes . "\n\n[" . strtoupper($user->role) . " Approved: " . now()->format('Y-m-d H:i') . " - " . $user->name . "]" .
                        "\nNotes: " . $request->notes
                ]);
            }

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $vr->ticket_id,
                'action' => 'vr_approved',
                'description' => ucfirst($user->role) . ' ' . $user->name . ' approved VR #' . $vr->vr_number . ' (' . $oldStatus . ' → ' . $newStatus . ')',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'VR approved successfully',
                'vr_id' => $vr->id
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
     * Reject VR (Admin, OM, GM)
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10',
            'notes' => 'nullable|string'
        ]);

        $vr = VoucherRequest::with(['ticket'])->findOrFail($id);
        $user = Auth::user();

        // Check permission
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
                'message' => 'You cannot reject this VR. Current status: ' . $vr->status
            ], 403);
        }

        DB::beginTransaction();
        try {
            $oldStatus = $vr->status;

            $vr->update([
                'status' => 'rejected',
                'notes' => $vr->notes . "\n\n[Rejected by " . strtoupper($user->role) . " " . $user->name . ": " . now()->format('Y-m-d H:i') . "]" .
                    "\nReason: " . $request->rejection_reason .
                    ($request->notes ? "\nAdditional Notes: " . $request->notes : "")
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $vr->ticket_id,
                'action' => 'vr_rejected',
                'description' => ucfirst($user->role) . ' ' . $user->name . ' rejected VR #' . $vr->vr_number . '. Reason: ' . $request->rejection_reason,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Notify creator
            $this->sendNotification(
                $vr->creator,
                $vr->ticket,
                'VR Rejected',
                'VR #' . $vr->vr_number . ' for ticket #' . $vr->ticket->ticket_number . ' was rejected by ' . $user->name . '. Reason: ' . $request->rejection_reason,
                'vr_rejected'
            );

            // Update ticket status jika perlu
            if ($vr->ticket->status === 'in_progress') {
                $vr->ticket->update(['status' => 'pending_vr']);

                // Notify technician
                if ($vr->ticket->assigned_to) {
                    $technician = User::find($vr->ticket->assigned_to);
                    if ($technician) {
                        $this->sendNotification(
                            $technician,
                            $vr->ticket,
                            'VR Rejected - Need Revision',
                            'VR #' . $vr->vr_number . ' was rejected. Please contact Admin Engineering for revision.',
                            'vr_rejected'
                        );
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'VR rejected successfully',
                'vr_id' => $vr->id
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
    public function markPaid(Request $request, $id)
    {
        $vr = VoucherRequest::with(['ticket'])->findOrFail($id);
        $user = Auth::user();

        if (!in_array($user->role, ['admin_eng', 'superadmin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only Admin Engineering can mark VR as paid'
            ], 403);
        }

        // Hanya bisa mark as paid jika status gm_approved atau pending payment
        $allowedStatuses = ['gm_approved', 'pending_payment'];
        if (!in_array($vr->status, $allowedStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'VR must be fully approved by GM before marking as paid. Current status: ' . $vr->status
            ], 422);
        }

        DB::beginTransaction();
        try {
            $oldStatus = $vr->status;
            $vr->update([
                'status' => 'paid',
                'notes' => $vr->notes . "\n\n[Marked as paid: " . now()->format('Y-m-d H:i') . " - " . $user->name . "]" .
                    ($request->notes ? "\nPayment Notes: " . $request->notes : "")
            ]);

            // ✅ FIX: Update ticket status ke in_progress (stage 4)
            $ticket = $vr->ticket;
            $ticket->update([
                'status' => 'in_progress',
                'current_stage' => 4, // Kembali ke stage 4: In Progress
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'action' => 'vr_paid',
                'description' => 'Marked VR #' . $vr->vr_number . ' as paid (' . $oldStatus . ' → paid). Ticket returned to in_progress stage.',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Add comment to ticket
            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'comment' => "VR #" . $vr->vr_number . " has been marked as paid. Ticket returned to in progress status. Technician can continue work.",
                'is_internal' => 0,
            ]);

            // ✅ FIX: Notify creator
            $this->sendNotification(
                $vr->creator,
                $ticket,
                'VR Marked as Paid',
                'VR #' . $vr->vr_number . ' for ticket #' . $ticket->ticket_number . ' has been marked as paid.',
                'vr_paid'
            );

            // ✅ FIX: Notify technician untuk lanjut kerja
            if ($ticket->assigned_to) {
                $technician = User::find($ticket->assigned_to);
                if ($technician) {
                    $this->sendNotification(
                        $technician,
                        $ticket,
                        'VR Paid - Continue Work',
                        'VR #' . $vr->vr_number . ' has been paid. Please continue work on ticket #' . $ticket->ticket_number,
                        'assignment'
                    );
                }
            }

            // ✅ FIX: Also notify reporter
            $this->sendNotification(
                $ticket->user,
                $ticket,
                'VR Paid - Work Resuming',
                'VR #' . $vr->vr_number . ' for your ticket #' . $ticket->ticket_number . ' has been paid. Technician will continue work.',
                'info'
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'VR marked as paid successfully. Ticket returned to in progress stage.',
                'vr_id' => $vr->id,
                'ticket_status' => 'in_progress'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Mark VR paid error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark VR as paid: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete VR - HANYA superadmin dan creator (jika status pending/rejected)
     */
    public function destroy(Request $request, $id)
    {
        $vr = VoucherRequest::with(['ticket'])->findOrFail($id);
        $user = Auth::user();

        // Superadmin bisa delete semua
        // Creator bisa delete hanya jika status pending atau rejected
        if ($user->role !== 'superadmin') {
            if ($vr->created_by !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only delete VRs that you created'
                ], 403);
            }

            if (!in_array($vr->status, ['pending', 'rejected'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only delete VRs in pending or rejected status'
                ], 403);
            }
        }

        // Verifikasi password untuk superadmin
        if ($user->role === 'superadmin' && $request->has('password')) {
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password verification failed'
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            // Delete items
            $vr->items()->delete();

            // Update ticket status jika perlu
            if ($vr->ticket->status === 'in_progress' && $vr->ticket->approval && $vr->ticket->approval->needs_vr) {
                $vr->ticket->update(['status' => 'pending_vr']);
            }

            // Log activity sebelum delete
            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $vr->ticket_id,
                'action' => 'vr_deleted',
                'description' => ucfirst($user->role) . ' ' . $user->name . ' deleted VR #' . $vr->vr_number,
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
     * Print VR
     */
    public function print($id)
    {
        $vr = VoucherRequest::with([
            'ticket.user',
            'ticket.category',
            'ticket.priority',
            'ticket.assignedUser',
            'creator',
            'adminApprover',
            'omApprover',
            'gmApprover',
            'items'
        ])->findOrFail($id);

        $user = Auth::user();

        if (!$this->canViewVR($user, $vr)) {
            abort(403, 'Unauthorized access to this VR');
        }

        return view('vouchers.print', compact('vr'));
    }

    /**
     * Verify password for new signature
     */
    public function verifyPassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string'
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

        return response()->json([
            'success' => true,
            'message' => 'Password verified successfully'
        ]);
    }

    /**
     * Helper: Check if user can view VR
     */
    private function canViewVR($user, $vr)
    {
        // Superadmin can view all
        if ($user->role === 'superadmin') {
            return true;
        }

        // Creator can always view
        if ($vr->created_by === $user->id) {
            return true;
        }

        // Admin Eng can view if they created or if pending/admin_approved
        if ($user->role === 'admin_eng') {
            return in_array($vr->status, ['pending', 'admin_approved']) || $vr->created_by === $user->id;
        }

        // OM can view if admin_approved atau om_approved
        if ($user->role === 'om') {
            return in_array($vr->status, ['admin_approved', 'om_approved']);
        }

        // GM can view if om_approved atau gm_approved
        if ($user->role === 'gm') {
            return in_array($vr->status, ['om_approved', 'gm_approved', 'paid']);
        }

        return false;
    }

    /**
     * Helper: Save signature from data URL
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

        $fileName = 'signature_vr_' . $vrNumber . '_stage' . $stage . '_user' . $userId . '_' . time() . '.png';
        $filePath = 'signatures/' . $fileName;

        Storage::disk('public')->makeDirectory('signatures', 0755, true);
        $saved = Storage::disk('public')->put($filePath, $imageData);

        if (!$saved) {
            throw new \Exception('Failed to save signature file');
        }

        return $filePath;
    }

    /**
     * Helper: Send notification
     */
    private function sendNotification($user, $ticket, $title, $message, $type = 'info', $vr = null)
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
                    Mail::to($user->email)->queue(new VRNotification(
                        $user,
                        $ticket,
                        $title,
                        $message,
                        $type,
                        $vr  // ✅ Kirim data VR juga
                    ));
                } catch (\Exception $e) {
                    \Log::warning('Email notification failed: ' . $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            \Log::error('Notification creation failed: ' . $e->getMessage());
        }
    }
}
