<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Notification;
use App\Models\User;
use App\Models\Department;
use App\Models\VoucherRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        // Data dasar
        $data = [
            'user' => $user,
            'role' => $role,
        ];

        // ==========================================
        // DASHBOARD BERDASARKAN ROLE
        // ==========================================

        if ($role === 'superadmin') {
            $data = array_merge($data, $this->getSuperAdminData());
        } elseif ($role === 'admin_eng') {
            $data = array_merge($data, $this->getAdminEngData());
        } elseif ($role === 'om') {
            $data = array_merge($data, $this->getOMData());
        } elseif ($role === 'gm') {
            $data = array_merge($data, $this->getGMData());
        } elseif ($role === 'manager') {
            $data = array_merge($data, $this->getManagerData($user));
        } elseif ($role === 'technician') {
            $data = array_merge($data, $this->getTechnicianData($user));
        } elseif ($role === 'user') {
            $data = array_merge($data, $this->getUserData($user));
        }

        return view('dashboard', $data);
    }

    /**
     * Data untuk Super Admin
     */
    private function getSuperAdminData()
    {
        // Total users by role (active only)
        $usersByRole = User::select('role', DB::raw('count(*) as total'))
            ->where('status', 'active')
            ->groupBy('role')
            ->get()
            ->pluck('total', 'role')
            ->toArray();

        // Ticket statistics berdasarkan status di database
        $ticketStats = [
            'total' => Ticket::count(),
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

        // Department statistics
        $departments = Department::where('status', 'active')->count();

        // Pending users (status pending)
        $pendingUsers = User::where('status', 'pending')->count();

        // Voucher/Purchase Request statistics
        $voucherStats = [
            'total' => VoucherRequest::count(),
            'pending' => VoucherRequest::where('status', 'pending')->count(),
            'admin_approved' => VoucherRequest::where('status', 'admin_approved')->count(),
            'om_approved' => VoucherRequest::where('status', 'om_approved')->count(),
            'gm_approved' => VoucherRequest::where('status', 'gm_approved')->count(),
            'paid' => VoucherRequest::where('status', 'paid')->count(),
            'rejected' => VoucherRequest::where('status', 'rejected')->count(),
        ];

        // Chart data - MR per month (last 6 months)
        $ticketsPerMonth = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = Ticket::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $ticketsPerMonth[] = [
                'month' => $month->format('M Y'),
                'count' => $count
            ];
        }

        // Chart data - Users by role
        $roleLabels = ['superadmin', 'admin_eng', 'om', 'gm', 'manager', 'technician', 'user'];
        $roleData = [];
        foreach ($roleLabels as $role) {
            $roleData[] = $usersByRole[$role] ?? 0;
        }

        return [
            'usersByRole' => $usersByRole,
            'totalUsers' => array_sum($usersByRole),
            'ticketStats' => $ticketStats,
            'totalDepartments' => $departments,
            'pendingUsers' => $pendingUsers,
            'voucherStats' => $voucherStats,
            'ticketsPerMonth' => $ticketsPerMonth,
            'roleLabels' => $roleLabels,
            'roleData' => $roleData,
        ];
    }

    /**
     * Data untuk Admin Engineering
     */
    private function getAdminEngData()
    {
        // Ticket statistics
        $ticketStats = [
            'total' => Ticket::count(),
            'open' => Ticket::where('status', 'open')->count(),
            'received' => Ticket::where('status', 'received')->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'pending_vr' => Ticket::where('status', 'pending_vr')->count(),
            'pending_om' => Ticket::where('status', 'pending_om')->count(),
            'completed' => Ticket::where('status', 'completed')->count(),
            'pending_gm' => Ticket::where('status', 'pending_gm')->count(),
            'ready_for_closure' => Ticket::where('status', 'ready_for_closure')->count(),
            'closed' => Ticket::where('status', 'closed')->count(),
            'cancelled' => Ticket::where('status', 'cancelled')->count(),
        ];

        // Technicians count
        $technicians = User::where('role', 'technician')->where('status', 'active')->count();

        // Pending PR approvals (voucher requests)
        $pendingPR = VoucherRequest::where('status', 'pending')->count();

        // Tickets by priority untuk chart
        $ticketsByPriority = Ticket::select('priority_id', DB::raw('count(*) as total'))
            ->groupBy('priority_id')
            ->with('priority')
            ->get();

        // Recent MR (limit 5)
        $recentTickets = Ticket::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return [
            'ticketStats' => $ticketStats,
            'totalTechnicians' => $technicians,
            'pendingPR' => $pendingPR,
            'ticketsByPriority' => $ticketsByPriority,
            'recentTickets' => $recentTickets,
        ];
    }

    /**
     * Data untuk Operation Manager (OM)
     */
    private function getOMData()
    {
        // Tickets pending OM approval (status pending_om)
        $pendingOMApproval = Ticket::where('status', 'pending_om')->count();

        // PR that need OM approval (status admin_approved, om_approved = 0)
        $pendingPRApproval = VoucherRequest::where('status', 'admin_approved')
            ->where('om_approved', 0)
            ->count();

        // Total MR this month
        $ticketsThisMonth = Ticket::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Completed MR this month (status completed or closed)
        $completedThisMonth = Ticket::where(function ($q) {
            $q->where('status', 'completed')
                ->orWhere('status', 'closed');
        })
            ->whereMonth('closed_at', now()->month)
            ->whereYear('closed_at', now()->year)
            ->count();

        return [
            'pendingOMApproval' => $pendingOMApproval,
            'pendingPRApproval' => $pendingPRApproval,
            'ticketsThisMonth' => $ticketsThisMonth,
            'completedThisMonth' => $completedThisMonth,
        ];
    }

    /**
     * Data untuk General Manager (GM)
     */
    private function getGMData()
    {
        // Tickets pending GM approval (status pending_gm)
        $pendingGMApproval = Ticket::where('status', 'pending_gm')->count();

        // PR pending GM approval (status om_approved, gm_approved = 0)
        $pendingPRApproval = VoucherRequest::where('status', 'om_approved')
            ->where('gm_approved', 0)
            ->count();

        // Total MR this month
        $ticketsThisMonth = Ticket::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Total PR amount this month (paid status)
        // Note: amount is stored in notes field with format "Total: Rp X"
        $vrTotalAmount = 0;
        $paidPRs = VoucherRequest::where('status', 'paid')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->get();

        foreach ($paidPRs as $pr) {
            if ($pr->notes && preg_match('/Total: Rp ([\d,]+)/', $pr->notes, $matches)) {
                $amount = str_replace(',', '', $matches[1]);
                $vrTotalAmount += (float) $amount;
            }
        }

        return [
            'pendingGMApproval' => $pendingGMApproval,
            'pendingPRApproval' => $pendingPRApproval,
            'ticketsThisMonth' => $ticketsThisMonth,
            'vrTotalAmount' => $vrTotalAmount > 0 ? 'Rp ' . number_format($vrTotalAmount, 0, ',', '.') : 'Rp 0',
        ];
    }

    /**
     * Data untuk Manager Engineering
     */
    private function getManagerData($user)
    {
        $departmentId = $user->department_id;
        $departmentName = $user->department ? $user->department->name : 'Engineering';

        // Tickets in manager's department
        $departmentTickets = Ticket::where('department_id', $departmentId);

        $ticketStats = [
            'total' => (clone $departmentTickets)->count(),
            'open' => (clone $departmentTickets)->where('status', 'open')->count(),
            'received' => (clone $departmentTickets)->where('status', 'received')->count(),
            'in_progress' => (clone $departmentTickets)->where('status', 'in_progress')->count(),
            'pending_vr' => (clone $departmentTickets)->where('status', 'pending_vr')->count(),
            'completed' => (clone $departmentTickets)->where('status', 'completed')->count(),
            'closed' => (clone $departmentTickets)->where('status', 'closed')->count(),
        ];

        // Technicians in department
        $technicians = User::where('department_id', $departmentId)
            ->where('role', 'technician')
            ->where('status', 'active')
            ->count();

        // Average completion time (in hours)
        $avgCompletionTime = Ticket::where('department_id', $departmentId)
            ->whereNotNull('closed_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, closed_at)) as avg_hours'))
            ->value('avg_hours');

        return [
            'ticketStats' => $ticketStats,
            'totalTechnicians' => $technicians,
            'avgCompletionTime' => $avgCompletionTime ? round($avgCompletionTime, 1) . ' hrs' : 'N/A',
            'departmentName' => $departmentName,
        ];
    }

    /**
     * Data untuk Technician
     */
    private function getTechnicianData($user)
    {
        $technicianId = $user->id;

        $ticketStats = [
            'assigned' => Ticket::where('assigned_to', $technicianId)
                ->whereIn('status', ['in_progress', 'received', 'pending_vr'])
                ->count(),
            'in_progress' => Ticket::where('assigned_to', $technicianId)
                ->where('status', 'in_progress')
                ->count(),
            'completed' => Ticket::where('assigned_to', $technicianId)
                ->whereIn('status', ['completed', 'closed'])
                ->count(),
            'total' => Ticket::where('assigned_to', $technicianId)->count(),
        ];

        // Pending PR requests by this technician
        $pendingPR = VoucherRequest::where('created_by', $technicianId)
            ->whereIn('status', ['pending', 'admin_approved', 'om_approved'])
            ->count();

        // Recent tickets assigned
        $recentTickets = Ticket::where('assigned_to', $technicianId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return [
            'ticketStats' => $ticketStats,
            'pendingPR' => $pendingPR,
            'recentTickets' => $recentTickets,
        ];
    }

    /**
     * Data untuk User biasa
     */
    private function getUserData($user)
    {
        $userId = $user->id;

        $ticketStats = [
            'total' => Ticket::where('user_id', $userId)->count(),
            'open' => Ticket::where('user_id', $userId)
                ->whereIn('status', ['open', 'received', 'pending_om', 'in_progress', 'pending_vr'])
                ->count(),
            'in_progress' => Ticket::where('user_id', $userId)
                ->where('status', 'in_progress')
                ->count(),
            'completed' => Ticket::where('user_id', $userId)
                ->whereIn('status', ['completed', 'closed'])
                ->count(),
            'pending_gm' => Ticket::where('user_id', $userId)
                ->where('status', 'pending_gm')
                ->count(),
        ];

        // Recent tickets
        $recentTickets = Ticket::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return [
            'ticketStats' => $ticketStats,
            'recentTickets' => $recentTickets,
        ];
    }
}
