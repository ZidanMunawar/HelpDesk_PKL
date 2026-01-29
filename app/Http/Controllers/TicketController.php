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
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    /**
     * Display all tickets dengan filter berdasarkan role
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Ticket::with(['user', 'category', 'priority', 'location', 'assignedUser', 'department']);

        // ==================== SEMUA ROLE BISA LIHAT SEMUA TICKET DI LIST ====================
        // Tidak ada filter di index, semua role bisa lihat semua ticket
        // Filter hanya untuk UI (search, status, dll) bukan untuk batasan akses

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by approval status
        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority_id', $request->priority);
        }

        // Filter by location
        if ($request->filled('location')) {
            $query->where('location_id', $request->location);
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        // ==================== FILTER "MY TICKETS" KHUSUS UNTUK USER ====================
        // Hanya user yang bisa filter "My Tickets", role lain gak perlu
        if ($request->filled('my_tickets') && $request->my_tickets == '1' && $user->role === 'user') {
            $query->where('user_id', $user->id);
        }

        // Search by ticket number or title
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('ticket_number', 'like', '%' . $request->search . '%')
                    ->orWhere('title', 'like', '%' . $request->search . '%')
                    ->orWhere('location_manual', 'like', '%' . $request->search . '%')
                    ->orWhereHas('user', function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        // Order by latest
        $tickets = $query->latest()->paginate(10);

        // Get filter options
        $categories = Category::active()->with('department')->get();
        $priorities = Priority::active()->orderBy('level')->get();
        $locations = Location::active()->get();
        $departments = Department::active()->get();

        // ==================== STATUS COUNTS UNTUK SEMUA TICKET (TANPA FILTER ROLE) ====================
        $baseQuery = Ticket::query();
        // Di dalam TicketController.php, method index():
// Update $statusCounts untuk user:

        if ($user->role === 'user') {
            // Untuk user: gabungkan pending_om dengan in_progress, pending_gm dengan completed
            $statusCounts = [
                'all' => $baseQuery->count(),
                'open' => $baseQuery->clone()->where('status', 'open')->count(),
                'received' => $baseQuery->clone()->where('status', 'received')->count(),
                'in_progress' => $baseQuery->clone()->whereIn('status', ['in_progress', 'pending_om'])->count(),
                'pending_vr' => $baseQuery->clone()->where('status', 'pending_vr')->count(),
                'completed' => $baseQuery->clone()->whereIn('status', ['completed', 'pending_gm'])->count(),
                'closed' => $baseQuery->clone()->where('status', 'closed')->count(),
                'cancelled' => $baseQuery->clone()->where('status', 'cancelled')->count(),
            ];
        } else {
            // Untuk admin/manager/technician/om/gm - semua status
            $statusCounts = [
                'all' => $baseQuery->count(),
                'open' => $baseQuery->clone()->where('status', 'open')->count(),
                'received' => $baseQuery->clone()->where('status', 'received')->count(),
                'pending_om' => $baseQuery->clone()->where('status', 'pending_om')->count(),
                'in_progress' => $baseQuery->clone()->where('status', 'in_progress')->count(),
                'pending_vr' => $baseQuery->clone()->where('status', 'pending_vr')->count(),
                'completed' => $baseQuery->clone()->where('status', 'completed')->count(),
                'pending_gm' => $baseQuery->clone()->where('status', 'pending_gm')->count(),
                'closed' => $baseQuery->clone()->where('status', 'closed')->count(),
                'cancelled' => $baseQuery->clone()->where('status', 'cancelled')->count(),
            ];
        }

        // Status options untuk semua role (full status)
        $statusOptions = [
            '' => 'All Status',
            'open' => 'Open',
            'received' => 'Received',
            'pending_om' => 'Pending OM',
            'in_progress' => 'In Progress',
            'pending_vr' => 'Pending VR',
            'completed' => 'Completed',
            'pending_gm' => 'Pending GM',
            'closed' => 'Closed',
            'cancelled' => 'Cancelled',
        ];

        return view('tickets.index', compact(
            'tickets',
            'categories',
            'priorities',
            'locations',
            'departments',
            'statusCounts',
            'statusOptions',
            'user'
        ));
    }

    /**
     * Show the form for creating a new ticket
     */
    public function create()
    {
        $user = Auth::user();

        // ==================== HANYA admin_eng DAN user YANG BISA CREATE TICKET ====================
        if (!in_array($user->role, ['admin_eng', 'user'])) {
            return redirect()->route('tickets.index')
                ->with('error', 'You are not authorized to create tickets.');
        }

        Log::info('User creating ticket:', [
            'user_id' => $user->id,
            'role' => $user->role,
            'department_id' => $user->department_id,
            'department_name' => $user->department->name ?? 'No department'
        ]);

        // Get categories filtered by user's department
        $categoriesQuery = Category::where('status', 'active');

        if ($user->department_id) {
            $categoriesQuery->where(function ($query) use ($user) {
                $query->where('department_id', $user->department_id)
                    ->orWhereNull('department_id');
            });
        } else {
            $categoriesQuery->whereNull('department_id');
        }

        $categories = $categoriesQuery->get();

        if ($categories->isEmpty()) {
            $categories = Category::where('status', 'active')->get();
            Log::warning('No categories found for user department, showing all categories');
        }

        $priorities = Priority::where('status', 'active')
            ->orderBy('level', 'desc')
            ->get();

        $locations = Location::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('tickets.create', compact('categories', 'priorities', 'locations'));
    }

    /**
     * Store new ticket with signature workflow
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // ==================== HANYA admin_eng DAN user YANG BISA CREATE TICKET ====================
        if (!in_array($user->role, ['admin_eng', 'user'])) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to create tickets.'
            ], 403);
        }

        // Debug log request
        Log::info('Ticket store request:', [
            'user_id' => $user->id,
            'title' => $request->title,
            'category_id' => $request->category_id,
            'has_signature' => !empty($request->signature_data),
            'attachments_count' => $request->hasFile('attachments') ? count($request->file('attachments')) : 0
        ]);

        // Validate request
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'priority_id' => 'required|exists:priorities,id',
            'department_id' => 'nullable|exists:departments,id',
            'location_id' => 'nullable|required_without:location_manual|exists:locations,id',
            'location_manual' => 'nullable|required_without:location_id|string|max:255',
            'due_date' => 'nullable|date|after:now',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            'signature_data' => 'required|string',
        ]);

        // Validasi: location_id atau location_manual harus ada salah satu
        if (!$request->location_id && !$request->location_manual) {
            return response()->json([
                'success' => false,
                'message' => 'Either location or manual location is required.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Generate ticket number
            $ticketNumber = $this->generateTicketNumber();

            // Debug: Check signature data
            Log::info('Signature data received:', [
                'starts_with_data_uri' => str_starts_with($request->signature_data, 'data:image/'),
                'length' => strlen($request->signature_data),
            ]);

            // Save signature
            $signaturePath = null;
            if ($request->signature_data && str_starts_with($request->signature_data, 'data:image/')) {
                $signaturePath = $this->saveSignature($request->signature_data, $ticketNumber, $user->id);
                Log::info('Signature saved successfully:', ['path' => $signaturePath]);
            } else {
                Log::error('Invalid signature data format');
                throw new \Exception('Invalid signature format. Please sign again.');
            }

            // Create ticket
            $ticketData = [
                'ticket_number' => $ticketNumber,
                'title' => $request->title,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'priority_id' => $request->priority_id,
                'department_id' => $request->department_id ?? $user->department_id,
                'location_id' => $request->location_id,
                'location_manual' => $request->location_manual,
                'user_id' => $user->id,
                'status' => 'open',
                'current_stage' => 1, // Requested stage
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
                'stage' => 1, // Requested stage
                'signature_path' => $signaturePath,
                'signed_at' => now(),
                'ip_address' => $request->ip(),
            ]);

            Log::info('Reporter signature saved:', [
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'stage' => 1
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

            // Create notification for Engineering Department
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
     * Check if user can view ticket details (untuk modal info)
     */
    public function checkAccess($id)
    {
        $ticket = Ticket::with(['user', 'category', 'department'])->findOrFail($id);
        $user = Auth::user();

        // ==================== LOGIKA AKSES DETAIL TICKET ====================
        $canViewFull = false;
        $reason = '';

        switch ($user->role) {
            case 'superadmin':
            case 'admin_eng':
            case 'om':
            case 'gm':
                // Bisa akses semua ticket
                $canViewFull = true;
                $reason = 'Full access granted for ' . $user->role;
                break;

            case 'user':
                // Hanya ticket milik sendiri
                if ($ticket->user_id === $user->id) {
                    $canViewFull = true;
                    $reason = 'Ticket owner';
                } else {
                    $canViewFull = false;
                    $reason = 'This ticket belongs to another user';
                }
                break;

            case 'technician':
                // Hanya ticket yang diassign ke dia
                if ($ticket->assigned_to === $user->id) {
                    $canViewFull = true;
                    $reason = 'Assigned technician';
                } else {
                    $canViewFull = false;
                    $reason = 'Ticket not assigned to you';
                }
                break;

            case 'manager':
                // Hanya ticket di departemennya
                if ($ticket->department_id === $user->department_id) {
                    $canViewFull = true;
                    $reason = 'Department manager';
                } else {
                    $canViewFull = false;
                    $reason = 'Ticket from different department';
                }
                break;

            default:
                $canViewFull = false;
                $reason = 'No access permissions';
                break;
        }

        // Jika tidak bisa view full, return info untuk modal
        if (!$canViewFull) {
            return response()->json([
                'type' => 'modal_info',
                'ticket_info' => [
                    'number' => $ticket->ticket_number,
                    'title' => $ticket->title,
                    'status' => $ticket->status,
                    'created_by' => $ticket->user->name,
                    'created_at' => $ticket->created_at->format('d M Y, H:i'),
                    'category' => $ticket->category->name,
                    'department' => $ticket->department->name ?? 'N/A',
                    'reason' => $reason
                ]
            ]);
        }

        // Jika bisa view full, redirect ke show page
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

        // Get last ticket number for today
        $lastTicket = Ticket::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastTicket) {
            // Extract sequence from last ticket number
            preg_match('/TKT-(\d{8})-(\d{4})/', $lastTicket->ticket_number, $matches);
            if (isset($matches[2])) {
                $sequence = (int) $matches[2] + 1;
            }
        }

        return 'TKT-' . $year . $month . $day . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Save signature from data URL
     */
    private function saveSignature($signatureData, $ticketNumber, $userId)
    {
        try {
            // Validate data URL format
            if (!preg_match('#^data:image/\w+;base64,#i', $signatureData)) {
                throw new \Exception('Invalid signature data URL format');
            }

            // Decode base64
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signatureData));

            if ($imageData === false) {
                throw new \Exception('Failed to decode base64 signature data');
            }

            // Generate unique filename
            $fileName = 'signature_' . $ticketNumber . '_user' . $userId . '_' . time() . '.png';
            $filePath = 'signatures/' . $fileName;

            // Ensure directory exists
            Storage::disk('public')->makeDirectory('signatures');

            // Save to storage
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
     * Notify Engineering Department (Admin/Technician)
     */
    private function notifyEngineeringDepartment(Ticket $ticket)
    {
        try {
            // Get users in Engineering department (ID 4) atau role tertentu
            $engineeringUsers = User::where(function ($query) {
                $query->where('department_id', 4) // Engineering & Maintenance
                    ->orWhereIn('role', ['admin_eng', 'technician', 'superadmin']);
            })
                ->where('status', 'active')
                ->get();

            Log::info('Notifying engineering users:', [
                'count' => $engineeringUsers->count(),
                'users' => $engineeringUsers->pluck('name')->toArray()
            ]);

            foreach ($engineeringUsers as $user) {
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
            }

            Log::info('Notifications created for engineering department');
        } catch (\Exception $e) {
            Log::error('Error notifying engineering department:', [
                'error' => $e->getMessage(),
                'ticket_id' => $ticket->id
            ]);
            // Don't throw, just log the error
        }
    }

    /**
     * Get tickets for dashboard
     */
    public function dashboardStats()
    {
        $user = Auth::user();

        $query = Ticket::query();

        // Apply role filters
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

        // Recent tickets
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
}
