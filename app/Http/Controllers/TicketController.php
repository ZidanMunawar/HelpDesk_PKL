<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Category;
use App\Models\Location;
use App\Models\Priority;
use App\Models\Signature;
use App\Models\Department;
use App\Models\ActivityLog;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\TicketAttachment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Mail\TicketNotification;
use Barryvdh\DomPDF\Facade\Pdf;

class TicketController extends Controller
{
    /**
     * Display all tickets dengan filter berdasarkan role
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // ==================== BASE QUERY DENGAN WITH RELATION ====================
        $query = Ticket::with(['user', 'category', 'priority', 'location', 'assignedUser', 'department']);

        // ==================== FILTER BERDASARKAN ROLE ====================

        // ==================== FILTER KHUSUS DARI SIDEBAR ====================

        // Filter My Tickets (untuk user, manager, admin_eng)
        if ($request->filled('my_tickets') && $request->my_tickets == '1') {
            if (in_array($user->role, ['user', 'manager', 'admin_eng'])) {
                $query->where('user_id', $user->id);
            }
        }

        // Filter Assigned to Me (untuk technician)
        if ($request->filled('assigned') && $request->assigned == '1') {
            if ($user->role === 'technician') {
                $query->where('assigned_to', $user->id);
            }
        }

        // Filter Department Tickets
        if ($request->filled('department_filter') && $request->department_filter == '1') {
            if ($user->department_id) {
                $query->where('department_id', $user->department_id);
            }
        }

        // Filter Pending Check (untuk user)
        if ($request->filled('status') && $request->status == 'completed' && $request->filled('stage') && $request->stage == '6') {
            if ($user->role === 'user' && $user->department_id) {
                $query->where('department_id', $user->department_id)
                    ->where('status', 'completed')
                    ->where('current_stage', 6);
            }
        }

        // Filter untuk Admin Engineering
        if ($user->role === 'admin_eng') {
            if ($request->filled('status') && $request->status == 'open') {
                $query->where('status', 'open')
                    ->where('current_stage', 1);
            }



            if ($request->filled('unassigned') && $request->unassigned == '1') {
                $query->where('status', 'in_progress')
                    ->whereNull('assigned_to');
            }

            if ($request->filled('status') && $request->status == 'pending_vr') {
                $query->where('status', 'pending_vr')
                    ->where('current_stage', 5);
            }

            if ($request->filled('status') && $request->status == 'ready_for_closure') {
                $query->where('status', 'ready_for_closure')
                    ->where('current_stage', 8);
            }
        }

        // Filter untuk OM & GM
        if (in_array($user->role, ['om', 'gm'])) {
            if ($request->filled('status')) {
                if ($user->role === 'om' && $request->status == 'pending_om') {
                    $query->where('status', 'pending_om')
                        ->where('current_stage', 3);
                }
                if ($user->role === 'gm' && $request->status == 'pending_gm') {
                    $query->where('status', 'pending_gm');
                }
            }
        }

        // ==================== FILTER STATUS ====================
        if ($request->filled('status') && $request->status !== '') {
            $skipStatusFilter = false;

            if ($user->role === 'admin_eng' && in_array($request->status, ['open', 'pending_om', 'pending_vr', 'ready_for_closure'])) {
                $skipStatusFilter = true;
            }
            if (in_array($user->role, ['om', 'gm']) && in_array($request->status, ['pending_om', 'pending_gm'])) {
                $skipStatusFilter = true;
            }
            if ($user->role === 'user' && $request->status == 'completed' && $request->filled('stage')) {
                $skipStatusFilter = true;
            }

            if (!$skipStatusFilter) {
                $query->where('status', $request->status);
            }
        }

        // ==================== FILTER SEARCH ====================
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('location_manual', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('location', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // ==================== FILTER CATEGORY (MULTIPLE) ====================
        if ($request->filled('category')) {
            $categories = $request->category;
            if (is_array($categories) && !empty($categories)) {
                $query->whereIn('category_id', $categories);
            } elseif (!empty($categories)) {
                $query->where('category_id', $categories);
            }
        }

        // ==================== FILTER PRIORITY (MULTIPLE) ====================
        if ($request->filled('priority')) {
            $priorities = $request->priority;
            if (is_array($priorities) && !empty($priorities)) {
                $query->whereIn('priority_id', $priorities);
            } elseif (!empty($priorities)) {
                $query->where('priority_id', $priorities);
            }
        }

        // ==================== FILTER DEPARTMENT (MULTIPLE) ====================
        if ($request->filled('department')) {
            $departments = $request->department;
            if (is_array($departments) && !empty($departments)) {
                $query->whereIn('department_id', $departments);
            } elseif (!empty($departments)) {
                $query->where('department_id', $departments);
            }
        }

        // ==================== FILTER DATE RANGE ====================
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // ==================== ORDER BY LATEST ====================
        $tickets = $query->latest()->paginate(10)->withQueryString();

        // ==================== GET FILTER OPTIONS (HANYA ACTIVE) ====================
        $categories = Category::where('status', 'active')->orderBy('name')->get();
        $priorities = Priority::where('status', 'active')->orderBy('level')->get();
        $departments = Department::where('status', 'active')->orderBy('name')->get();

        // Status options dengan display names yang sama seperti di show blade
        $statusOptions = [
            '' => 'All Status',
            'open' => 'Open',
            'received' => 'Received',
            'pending_om' => 'OM Approval',
            'in_progress' => 'In Progress',
            'pending_vr' => 'VR Approval',
            'completed' => 'Completed',
            'pending_gm' => 'GM Approval',
            'ready_for_closure' => 'Ready for Closure',
            'closed' => 'Closed',
            'cancelled' => 'Cancelled',
        ];

        // ==================== STATUS COUNTS ====================
        $statusCounts = [
            'all' => Ticket::count(),
            'open' => Ticket::where('status', 'open')->count(),
            'received' => Ticket::where('status', 'received')->count(),
            'pending_om' => Ticket::where('status', 'pending_om')->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'pending_vr' => Ticket::where('status', 'pending_vr')->count(),
            'completed' => Ticket::where('status', 'completed')->count(),
            'pending_gm' => Ticket::where('status', 'pending_gm')->count(),
            'ready_for_closure' => Ticket::where('status', 'ready_for_closure')->count(),
            'closed' => Ticket::where('status', 'closed')->count(),
            'cancelled' => Ticket::where('status', 'cancelled')->count(),
        ];

        // Counts khusus untuk sidebar
        $sidebarCounts = [
            'my_tickets' => Ticket::where('user_id', $user->id)->count(),
            'assigned_to_me' => Ticket::where('assigned_to', $user->id)
                ->whereIn('status', ['in_progress', 'pending_vr'])
                ->count(),
            'department_tickets' => $user->department_id
                ? Ticket::where('department_id', $user->department_id)
                    ->whereNotIn('status', ['closed', 'cancelled'])
                    ->count()
                : 0,
            'pending_check' => $user->department_id && $user->role === 'user'
                ? Ticket::where('department_id', $user->department_id)
                    ->where('status', 'completed')
                    ->where('current_stage', 6)
                    ->count()
                : 0,
            'pending_receive' => Ticket::where('status', 'open')
                ->where('current_stage', 1)
                ->count(),
            'unassigned' => Ticket::where('status', 'pending_om')
                ->where('current_stage', 3)
                ->whereNull('assigned_to')
                ->count(),
            'pending_vr_count' => Ticket::where('status', 'pending_vr')
                ->where('current_stage', 5)
                ->count(),
            'ready_close' => Ticket::where('status', 'ready_for_closure')
                ->where('current_stage', 8)
                ->count(),
            'pending_om_approval' => Ticket::where('status', 'pending_om')
                ->where('current_stage', 3)
                ->count(),
            'pending_gm_approval' => Ticket::where('status', 'pending_gm')
                ->where('current_stage', 8)
                ->count(),
        ];

        return view('tickets.index', compact(
            'tickets',
            'categories',
            'priorities',
            'departments',
            'statusOptions',
            'statusCounts',
            'sidebarCounts',
            'user'
        ));
    }

    /**
     * Show the form for creating a new ticket
     */
    public function create()
    {
        $user = Auth::user();

        // ==================== TIGA ROLE YANG BISA CREATE: admin_eng, user, manager ====================
        if (!in_array($user->role, ['admin_eng', 'user', 'manager'])) {
            return redirect()->route('tickets.index')
                ->with('error', 'You are not authorized to create tickets.');
        }

        // HANYA ambil data yang statusnya 'active'
        $categories = Category::where('status', 'active')->orderBy('name')->get();
        $priorities = Priority::where('status', 'active')->orderBy('level', 'desc')->get();
        $locations = Location::where('status', 'active')->orderBy('name')->get();

        // Cek apakah user bisa menggunakan saved signature (manager atau admin_eng)
        $canUseSavedSignature = in_array($user->role, ['manager', 'admin_eng']);

        // Ambil data signature user jika ada
        $hasSavedSignature = $user->has_signature && $user->signature_path;
        $signatureUrl = $hasSavedSignature ? Storage::url($user->signature_path) : null;

        Log::info('User creating ticket:', [
            'user_id' => $user->id,
            'role' => $user->role,
            'department_id' => $user->department_id,
            'department_name' => $user->department->name ?? 'No department',
            'categories_count' => $categories->count(),
            'priorities_count' => $priorities->count(),
            'locations_count' => $locations->count(),
            'can_use_saved_signature' => $canUseSavedSignature,
            'has_saved_signature' => $hasSavedSignature
        ]);

        return view('tickets.create', compact(
            'categories',
            'priorities',
            'locations',
            'canUseSavedSignature',
            'hasSavedSignature',
            'signatureUrl'
        ));
    }

