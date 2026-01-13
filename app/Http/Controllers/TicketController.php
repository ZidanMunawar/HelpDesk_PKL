<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Category;
use App\Models\Location;
use App\Models\Priority;
use App\Models\Department;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    /**
     * Display all tickets (accessible by all users)
     */
    /**
     * Display all tickets (accessible by all users)
     */
    public function index(Request $request)
    {
        $query = Ticket::with(['user', 'category', 'priority', 'location', 'assignedUser', 'department']);

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
        $tickets = $query->latest()->paginate(5);

        // Get filter options
        $categories = Category::active()->with('department')->get();
        $priorities = Priority::active()->orderBy('level')->get();
        $locations = Location::active()->get();
        $departments = Department::active()->get();

        // Count by status
        $statusCounts = [
            'all' => Ticket::count(),
            'open' => Ticket::where('status', 'open')->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'pending' => Ticket::where('status', 'pending')->count(),
            'resolved' => Ticket::where('status', 'resolved')->count(),
            'closed' => Ticket::where('status', 'closed')->count(),
            'cancelled' => Ticket::where('status', 'cancelled')->count(),
        ];

        return view('tickets.index', compact('tickets', 'categories', 'priorities', 'locations', 'departments', 'statusCounts'));
    }

    /**
     * Display tickets created by authenticated user
     */
    /**
     * Display tickets created by authenticated user
     */
    public function myTickets(Request $request)
    {
        $query = Ticket::with(['category', 'priority', 'location', 'assignedUser', 'department'])
            ->where('user_id', auth()->id());

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

        // Search by ticket number or title
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('ticket_number', 'like', '%' . $request->search . '%')
                    ->orWhere('title', 'like', '%' . $request->search . '%')
                    ->orWhere('location_manual', 'like', '%' . $request->search . '%');
            });
        }

        $tickets = $query->latest()->paginate(5);

        // Get filter options
        $categories = Category::where('status', 'active')->with('department')->get();
        $priorities = Priority::where('status', 'active')->orderBy('level')->get();
        $locations = Location::where('status', 'active')->get();
        $departments = Department::where('status', 'active')->get();

        // Count by status (only for current user's tickets)
        $statusCounts = [
            'all' => Ticket::where('user_id', auth()->id())->count(),
            'open' => Ticket::where('user_id', auth()->id())->where('status', 'open')->count(),
            'in_progress' => Ticket::where('user_id', auth()->id())->where('status', 'in_progress')->count(),
            'pending' => Ticket::where('user_id', auth()->id())->where('status', 'pending')->count(),
            'resolved' => Ticket::where('user_id', auth()->id())->where('status', 'resolved')->count(),
            'closed' => Ticket::where('user_id', auth()->id())->where('status', 'closed')->count(),
            'cancelled' => Ticket::where('user_id', auth()->id())->where('status', 'cancelled')->count(),
        ];

        return view('tickets.my-tickets', compact('tickets', 'categories', 'priorities', 'locations', 'departments', 'statusCounts'));
    }
    /**
     * Show the form for creating a new ticket
     */
    public function create()
    {
        $categories = Category::where('status', 'active')->get();
        $priorities = Priority::where('status', 'active')->orderBy('level')->get();
        $locations = Location::where('status', 'active')->get();
        $departments = Department::where('status', 'active')->get();

        return view('tickets.create', compact('categories', 'priorities', 'locations', 'departments'));
    }

    /**
     * Store new ticket
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'priority_id' => 'required|exists:priorities,id',
            'department_id' => 'nullable|exists:departments,id',
            'location_id' => 'nullable|exists:locations,id',
            'location_manual' => 'nullable|string|max:255',
            'estimated_cost' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date|after:now',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120', // 5MB max
        ]);

        DB::beginTransaction();
        try {
            // Generate ticket number
            $ticketNumber = $this->generateTicketNumber();

            // Prepare ticket data
            $ticketData = [
                'ticket_number' => $ticketNumber,
                'title' => $request->title,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'priority_id' => $request->priority_id,
                'department_id' => $request->department_id,
                'user_id' => auth()->id(),
                'status' => 'open',
                'approval_status' => 'pending_approval',
                'due_date' => $request->due_date,
                'estimated_cost' => $request->estimated_cost,
            ];

            // Handle location
            if ($request->filled('location_id')) {
                $ticketData['location_id'] = $request->location_id;
                $ticketData['location_manual'] = null;
            } elseif ($request->filled('location_manual')) {
                $ticketData['location_id'] = null;
                $ticketData['location_manual'] = $request->location_manual;
            }

            // Create ticket
            $ticket = Ticket::create($ticketData);

            // Handle file attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('ticket_attachments', $fileName, 'public');

                    $ticket->attachments()->create([
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                        'uploaded_by' => auth()->id(),
                    ]);
                }
            }

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'ticket_id' => $ticket->id,
                'action' => 'created',
                'description' => 'Ticket created by ' . auth()->user()->name,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ticket created successfully!',
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticketNumber,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate unique ticket number
     */
    private function generateTicketNumber()
    {
        $year = date('Y');
        $month = date('m');

        // Format: TKT-YYYYMM-XXXX
        $prefix = 'TKT-' . $year . $month . '-';

        // Get last ticket number for this month
        $lastTicket = Ticket::where('ticket_number', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastTicket) {
            $lastNumber = intval(substr($lastTicket->ticket_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }

}
