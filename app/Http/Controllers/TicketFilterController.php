<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Category;
use App\Models\Priority;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;

class TicketFilterController extends Controller
{
    /**
     * Display tickets assigned to authenticated user
     */
    public function assignedToMe(Request $request)
    {
        $user = auth()->user();

        // Check if user has department or is admin
        if (!$user->department_id && $user->role !== 'admin') {
            abort(403, 'You do not have permission to access this page. Please contact administrator.');
        }

        $query = Ticket::with(['user', 'category', 'priority', 'location', 'assignedUser', 'department'])
            ->where('assigned_to', $user->id);

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

        // Search by ticket number or title or user
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('ticket_number', 'like', '%' . $request->search . '%')
                    ->orWhere('title', 'like', '%' . $request->search . '%')
                    ->orWhereHas('user', function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        // Get all tickets (no pagination for assigned tickets)
        $tickets = $query->latest()->get();

        // Get filter options
        $categories = Category::where('status', 'active')->get();
        $priorities = Priority::where('status', 'active')->orderBy('level')->get();
        $locations = Location::where('status', 'active')->get();

        // Count by status (only for assigned tickets)
        $statusCounts = [
            'all' => Ticket::where('assigned_to', $user->id)->count(),
            'open' => Ticket::where('assigned_to', $user->id)->where('status', 'open')->count(),
            'in_progress' => Ticket::where('assigned_to', $user->id)->where('status', 'in_progress')->count(),
            'pending' => Ticket::where('assigned_to', $user->id)->where('status', 'pending')->count(),
            'resolved' => Ticket::where('assigned_to', $user->id)->where('status', 'resolved')->count(),
            'closed' => Ticket::where('assigned_to', $user->id)->where('status', 'closed')->count(),
        ];

        return view('tickets.assigned-to-me', compact('tickets', 'categories', 'priorities', 'locations', 'statusCounts'));
    }

    /**
     * Display unassigned tickets (Admin only)
     */
    public function unassigned(Request $request)
    {
        // Admin only
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Only administrators can access this page.');
        }

        $query = Ticket::with(['user', 'category', 'priority', 'location', 'department'])
            ->whereNull('assigned_to')
            ->whereNotIn('status', ['closed', 'cancelled']);

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

        // Search by ticket number, title, or user
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('ticket_number', 'like', '%' . $request->search . '%')
                    ->orWhere('title', 'like', '%' . $request->search . '%')
                    ->orWhereHas('user', function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        // Sort by priority level (urgent first)
        $tickets = $query->get()->sortBy(function ($ticket) {
            return $ticket->priority->level ?? 999;
        })->values();

        // Get filter options
        $categories = Category::where('status', 'active')->get();
        $priorities = Priority::where('status', 'active')->get();
        $locations = Location::where('status', 'active')->get();

        // Get assignable users
        $assignableUsers = User::whereNotNull('department_id')
            ->where('status', 'active')
            ->get();

        // Count by status (only unassigned)
        $statusCounts = [
            'all' => Ticket::whereNull('assigned_to')->whereNotIn('status', ['closed', 'cancelled'])->count(),
            'open' => Ticket::whereNull('assigned_to')->where('status', 'open')->count(),
            'in_progress' => Ticket::whereNull('assigned_to')->where('status', 'in_progress')->count(),
            'pending' => Ticket::whereNull('assigned_to')->where('status', 'pending')->count(),
        ];

        return view('tickets.unassigned', compact('tickets', 'categories', 'priorities', 'locations', 'statusCounts', 'assignableUsers'));
    }

    /**
     * Bulk assign tickets (Admin only)
     */
    public function bulkAssign(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'ticket_ids' => 'required|array',
            'ticket_ids.*' => 'exists:tickets,id',
            'assigned_to' => 'required|exists:users,id'
        ]);

        try {
            $assignedUser = User::find($request->assigned_to);
            $ticketCount = count($request->ticket_ids);

            foreach ($request->ticket_ids as $ticketId) {
                $ticket = Ticket::find($ticketId);
                $ticket->update([
                    'assigned_to' => $request->assigned_to,
                    'status' => $ticket->status === 'open' ? 'in_progress' : $ticket->status
                ]);

                // Log activity
                \App\Models\ActivityLog::create([
                    'user_id' => auth()->id(),
                    'ticket_id' => $ticket->id,
                    'action' => 'assigned',
                    'description' => "Ticket assigned to {$assignedUser->name} via bulk action",
                    'new_values' => json_encode(['assigned_to' => $request->assigned_to]),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "{$ticketCount} ticket(s) assigned to {$assignedUser->name}"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign tickets: ' . $e->getMessage()
            ], 500);
        }
    }
}