    /**
     * Store new ticket with signature workflow
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // ==================== SAMA DENGAN CREATE (admin_eng, user, manager) ====================
        if (!in_array($user->role, ['admin_eng', 'user', 'manager'])) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to create tickets.'
            ], 403);
        }

        // Debug log request
        Log::info('Ticket store request:', [
            'user_id' => $user->id,
            'role' => $user->role,
            'title' => $request->title,
            'category_id' => $request->category_id,
            'has_signature' => !empty($request->signature_data),
            'use_saved_signature' => $request->use_saved_signature,
            'attachments_count' => $request->hasFile('attachments') ? count($request->file('attachments')) : 0
        ]);

        // Validate request (tanpa validasi use_saved_signature)
        $validator = validator($request->all(), [
            'title' => 'required|string|max:100',
            'description' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (trim($value) === '') {
                        $fail('Description cannot be empty or only whitespace.');
                        return;
                    }
                    if (preg_match('/^[\n\r]+$/', $value)) {
                        $fail('Description cannot contain only line breaks.');
                        return;
                    }
                },
            ],
            'category_id' => 'required|exists:categories,id',
            'priority_id' => 'required|exists:priorities,id',
            'department_id' => 'nullable|exists:departments,id',
            'location_id' => 'nullable|required_without:location_manual|exists:locations,id',
            'location_manual' => 'nullable|required_without:location_id|string|max:255',
            'due_date' => 'nullable|date|after:now',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            'signature_data' => 'nullable|string',
            // 'use_saved_signature' => 'nullable|in:1', // HAPUS validasi ini
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // ==================== CUSTOM VALIDASI SIGNATURE ====================
        $useSavedSignature = $request->input('use_saved_signature') == '1';
        $hasSignatureData = $request->filled('signature_data') && $request->signature_data;

        // Cek apakah user boleh menggunakan saved signature (manager atau admin_eng)
        $canUseSavedSignature = in_array($user->role, ['manager', 'admin_eng']);

        // Validasi: salah satu harus dipilih
        if (!$useSavedSignature && !$hasSignatureData) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a signature or use saved signature.'
            ], 422);
        }

        // Jika pilih saved signature tapi tidak punya hak akses
        if ($useSavedSignature && !$canUseSavedSignature) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to use saved signature.'
            ], 403);
        }

        // Jika pilih saved signature tapi tidak punya signature tersimpan
        if ($useSavedSignature && (!$user->has_signature || !$user->signature_path)) {
            return response()->json([
                'success' => false,
                'message' => 'You have not uploaded a signature in your profile. Please upload one first.'
            ], 422);
        }

        // Validasi: location_id atau location_manual harus ada salah satu
        if (!$request->location_id && !$request->location_manual) {
            return response()->json([
                'success' => false,
                'message' => 'Either location or manual location is required.'
            ], 422);
        }

        // Validasi: pastikan data yang dipilih masih active
        $category = Category::where('id', $request->category_id)->where('status', 'active')->first();
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Selected category is not available or inactive.'
            ], 422);
        }

        $priority = Priority::where('id', $request->priority_id)->where('status', 'active')->first();
        if (!$priority) {
            return response()->json([
                'success' => false,
                'message' => 'Selected priority is not available or inactive.'
            ], 422);
        }

        if ($request->location_id) {
            $location = Location::where('id', $request->location_id)->where('status', 'active')->first();
            if (!$location) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected location is not available or inactive.'
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            // Generate ticket number
            $ticketNumber = $this->generateTicketNumber();

            // Save signature
            $signaturePath = null;

            // Jika menggunakan saved signature (untuk manager atau admin_eng)
            if ($useSavedSignature) {
                $signaturePath = $user->signature_path;
                Log::info('Using saved signature:', [
                    'user_id' => $user->id,
                    'role' => $user->role,
                    'path' => $signaturePath
                ]);
            }
            // Jika pakai signature baru dari modal
            elseif ($hasSignatureData && str_starts_with($request->signature_data, 'data:image/')) {
                $signaturePath = $this->saveSignature($request->signature_data, $ticketNumber, $user->id);
                Log::info('New signature saved successfully:', [
                    'user_id' => $user->id,
                    'path' => $signaturePath
                ]);
            } else {
                throw new \Exception('No valid signature provided');
            }

            // Create ticket
// Create ticket
            $ticketData = [
                'ticket_number' => $ticketNumber,
                'title' => $request->title,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'priority_id' => $request->priority_id,
                'department_id' => $request->department_id ?? $user->department_id,
                'location_id' => $request->location_id,
                'location_manual' => $request->location_type === 'manual'
                    ? $request->location_manual . ' (' . ucfirst($request->manual_location_hotel) . ' Hotel)'
                    : $request->location_manual,
                'user_id' => $user->id,
                'status' => 'open',
                'current_stage' => 1,
                'approval_status' => 'pending_approval',
                'due_date' => $request->due_date,
            ];

            $ticket = Ticket::create($ticketData);

            Log::info('Ticket created:', [
                'id' => $ticket->id,
                'ticket_number' => $ticketNumber
            ]);

            // Save reporter signature to signatures table
            Signature::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'signature_type' => 'reporter',
                'stage' => 1,
                'signature_path' => $signaturePath,
                'signed_at' => now(),
                'ip_address' => $request->ip(),
            ]);

            Log::info('Reporter signature saved:', [
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'stage' => 1,
                'used_saved_signature' => $useSavedSignature
            ]);

            // Handle file attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('ticket_attachments', $fileName, 'public');

                    TicketAttachment::create([
                        'ticket_id' => $ticket->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                        'uploaded_by' => $user->id,
                    ]);
                }

                Log::info('Attachments saved:', [
                    'count' => count($request->file('attachments')),
                    'ticket_id' => $ticket->id
                ]);
            }

            // Notifikasi ke user pembuat ticket
            $this->sendTicketCreatedNotification($ticket->user, $ticket);

            // Notifikasi ke Engineering Department (dengan email)
            $this->notifyEngineeringDepartment($ticket);

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'action' => 'created',
                'description' => 'Ticket created by ' . $user->name,
                'old_values' => null,
                'new_values' => json_encode($ticketData),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            Log::info('Ticket successfully created and committed', [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticketNumber
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ticket created successfully!',
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticketNumber,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Ticket creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified ticket
     */
    public function show($id)
    {
        $ticket = Ticket::with([
            'user',
            'category',
            'priority',
            'location',
            'assignedUser',
            'department',
            'attachments',
            'comments.user',
            'approvals',
            'signatures',
            'voucherRequests'
        ])->findOrFail($id);

        $user = Auth::user();

        // Check access
        $canView = false;

        switch ($user->role) {
            case 'superadmin':
            case 'admin_eng':
            case 'om':
            case 'gm':
                $canView = true;
                break;
            case 'user':
                $canView = ($ticket->user_id === $user->id);
                break;
            case 'technician':
                $canView = ($ticket->assigned_to === $user->id);
                break;
            case 'manager':
                $canView = ($ticket->department_id === $user->department_id);
                break;
        }

        if (!$canView) {
            return redirect()->route('tickets.index')
                ->with('error', 'You do not have permission to view this ticket.');
        }

        return view('tickets.show', compact('ticket'));
    }

