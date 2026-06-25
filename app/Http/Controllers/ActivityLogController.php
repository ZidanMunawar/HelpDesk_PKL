<?php

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
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'superadmin') {
            abort(403, 'Unauthorized access. Superadmin only.');
        }

        $query = ActivityLog::with(['user', 'ticket', 'ticket.category', 'ticket.priority'])
            ->orderBy('created_at', 'desc');

        // HANYA FILTER JIKA ADA PARAMETER
        if ($request->filled('user_id') && $request->user_id !== 'all') {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action') && $request->action !== 'all') {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

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

        $logs = $query->paginate(15);
        $logs->appends($request->except('page'));

        // AMBIL SEMUA USER ACTIVE (BUKAN YANG PUNYA LOG AJA)
        $users = User::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'role']);

        $actions = ActivityLog::distinct('action')
            ->whereNotNull('action')
            ->pluck('action')
            ->sort();

        $currentFilters = [
            'user_id' => $request->user_id ?? 'all',
            'action' => $request->action ?? 'all',
            'date_from' => $request->date_from ?? '',
            'date_to' => $request->date_to ?? '',
            'search' => $request->search ?? '',
        ];

        if ($request->ajax()) {
            return response()->json([
                'html' => view('activity-logs.partials.logs-list', compact('logs'))->render(),
                'pagination' => view('activity-logs.partials.pagination', compact('logs'))->render(),
                'total' => $logs->total(),
            ]);
        }

        return view('activity-logs.index', compact('logs', 'users', 'actions', 'currentFilters'));
    }

    /**
     * Show single activity log details (AJAX)
     */
    public function show($id)
    {
        $user = Auth::user();
        if ($user->role !== 'superadmin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $log = ActivityLog::with(['user', 'ticket', 'ticket.category', 'ticket.priority'])
            ->findOrFail($id);

        if (request()->ajax()) {
            return view('activity-logs.partials.details', compact('log'));
        }

        return view('activity-logs.show', compact('log'));
    }

    /**
     * Export activity logs to CSV (sesuai filter)
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'superadmin') {
            abort(403, 'Unauthorized access. Superadmin only.');
        }

        // Build query dengan filter yang SAMA seperti di index
        $query = ActivityLog::with(['user', 'ticket'])
            ->orderBy('created_at', 'desc');

        // Filter by user
        if ($request->filled('user_id') && $request->user_id !== 'all') {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->filled('action') && $request->action !== 'all') {
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

        $logs = $query->get();

        // Generate CSV
        $filename = 'activity_logs_' . date('Ymd_His') . '.csv';

        // Buat response CSV manual
        $csvContent = $this->generateCSV($logs);

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Generate CSV content
     */
    private function generateCSV($logs)
    {
        // Start output buffering
        ob_start();

        // Create output stream
        $output = fopen('php://output', 'w');

        // Add UTF-8 BOM for Excel compatibility
        fwrite($output, "\xEF\xBB\xBF");

        // Header - rapih
        fputcsv($output, [
            'ID',
            'Tanggal & Waktu',
            'User',
            'Role',
            'Ticket Number',
            'Action',
            'Description',
            'IP Address',
            'User Agent'
        ]);

        // Data rows
        foreach ($logs as $log) {
            fputcsv($output, [
                $log->id,
                $log->created_at->format('Y-m-d H:i:s'),
                $log->user->name ?? 'System',
                $log->user->role ?? 'system',
                $log->ticket->ticket_number ?? '-',
                $log->action,
                $log->description,
                $log->ip_address ?? '-',
                $log->user_agent ?? '-'
            ]);
        }

        fclose($output);

        // Get output buffer content
        $csvContent = ob_get_contents();
        ob_end_clean();

        return $csvContent;
    }

    /**
     * Get statistics for settings page
     */
    public function getStatistics(Request $request)
    {
        $user = Auth::user();

        // Untuk settings page, semua user bisa lihat statistik log mereka sendiri
        // Tapi untuk superadmin bisa lihat semua

        $query = ActivityLog::query();

        if ($user->role !== 'superadmin') {
            $query->where('user_id', $user->id);
        }

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%");
            });
        }

        $stats = [
            'total' => (clone $query)->count(),
            'today' => (clone $query)->whereDate('created_at', today())->count(),
            'week' => (clone $query)->where('created_at', '>=', now()->subDays(7))->count(),
            'month' => (clone $query)->where('created_at', '>=', now()->subDays(30))->count(),
        ];

        return response()->json($stats);
    }
}
