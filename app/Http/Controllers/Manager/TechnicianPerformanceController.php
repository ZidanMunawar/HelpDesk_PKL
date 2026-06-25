<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TechnicianPerformanceController extends Controller
{
    /**
     * Display list of technicians (Index page)
     */
    public function index()
    {
        $user = Auth::user();
        $technicians = collect();

        // SUPERADMIN, ADMIN_ENG, OM, GM - bisa lihat semua teknisi
        if (in_array($user->role, ['superadmin', 'admin_eng', 'om', 'gm'])) {
            $technicians = $this->getTechniciansWithStats();
        }
        // MANAGER - cek has_manager_access dulu, kalau true bisa lihat SEMUA TEKNISI
        elseif ($user->role === 'manager') {
            $department = Department::find($user->department_id);

            if (!$department || !$department->has_manager_access) {
                abort(403, 'Your department does not have access to this feature.');
            }

            $technicians = $this->getTechniciansWithStats();
        }
        // TECHNICIAN - redirect ke detail sendiri
        elseif ($user->role === 'technician') {
            return redirect()->route('technician-performance.show', $user->id);
        } else {
            abort(403, 'Unauthorized access.');
        }

        // Hitung summary stats
        $totalTechs = $technicians->count();
        $totalTicketsAll = $technicians->sum('total_tickets');
        $totalCompletedAll = $technicians->sum('completed_count') + $technicians->sum('closed_count');
        $avgRateAll = $totalTicketsAll > 0 ? round(($totalCompletedAll / $totalTicketsAll) * 100, 1) : 0;
        $avgTimeAll = $technicians->avg('avg_completion_time');

        return view('manager.technician-performance.index', compact(
            'technicians',
            'totalTechs',
            'totalTicketsAll',
            'totalCompletedAll',
            'avgRateAll',
            'avgTimeAll'
        ));
    }

    /**
     * Get all technicians with their statistics
     */
    private function getTechniciansWithStats()
    {
        $technicians = User::where('role', 'technician')
            ->where('status', 'active')
            ->with('department')
            ->get();

        foreach ($technicians as $tech) {
            // Count tickets assigned to this technician
            $totalTickets = Ticket::where('assigned_to', $tech->id)->count();
            $completedCount = Ticket::where('assigned_to', $tech->id)->where('status', 'completed')->count();
            $closedCount = Ticket::where('assigned_to', $tech->id)->where('status', 'closed')->count();
            $inProgressCount = Ticket::where('assigned_to', $tech->id)
                ->whereIn('status', ['received', 'in_progress', 'pending_om', 'pending_vr', 'ready_for_closure'])
                ->count();
            $cancelledCount = Ticket::where('assigned_to', $tech->id)->where('status', 'cancelled')->count();

            $tech->total_tickets = $totalTickets;
            $tech->completed_count = $completedCount;
            $tech->closed_count = $closedCount;
            $tech->in_progress_count = $inProgressCount;
            $tech->cancelled_count = $cancelledCount;
            $tech->completion_rate = $totalTickets > 0
                ? round((($completedCount + $closedCount) / $totalTickets) * 100, 1)
                : 0;

            // Average completion time in hours
            $avgTime = Ticket::where('assigned_to', $tech->id)
                ->whereNotNull('assigned_to')
                ->whereNotNull('resolved_at')
                ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_time'))
                ->value('avg_time');
            $tech->avg_completion_time = $avgTime ? round($avgTime, 1) : 0;
        }

        return $technicians;
    }

    /**
     * Show detailed statistics for a specific technician
     */
    public function show($id)
    {
        $user = Auth::user();
        $technician = User::where('role', 'technician')->findOrFail($id);

        // CEK AKSES
        $canAccess = false;

        if (in_array($user->role, ['superadmin', 'admin_eng', 'om', 'gm'])) {
            $canAccess = true;
        } elseif ($user->role === 'manager') {
            $department = Department::find($user->department_id);
            if ($department && $department->has_manager_access) {
                $canAccess = true;
            }
        } elseif ($user->role === 'technician' && $user->id == $id) {
            $canAccess = true;
        }

        if (!$canAccess) {
            abort(403, 'You do not have permission to view this technician\'s statistics.');
        }

        // Ambil semua ticket teknisi
        $tickets = Ticket::where('assigned_to', $technician->id)
            ->with(['user', 'priority', 'category', 'department'])
            ->orderBy('created_at', 'desc')
            ->get();

        // STATS CARDS
        $totalTickets = $tickets->count();
        $completedCount = $tickets->where('status', 'completed')->count();
        $closedCount = $tickets->where('status', 'closed')->count();
        $inProgressCount = $tickets->whereIn('status', ['received', 'in_progress', 'pending_om', 'pending_vr', 'ready_for_closure'])->count();
        $cancelledCount = $tickets->where('status', 'cancelled')->count();
        $overdueCount = $tickets->where('due_date', '<', now())
            ->whereNotIn('status', ['closed', 'cancelled', 'completed'])
            ->count();

        $successCount = $completedCount + $closedCount;
        $completionRate = $totalTickets > 0 ? round(($successCount / $totalTickets) * 100, 1) : 0;

        // Rata-rata waktu pengerjaan (jam)
        $resolvedTickets = $tickets->whereNotNull('resolved_at');
        $totalHours = 0;
        foreach ($resolvedTickets as $ticket) {
            $totalHours += $ticket->created_at->diffInHours($ticket->resolved_at);
        }
        $avgCompletionTime = $resolvedTickets->count() > 0 ? round($totalHours / $resolvedTickets->count(), 1) : 0;

        // WEEKLY PERFORMANCE (last 8 weeks)
        $weeklyData = [];
        for ($i = 7; $i >= 0; $i--) {
            $weekStart = now()->startOfWeek()->subWeeks($i);
            $weekEnd = $weekStart->copy()->endOfWeek();
            $weekLabel = 'W' . $weekStart->format('W');

            $weekTickets = $tickets->filter(function ($ticket) use ($weekStart, $weekEnd) {
                return $ticket->created_at >= $weekStart && $ticket->created_at <= $weekEnd;
            });

            $weeklyData[] = [
                'week' => $weekLabel,
                'completed' => $weekTickets->where('status', 'completed')->count() + $weekTickets->where('status', 'closed')->count(),
                'overdue' => $weekTickets->filter(function ($t) use ($weekEnd) {
                    return $t->due_date && $t->due_date < $weekEnd && !in_array($t->status, ['closed', 'cancelled', 'completed']);
                })->count(),
                'in_progress' => $weekTickets->whereIn('status', ['received', 'in_progress', 'pending_om', 'pending_vr', 'ready_for_closure'])->count(),
            ];
        }

        // CATEGORY BREAKDOWN
        $categoryBreakdown = [];
        foreach ($tickets as $ticket) {
            $catName = $ticket->category->name ?? 'Unknown';
            if (!isset($categoryBreakdown[$catName])) {
                $categoryBreakdown[$catName] = 0;
            }
            $categoryBreakdown[$catName]++;
        }
        arsort($categoryBreakdown);

        // STATUS BREAKDOWN
        $statusBreakdown = [
            'Completed' => $completedCount + $closedCount,
            'In Progress' => $inProgressCount,
            'Overdue' => $overdueCount,
            'Cancelled' => $cancelledCount,
        ];

        // RECENT TICKETS (last 10)
        $recentTickets = $tickets->take(10);

        // MONTHLY TREND (last 6 months)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = now()->startOfMonth()->subMonths($i);
            $monthLabel = $monthStart->format('M Y');

            $monthTickets = $tickets->filter(function ($ticket) use ($monthStart) {
                return $ticket->created_at->format('Y-m') == $monthStart->format('Y-m');
            });

            $monthlyTrend[] = [
                'month' => $monthLabel,
                'completed' => $monthTickets->where('status', 'completed')->count() + $monthTickets->where('status', 'closed')->count(),
                'total' => $monthTickets->count(),
            ];
        }

        return view('manager.technician-performance.show', compact(
            'technician',
            'totalTickets',
            'successCount',
            'completionRate',
            'avgCompletionTime',
            'inProgressCount',
            'overdueCount',
            'cancelledCount',
            'weeklyData',
            'categoryBreakdown',
            'statusBreakdown',
            'recentTickets',
            'monthlyTrend'
        ));
    }
}