    /**
     * Send notification to user that ticket was created
     */
    private function sendTicketCreatedNotification($user, $ticket)
    {
        try {
            // In-App Notification
            Notification::create([
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'title' => 'Your Maintenance Request Has Been Created',
                'message' => 'Your request #' . $ticket->ticket_number . ' has been successfully submitted. We will process it shortly.',
                'type' => 'success',
                'is_read' => false,
            ]);

            // Email Notification
            if (config('mail.mailers.smtp.host')) {
                try {
                    Mail::to($user->email)->queue(new TicketNotification(
                        $user,
                        $ticket,
                        'Your Maintenance Request Has Been Created',
                        'Your request #' . $ticket->ticket_number . ' has been successfully submitted. We will process it shortly.',
                        'success'
                    ));
                    Log::info('Email notification sent to user:', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'ticket_id' => $ticket->id
                    ]);
                } catch (\Exception $e) {
                    Log::warning('Email notification failed for user ' . $user->id . ': ' . $e->getMessage());
                }
            } else {
                Log::info('Email not configured, skipping email notification for user ' . $user->id);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send ticket created notification: ' . $e->getMessage());
        }
    }

    /**
     * Notify Engineering Department (Admin/Technician) with both in-app and email
     */
    private function notifyEngineeringDepartment(Ticket $ticket)
    {
        try {
            // Get users in Engineering department (ID 3) atau role tertentu
            $engineeringUsers = User::where(function ($query) {
                $query->where('department_id', 3)
                    ->orWhereIn('role', ['admin_eng', 'technician', 'superadmin']);
            })
                ->where('status', 'active')
                ->get();

            Log::info('Notifying engineering users:', [
                'count' => $engineeringUsers->count(),
                'users' => $engineeringUsers->pluck('name')->toArray(),
                'ticket_id' => $ticket->id
            ]);

            foreach ($engineeringUsers as $user) {
                // In-App Notification
                Notification::create([
                    'user_id' => $user->id,
                    'ticket_id' => $ticket->id,
                    'title' => 'New Ticket Created',
                    'message' => 'Ticket #' . $ticket->ticket_number . ' has been created by ' .
                        $ticket->user->name . ' (Department: ' .
                        ($ticket->department->name ?? 'N/A') . ')',
                    'type' => 'info',
                    'is_read' => false,
                ]);

                // Email Notification
                if (config('mail.mailers.smtp.host')) {
                    try {
                        Mail::to($user->email)->queue(new TicketNotification(
                            $user,
                            $ticket,
                            'New Maintenance Request Created',
                            'A new maintenance request #' . $ticket->ticket_number . ' has been created by ' .
                            $ticket->user->name . '. Priority: ' . ($ticket->priority->name ?? 'N/A'),
                            'info'
                        ));
                        Log::info('Email notification sent to engineering user:', [
                            'user_id' => $user->id,
                            'email' => $user->email,
                            'ticket_id' => $ticket->id
                        ]);
                    } catch (\Exception $e) {
                        Log::warning('Email notification failed for engineering user ' . $user->id . ': ' . $e->getMessage());
                    }
                }
            }

            Log::info('Engineering department notifications created:', [
                'ticket_id' => $ticket->id,
                'recipients_count' => $engineeringUsers->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error notifying engineering department:', [
                'error' => $e->getMessage(),
                'ticket_id' => $ticket->id
            ]);
        }
    }

