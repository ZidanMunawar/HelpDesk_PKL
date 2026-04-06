<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'active']);
    }

    /**
     * Show simple dashboard for all roles
     */
    public function index()
    {
        $user = Auth::user();

        // Common data for all users
        $data = [
            'user' => $user,
            'recentActivities' => ActivityLog::with('ticket')
                ->where('user_id', $user->id)
                ->orWhere(function ($query) use ($user) {
                    // For tickets created by user
                    $query->whereHas('ticket', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
                })
                ->latest()
                ->take(10)
                ->get(),
        ];

        // Add role-specific data
        switch ($user->role) {
            case 'superadmin':
            case 'admin_eng':
            case 'gm':
            case 'om':
            case 'manager':
                $data = array_merge($data, $this->getAdminStats());
                break;

            case 'technician':
                $data = array_merge($data, $this->getTechnicianStats($user));
                break;

            case 'user':
                $data = array_merge($data, $this->getUserStats($user));
                break;
        }

        return view('dashboard', $data);
    }

    /**
     * Get statistics for admin/manager roles
     */
    private function getAdminStats()
    {
        return [
            'totalTickets' => Ticket::count(),
            'openTickets' => Ticket::where('status', 'open')->count(),
            'inProgressTickets' => Ticket::whereIn('status', ['received', 'pending_om', 'in_progress', 'pending_vr'])->count(),
            'completedTickets' => Ticket::whereIn('status', ['completed', 'pending_gm', 'ready_for_closure', 'closed'])->count(),
        ];
    }

    /**
     * Get statistics for technician
     */
    private function getTechnicianStats($user)
    {
        $assignedTickets = Ticket::where('assigned_to', $user->id)->get();

        return [
            'assignedTicketsCount' => $assignedTickets->count(),
            'inProgressCount' => $assignedTickets->where('status', 'in_progress')->count(),
            'completedCount' => $assignedTickets->where('status', 'completed')->count(),
            'pendingVRCount' => $assignedTickets->where('status', 'pending_vr')->count(),
        ];
    }

    /**
     * Get statistics for regular user
     */
    private function getUserStats($user)
    {
        $myTickets = Ticket::where('user_id', $user->id)->get();

        return [
            'myTicketsCount' => $myTickets->count(),
            'myOpenTicketsCount' => $myTickets->where('status', 'open')->count(),
            'myCompletedTicketsCount' => $myTickets->whereIn('status', ['completed', 'pending_gm', 'ready_for_closure', 'closed'])->count(),
        ];
    }
}
