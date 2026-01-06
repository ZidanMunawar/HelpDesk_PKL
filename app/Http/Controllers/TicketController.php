<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Category;
use App\Models\Priority;
use App\Models\Location;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
class TicketController extends Controller
{
    /**
     * Display all tickets (accessible by all users)
     */
    public function index(Request $request)
    {
        $query = Ticket::with(['user', 'category', 'priority', 'location', 'assignedUser']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
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

        // Search by ticket number or title
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('ticket_number', 'like', '%' . $request->search . '%')
                    ->orWhere('title', 'like', '%' . $request->search . '%');
            });
        }

        $tickets = $query->latest()->get();

        // Get filter options
        $categories = Category::active()->get();
        $priorities = Priority::active()->get();
        $locations = Location::active()->get();

        // Count by status
        $statusCounts = [
            'all' => Ticket::count(),
            'open' => Ticket::where('status', 'open')->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'pending' => Ticket::where('status', 'pending')->count(),
            'resolved' => Ticket::where('status', 'resolved')->count(),
            'closed' => Ticket::where('status', 'closed')->count(),
        ];

        return view('tickets.index', compact('tickets', 'categories', 'priorities', 'locations', 'statusCounts'));
    }

    /**
     * Display tickets created by authenticated user
     */
    public function myTickets(Request $request)
    {
        $query = Ticket::with(['category', 'priority', 'location', 'assignedUser'])
            ->where('user_id', auth()->id());

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Search by ticket number or title
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('ticket_number', 'like', '%' . $request->search . '%')
                    ->orWhere('title', 'like', '%' . $request->search . '%');
            });
        }

        $tickets = $query->latest()->get();

        // Get filter options
        $categories = Category::active()->get();
        $priorities = Priority::active()->get();
        $locations = Location::active()->get();

        // Count by status (only for current user's tickets)
        $statusCounts = [
            'all' => Ticket::where('user_id', auth()->id())->count(),
            'open' => Ticket::where('user_id', auth()->id())->where('status', 'open')->count(),
            'in_progress' => Ticket::where('user_id', auth()->id())->where('status', 'in_progress')->count(),
            'resolved' => Ticket::where('user_id', auth()->id())->where('status', 'resolved')->count(),
            'closed' => Ticket::where('user_id', auth()->id())->where('status', 'closed')->count(),
        ];

        return view('tickets.my-tickets', compact('tickets', 'categories', 'priorities', 'locations', 'statusCounts'));
    }
    public function create()
    {
        $categories = Category::where('status', 'active')->get();
        $priorities = Priority::where('status', 'active')->orderBy('level')->get();
        $locations = Location::where('status', 'active')->get();

        return view('tickets.create', compact('categories', 'priorities', 'locations'));
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
            'location_id' => 'nullable|exists:locations,id',
            'due_date' => 'nullable|date|after:now',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120', // 5MB max
        ]);

        DB::beginTransaction();
        try {
            // Generate ticket number
            $ticketNumber = $this->generateTicketNumber();

            // Create ticket
            $ticket = Ticket::create([
                'ticket_number' => $ticketNumber,
                'title' => $request->title,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'priority_id' => $request->priority_id,
                'location_id' => $request->location_id,
                'user_id' => auth()->id(),
                'status' => 'open',
                'due_date' => $request->due_date,
            ]);

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