    /**
     * Check if user can view ticket details (untuk modal info)
     */
    public function checkAccess($id)
    {
        $ticket = Ticket::with(['user', 'category', 'department', 'priority'])->findOrFail($id);
        $user = Auth::user();

        $canViewFull = false;
        $reason = '';

        // Helper function untuk status display (sama kayak calendar)
        $getStatusDisplayName = function ($status) {
            $displayNames = [
                'open' => 'Open',
                'received' => 'Received',
                'pending_om' => 'OM Approval',
                'in_progress' => 'In Progress',
                'pending_vr' => 'PR Approval',
                'completed' => 'Completed',
                'pending_gm' => 'GM Approval',
                'ready_for_closure' => 'Ready for Closure',
                'closed' => 'Closed',
                'cancelled' => 'Cancelled'
            ];
            return $displayNames[$status] ?? $status;
        };

        switch ($user->role) {
            case 'superadmin':
            case 'admin_eng':
            case 'om':
            case 'gm':
                $canViewFull = true;
                $reason = 'Full access granted for ' . $user->role;
                break;

            case 'user':
                if ($ticket->user_id === $user->id) {
                    $canViewFull = true;
                    $reason = 'Ticket owner';
                } else {
                    $canViewFull = false;
                    $reason = 'This maintenance request belongs to another user';
                }
                break;

            case 'technician':
                if ($ticket->assigned_to === $user->id) {
                    $canViewFull = true;
                    $reason = 'Assigned technician';
                } else {
                    $canViewFull = false;
                    $reason = 'Maintenance request not assigned to you';
                }
                break;

            case 'manager':
                if ($ticket->department_id === $user->department_id) {
                    $canViewFull = true;
                    $reason = 'Department manager';
                } else {
                    $canViewFull = false;
                    $reason = 'Maintenance request from different department';
                }
                break;

            default:
                $canViewFull = false;
                $reason = 'No access permissions';
                break;
        }

        if (!$canViewFull) {
            return response()->json([
                'type' => 'modal_info',
                'ticket_info' => [
                    'number' => $ticket->ticket_number,
                    'title' => $ticket->title,
                    'status' => $ticket->status,
                    'status_display' => $getStatusDisplayName($ticket->status),
                    'created_by' => $ticket->user->name,
                    'created_at' => $ticket->created_at->format('d M Y, H:i'),
                    'category' => $ticket->category->name,
                    'department' => $ticket->department->name ?? 'N/A',
                    'priority' => $ticket->priority->name ?? 'N/A',
                    'priority_color' => $ticket->priority->color ?? '#003366',
                    'reason' => $reason
                ]
            ]);
        }

        return response()->json([
            'type' => 'redirect',
            'url' => route('tickets.show', $ticket->id)
        ]);
    }

