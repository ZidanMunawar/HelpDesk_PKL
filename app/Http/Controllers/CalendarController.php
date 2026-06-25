<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Priority;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    /**
     * Display calendar page
     */
    public function index()
    {
        // Ambil SEMUA priorities (termasuk yang non-active) untuk CSS dinamis
        $priorities = Priority::orderBy('level')->get();
        return view('calendar.index', compact('priorities'));
    }

    /**
     * Get events for calendar (JSON) - SEMUA TICKET SEMUA USER
     */
    public function getEvents(Request $request)
    {
        // Ambil filter dari request
        $showCreated = $request->has('show_created') ? filter_var($request->show_created, FILTER_VALIDATE_BOOLEAN) : true;
        $showClosed = $request->has('show_closed') ? filter_var($request->show_closed, FILTER_VALIDATE_BOOLEAN) : true;

        // Ambil SEMUA ticket, tanpa filter role
        $tickets = Ticket::with(['user', 'priority', 'department', 'category'])->get();

        $events = [];

        foreach ($tickets as $ticket) {
            // Event untuk tanggal dibuat (start)
            if ($showCreated) {
                $events[] = [
                    'id' => 'created-' . $ticket->id,
                    'ticket_id' => $ticket->id,
                    'title' => $ticket->ticket_number,
                    'start' => $ticket->created_at->format('Y-m-d'),
                    'end' => $ticket->created_at->format('Y-m-d'),
                    'color' => '#003366',
                    'textColor' => '#ffffff',
                    'description' => 'CREATED: ' . $ticket->created_at->format('d M Y') . ' | ' . $ticket->title,
                    'allDay' => true,
                    'type' => 'created',
                    'ticket_number' => $ticket->ticket_number,
                    'title_full' => $ticket->title,
                    'status' => $ticket->status,
                    'status_display' => $this->getStatusDisplayName($ticket->status),
                    'created_by' => $ticket->user->name ?? 'Unknown',
                    'department' => $ticket->department->name ?? 'N/A',
                    'category' => $ticket->category->name ?? 'N/A',
                    'priority' => $ticket->priority->name ?? 'N/A',
                    'priority_id' => $ticket->priority->id ?? null,
                    'priority_name' => $ticket->priority->name ?? 'MEDIUM',
                    'priority_color' => $ticket->priority->color ?? '#000000',
                    'priority_level' => $ticket->priority->level ?? 3,
                    'created_at_formatted' => $ticket->created_at->format('d M Y, H:i'),
                    'event_date' => $ticket->created_at->format('d M Y')
                ];
            }

            // Event untuk tanggal closed (jika ada)
            if ($showClosed && $ticket->closed_at) {
                $events[] = [
                    'id' => 'closed-' . $ticket->id,
                    'ticket_id' => $ticket->id,
                    'title' => $ticket->ticket_number,
                    'start' => $ticket->closed_at->format('Y-m-d'),
                    'end' => $ticket->closed_at->format('Y-m-d'),
                    'color' => '#ff6600',
                    'textColor' => '#ffffff',
                    'description' => 'CLOSED: ' . $ticket->closed_at->format('d M Y') . ' | ' . $ticket->title,
                    'allDay' => true,
                    'type' => 'closed',
                    'ticket_number' => $ticket->ticket_number,
                    'title_full' => $ticket->title,
                    'status' => $ticket->status,
                    'status_display' => $this->getStatusDisplayName($ticket->status),
                    'created_by' => $ticket->user->name ?? 'Unknown',
                    'department' => $ticket->department->name ?? 'N/A',
                    'category' => $ticket->category->name ?? 'N/A',
                    'priority' => $ticket->priority->name ?? 'N/A',
                    'priority_id' => $ticket->priority->id ?? null,
                    'priority_name' => $ticket->priority->name ?? 'MEDIUM',
                    'priority_color' => $ticket->priority->color ?? '#000000',
                    'priority_level' => $ticket->priority->level ?? 3,
                    'closed_at_formatted' => $ticket->closed_at->format('d M Y, H:i'),
                    'event_date' => $ticket->closed_at->format('d M Y')
                ];
            }
        }

        return response()->json($events);
    }

    /**
     * Get status display name (UBAH pending_vr jadi PR Approval)
     */
    private function getStatusDisplayName($status)
    {
        $displayNames = [
            'open' => 'Open',
            'received' => 'Received',
            'pending_om' => 'OM Approval',
            'in_progress' => 'In Progress',
            'pending_vr' => 'PR Approval',  // ✅ UBAH VR jadi PR
            'completed' => 'Completed',
            'pending_gm' => 'GM Approval',
            'ready_for_closure' => 'Ready for Closure',
            'closed' => 'Closed',
            'cancelled' => 'Cancelled'
        ];
        return $displayNames[$status] ?? $status;
    }

    /**
     * Check access for ticket
     */
    public function checkAccess($id)
    {
        $ticket = Ticket::with(['user', 'department', 'category', 'priority'])->findOrFail($id);
        $user = Auth::user();

        // Cek apakah user bisa view ticket
        $canView = $this->canViewTicket($user, $ticket);

        if ($canView) {
            return response()->json([
                'type' => 'redirect',
                'url' => route('tickets.show', $ticket->id)
            ]);
        } else {
            $ticketInfo = [
                'number' => $ticket->ticket_number,
                'title' => $ticket->title,
                'status' => $ticket->status,
                'status_display' => $this->getStatusDisplayName($ticket->status),
                'created_by' => $ticket->user->name ?? 'Unknown',
                'department' => $ticket->department->name ?? 'N/A',
                'category' => $ticket->category->name ?? 'N/A',
                'priority' => $ticket->priority->name ?? 'N/A',
                'priority_color' => $ticket->priority->color ?? '#000000',
                'created_at' => $ticket->created_at->format('d M Y, H:i'),
                'reason' => $this->getAccessDeniedReason($user, $ticket)
            ];

            return response()->json([
                'type' => 'modal_info',
                'ticket_info' => $ticketInfo
            ]);
        }
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
     * Get reason for access denied
     */
    private function getAccessDeniedReason($user, $ticket)
    {
        if ($user->role === 'user') {
            return "This Maintenance Request was created by another user. You can only view requests you created yourself.";
        } elseif ($user->role === 'technician') {
            return "This Maintenance Request is not assigned to you. You can only view requests assigned to you or created by you.";
        } elseif ($user->role === 'manager') {
            return "This Maintenance Request is not from your department. You can only view requests from your own department.";
        }
        return "You don't have permission to view this Maintenance Request.";
    }

    /**
     * Print calendar view
     */
    public function print(Request $request)
    {
        $year = $request->year ?? date('Y');
        $month = $request->month ?? date('m');
        // Ambil SEMUA priorities (termasuk yang non-active)
        $priorities = Priority::orderBy('level')->get();

        $tickets = Ticket::with(['user', 'priority', 'department', 'category'])->get();

        $events = [];
        foreach ($tickets as $ticket) {
            $events['created'][] = [
                'ticket' => $ticket,
                'date' => $ticket->created_at->format('Y-m-d')
            ];
            if ($ticket->closed_at) {
                $events['closed'][] = [
                    'ticket' => $ticket,
                    'date' => $ticket->closed_at->format('Y-m-d')
                ];
            }
        }

        return view('calendar.print', compact('year', 'month', 'events', 'priorities'));
    }
}
