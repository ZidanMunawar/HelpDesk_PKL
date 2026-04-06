<?php
// app/Http/Controllers/ActivityLogController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActivityLogController extends Controller
{
    /**
     * Display activity logs (SuperAdmin only)
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Authorization - HANYA SUPERADMIN
        if ($user->role !== 'superadmin') {
            abort(403, 'Unauthorized access. Superadmin only.');
        }

        // Query logs dengan eager loading
        $query = ActivityLog::with(['user', 'ticket', 'ticket.category', 'ticket.priority'])
            ->orderBy('created_at', 'desc');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by ticket
        if ($request->filled('ticket_id')) {
            $query->where('ticket_id', $request->ticket_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('ticket', function ($q) use ($search) {
                        $q->where('ticket_number', 'like', "%{$search}%")
                            ->orWhere('title', 'like', "%{$search}%");
                    });
            });
        }

        // Get paginated results
        $logs = $query->paginate(25)->withQueryString();

        // Get filter data
        $users = User::whereIn('role', ['superadmin', 'admin_eng', 'om', 'gm', 'technician', 'user'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'role']);

        $tickets = Ticket::select('id', 'ticket_number', 'title')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        // Get unique actions for filter
        $actions = ActivityLog::distinct('action')
            ->whereNotNull('action')
            ->pluck('action')
            ->sort();

        // Statistics
        $stats = [
            'total' => ActivityLog::count(),
            'today' => ActivityLog::whereDate('created_at', today())->count(),
            'last_7_days' => ActivityLog::where('created_at', '>=', now()->subDays(7))->count(),
            'last_30_days' => ActivityLog::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        // Activity by user
        $activityByUser = ActivityLog::select('user_id', DB::raw('count(*) as count'))
            ->with('user')
            ->groupBy('user_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Activity by action
        $activityByAction = ActivityLog::select('action', DB::raw('count(*) as count'))
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->get();

        return view('activity-logs.index', compact(
            'logs',
            'users',
            'tickets',
            'actions',
            'stats',
            'activityByUser',
            'activityByAction'
        ));
    }

    /**
     * Show single activity log details
     */
    public function show($id)
    {
        $user = Auth::user();
        if ($user->role !== 'superadmin') {
            abort(403, 'Unauthorized access. Superadmin only.');
        }

        $log = ActivityLog::with(['user', 'ticket', 'ticket.category', 'ticket.priority'])
            ->findOrFail($id);

        if (request()->ajax()) {
            return view('activity-logs.partials.details', compact('log'));
        }

        return view('activity-logs.show', compact('log'));
    }

    /**
     * Export activity logs to CSV
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'superadmin') {
            abort(403, 'Unauthorized access. Superadmin only.');
        }

        // Build query
        $query = ActivityLog::with(['user', 'ticket'])
            ->orderBy('created_at', 'desc');

        // Apply filters if any
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        $logs = $query->get();

        // Generate CSV
        $filename = 'activity_logs_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Header
        fputcsv($output, [
            'ID',
            'Date & Time',
            'User',
            'Role',
            'Ticket Number',
            'Action',
            'Description',
            'IP Address',
            'User Agent',
            'Old Values',
            'New Values'
        ]);

        // Data rows
        foreach ($logs as $log) {
            fputcsv($output, [
                $log->id,
                $log->created_at->format('Y-m-d H:i:s'),
                $log->user->name ?? 'N/A',
                $log->user->role ?? 'N/A',
                $log->ticket->ticket_number ?? 'N/A',
                $log->action,
                $log->description,
                $log->ip_address ?? 'N/A',
                $log->user_agent ?? 'N/A',
                $log->old_values,
                $log->new_values
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Clear old logs (older than 90 days)
     */
    public function clearOldLogs()
    {
        $user = Auth::user();
        if ($user->role !== 'superadmin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $cutoffDate = now()->subDays(90);
        $deletedCount = ActivityLog::where('created_at', '<', $cutoffDate)->delete();

        return response()->json([
            'success' => true,
            'message' => "Cleared {$deletedCount} old activity logs (older than 90 days)"
        ]);
    }

    /**
     * Delete single log
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->role !== 'superadmin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $log = ActivityLog::findOrFail($id);
        $log->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Activity log deleted successfully'
            ]);
        }

        return redirect()->route('activity-logs.index')
            ->with('success', 'Activity log deleted successfully');
    }

    /**
     * Get activity logs for specific ticket (for modal)
     */
    public function getTicketActivities($ticketId)
    {
        $user = Auth::user();
        if ($user->role !== 'superadmin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $activities = ActivityLog::with(['user'])
            ->where('ticket_id', $ticketId)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'user' => $activity->user->name ?? 'System',
                    'role' => $activity->user->role ?? 'system',
                    'action' => $activity->action,
                    'description' => $activity->description,
                    'time' => $activity->created_at->format('d M Y, H:i'),
                    'relative_time' => $activity->created_at->diffForHumans(),
                    'ip_address' => $activity->ip_address,
                ];
            });

        return response()->json($activities);
    }

    /**
     * Get system statistics for charts
     */
    public function getStatistics()
    {
        $user = Auth::user();
        if ($user->role !== 'superadmin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Last 30 days activity
        $last30Days = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = ActivityLog::whereDate('created_at', $date)->count();
            $last30Days[] = [
                'date' => $date->format('M d'),
                'count' => $count
            ];
        }

        // Top 10 active users
        $topUsers = ActivityLog::select('user_id', DB::raw('count(*) as count'))
            ->with('user:id,name,role')
            ->groupBy('user_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'user' => $item->user ? $item->user->name : 'System',
                    'role' => $item->user ? $item->user->role : 'system',
                    'count' => $item->count
                ];
            });

        // Activity by hour (last 24 hours)
        $activityByHour = [];
        for ($i = 23; $i >= 0; $i--) {
            $hourStart = now()->subHours($i)->startOfHour();
            $hourEnd = now()->subHours($i)->endOfHour();

            $count = ActivityLog::whereBetween('created_at', [$hourStart, $hourEnd])->count();
            $activityByHour[] = [
                'hour' => $hourStart->format('H:00'),
                'count' => $count
            ];
        }

        return response()->json([
            'last_30_days' => $last30Days,
            'top_users' => $topUsers,
            'activity_by_hour' => $activityByHour
        ]);
    }
}