    /**
     * Generate unique ticket number
     */
    private function generateTicketNumber()
    {
        $year = date('Y');
        $month = date('m');
        $day = date('d');

        $lastTicket = Ticket::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastTicket) {
            preg_match('/MR-(\d{8})-(\d{2})/', $lastTicket->ticket_number, $matches);
            if (isset($matches[2])) {
                $sequence = (int) $matches[2] + 1;
            }
        }

        return 'MR-' . $year . $month . $day . '-' . str_pad($sequence, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Save signature from data URL - 300x200
     */
    private function saveSignature($signatureData, $ticketNumber, $userId)
    {
        try {
            if (!preg_match('#^data:image/\w+;base64,#i', $signatureData)) {
                throw new \Exception('Invalid signature data URL format');
            }

            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signatureData));

            if ($imageData === false) {
                throw new \Exception('Failed to decode base64 signature data');
            }

            $fileName = 'signature_' . $ticketNumber . '_user' . $userId . '_' . time() . '.png';
            $filePath = 'signatures/' . $fileName;

            Storage::disk('public')->makeDirectory('signatures');
            $saved = Storage::disk('public')->put($filePath, $imageData);

            if (!$saved) {
                throw new \Exception('Failed to save signature file');
            }

            Log::info('Signature saved successfully:', [
                'path' => $filePath,
                'size' => strlen($imageData)
            ]);

            return $filePath;

        } catch (\Exception $e) {
            Log::error('Error saving signature:', [
                'error' => $e->getMessage(),
                'ticket_number' => $ticketNumber,
                'user_id' => $userId
            ]);
            throw $e;
        }
    }

    /**
     * Get user signature for saved signature (AJAX)
     * Untuk manager dan admin_eng
     */
    public function getSavedSignature(Request $request)
    {
        $user = Auth::user();

        // Hanya manager dan admin_eng yang bisa menggunakan saved signature
        if (!in_array($user->role, ['manager', 'admin_eng'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($user->has_signature && $user->signature_path) {
            $signatureUrl = Storage::url($user->signature_path);

            return response()->json([
                'success' => true,
                'has_signature' => true,
                'signature_url' => $signatureUrl,
                'signature_name' => $user->name,
                'signature_role' => $user->role === 'manager' ? 'Manager' : 'Admin Engineering',
                'signature_date' => $user->signature_updated_at ?
                    \Carbon\Carbon::parse($user->signature_updated_at)->format('d M Y H:i') : null
            ]);
        }

        return response()->json([
            'success' => true,
            'has_signature' => false,
            'message' => 'You have not uploaded a signature yet. Please upload in Profile > Signature.'
        ]);
    }

    /**
     * Get tickets for dashboard
     */
    public function dashboardStats()
    {
        $user = Auth::user();

        $query = Ticket::query();

        if ($user->role === 'user') {
            $query->where('user_id', $user->id);
        } elseif ($user->role === 'manager' && $user->department_id) {
            $query->where('department_id', $user->department_id);
        } elseif ($user->role === 'technician') {
            $query->where('assigned_to', $user->id);
        }

        $totalTickets = $query->count();
        $openTickets = $query->clone()->where('status', 'open')->count();
        $inProgressTickets = $query->clone()->where('status', 'in_progress')->count();
        $completedTickets = $query->clone()->where('status', 'completed')->count();

        $recentTickets = $query->clone()
            ->with(['category', 'priority'])
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'stats' => [
                'total' => $totalTickets,
                'open' => $openTickets,
                'in_progress' => $inProgressTickets,
                'completed' => $completedTickets,
            ],
            'recent_tickets' => $recentTickets
        ]);
    }

    /**
     * Export tickets to CSV or PDF
     */
    public function export(Request $request)
    {
        $user = Auth::user();

        // Build query dengan filter yang sama seperti index
        $query = Ticket::with(['user', 'category', 'priority', 'location', 'assignedUser', 'department']);

        // Apply filters (sama seperti di index)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('location', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $categories = (array) $request->category;
            if (!empty($categories)) {
                $query->whereIn('category_id', $categories);
            }
        }

        if ($request->filled('priority')) {
            $priorities = (array) $request->priority;
            if (!empty($priorities)) {
                $query->whereIn('priority_id', $priorities);
            }
        }

        if ($request->filled('department')) {
            $departments = (array) $request->department;
            if (!empty($departments)) {
                $query->whereIn('department_id', $departments);
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $tickets = $query->latest()->get();

        $format = $request->export;

        if ($format === 'csv') {
            return $this->exportToCSV($tickets);
        } elseif ($format === 'pdf') {
            return view('tickets.exports.print', compact('tickets'));
        }

        return redirect()->route('tickets.index')->with('error', 'Invalid export format');
    }

    private function exportToCSV($tickets)
    {
        $filename = 'maintenance_requests_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($tickets) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF"); // UTF-8 BOM

            // Header
            fputcsv($file, [
                'MR Number',
                'Title',
                'Status',
                'Priority',
                'Category',
                'Department',
                'Location',
                'Created By',
                'Created At',
                'Due Date'
            ]);

            // Data
            foreach ($tickets as $ticket) {
                $statusDisplay = $ticket->status == 'pending_vr' ? 'PR Approval' : ucfirst(str_replace('_', ' ', $ticket->status));

                fputcsv($file, [
                    $ticket->ticket_number,
                    $ticket->title,
                    $statusDisplay,
                    $ticket->priority->name ?? 'N/A',
                    $ticket->category->name ?? 'N/A',
                    $ticket->department->name ?? 'N/A',
                    $ticket->location->name ?? $ticket->location_manual ?? 'N/A',
                    $ticket->user->name ?? 'N/A',
                    $ticket->created_at->format('Y-m-d H:i:s'),
                    $ticket->due_date ? $ticket->due_date->format('Y-m-d') : '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
