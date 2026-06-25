<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Build query untuk SEMUA DATA (buat stats)
        $statsQuery = Notification::where('user_id', $user->id);

        // Apply filters ke statsQuery juga!
        if ($request->filled('type') && $request->type !== 'all') {
            if ($request->type === 'unread') {
                $statsQuery->where('is_read', false);
            } elseif ($request->type === 'read') {
                $statsQuery->where('is_read', true);
            } else {
                $statsQuery->where('type', $request->type);
            }
        }

        if ($request->filled('start_date')) {
            $statsQuery->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $statsQuery->whereDate('created_at', '<=', $request->end_date);
        }

        // Ambil SEMUA notifikasi untuk stats (BUKAN cuma per page)
        $allNotificationsForStats = $statsQuery->get();

        // Hitung stats dari SEMUA data
        $stats = [
            'total' => $allNotificationsForStats->count(),
            'unread' => $allNotificationsForStats->where('is_read', false)->count(),
            'read' => $allNotificationsForStats->where('is_read', true)->count(),
            'today' => $allNotificationsForStats->filter(function ($n) {
                return $n->created_at->isToday();
            })->count(),
        ];

        // ==========================================
        // BUILD QUERY UNTUK TAMPILAN (dengan pagination)
        // ==========================================
        $displayQuery = Notification::with(['ticket', 'ticket.category', 'ticket.priority'])
            ->where('user_id', $user->id);

        // Apply filters yang SAMA
        if ($request->filled('type') && $request->type !== 'all') {
            if ($request->type === 'unread') {
                $displayQuery->where('is_read', false);
            } elseif ($request->type === 'read') {
                $displayQuery->where('is_read', true);
            } else {
                $displayQuery->where('type', $request->type);
            }
        }

        if ($request->filled('start_date')) {
            $displayQuery->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $displayQuery->whereDate('created_at', '<=', $request->end_date);
        }

        $allNotifications = $displayQuery->orderBy('created_at', 'desc')->get();

        // Group by date
        $groupedNotifications = [];
        foreach ($allNotifications as $notification) {
            $dateKey = $notification->created_at->format('Y-m-d');
            if (!isset($groupedNotifications[$dateKey])) {
                $groupedNotifications[$dateKey] = [
                    'date' => $dateKey,
                    'notifications' => []
                ];
            }
            $groupedNotifications[$dateKey]['notifications'][] = $notification;
        }

        krsort($groupedNotifications);
        $groups = array_values($groupedNotifications);

        // Pagination PER GROUP (5 tanggal per halaman)
        $perPage = 5;
        $currentPage = $request->get('page', 1);
        $totalGroups = count($groups);
        $totalPages = ceil($totalGroups / $perPage);
        $currentPage = max(1, min($currentPage, $totalPages ?: 1));
        $offset = ($currentPage - 1) * $perPage;
        $paginatedGroups = array_slice($groups, $offset, $perPage);

        $groupedData = [];
        foreach ($paginatedGroups as $group) {
            $groupedData[$group['date']] = $group['notifications'];
        }

        $totalNotifications = $allNotifications->count();

        $notifications = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedGroups,
            $totalGroups,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
                'pageName' => 'page'
            ]
        );

        $notificationTypes = [
            'all' => 'All Notifications',
            'unread' => 'Unread',
            'read' => 'Read',
            'info' => 'Info',
            'success' => 'Success',
            'warning' => 'Warning',
            'danger' => 'Danger',
            'approval' => 'Approval',
            'assignment' => 'Assignment',
            'comment' => 'Comments',
            'vr_request' => 'VR Requests',
            'closure' => 'Closure',
            'broadcast' => 'Broadcast'
        ];

        $departments = Department::where('status', 'active')->orderBy('name')->get();

        $currentFilters = [
            'type' => $request->type ?? 'all',
            'start_date' => $request->start_date ?? '',
            'end_date' => $request->end_date ?? '',
        ];

        // AJAX response - kirim HTML + STATS (yang dari SEMUA data)
        if ($request->ajax()) {
            return response()->json([
                'html' => view('notifications.partials.notification-list', compact(
                    'notifications',
                    'groupedData',
                    'totalNotifications'
                ))->render(),
                'stats' => $stats  // <-- INI STATS DARI SEMUA DATA!
            ]);
        }

        return view('notifications.index', compact(
            'notifications',
            'groupedData',
            'stats',
            'notificationTypes',
            'departments',
            'currentFilters',
            'totalNotifications'
        ));
    }

    /**
     * Mark notification as read (AJAX)
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);

        if (!$notification->is_read) {
            $oldValues = ['is_read' => false, 'read_at' => null];
            $notification->markAsRead();

            ActivityLog::create([
                'user_id' => Auth::id(),
                'ticket_id' => $notification->ticket_id,
                'action' => 'updated',
                'description' => 'Notification marked as read: ' . $notification->title,
                'old_values' => json_encode($oldValues),
                'new_values' => json_encode(['is_read' => true, 'read_at' => now()]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Notification marked as read']);
        }

        if ($notification->ticket_id) {
            return redirect()->route('tickets.show', $notification->ticket_id);
        }

        return back();
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        if ($unreadCount > 0) {
            Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'updated',
                'description' => "Marked all {$unreadCount} notifications as read",
                'old_values' => json_encode(['unread_count' => $unreadCount]),
                'new_values' => json_encode(['unread_count' => 0]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
        }

        return back()->with('success', 'All notifications marked as read');
    }

    /**
     * Delete a single notification
     */
    public function destroy(Request $request, $id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notificationData = $notification->toArray();
        $notification->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'ticket_id' => $notificationData['ticket_id'] ?? null,
            'action' => 'deleted',
            'description' => 'Notification deleted: ' . $notificationData['title'],
            'old_values' => json_encode($notificationData),
            'new_values' => null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Notification deleted']);
        }

        return back()->with('success', 'Notification deleted');
    }

    /**
     * Clear all notifications
     */
    public function clearAll(Request $request)
    {
        $user = Auth::user();
        $count = Notification::where('user_id', $user->id)->count();

        if ($count > 0) {
            Notification::where('user_id', $user->id)->delete();

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'deleted',
                'description' => "Cleared all {$count} notifications",
                'old_values' => json_encode(['total_count' => $count]),
                'new_values' => json_encode(['total_count' => 0]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'All notifications cleared']);
        }

        return back()->with('success', 'All notifications cleared');
    }

    /**
     * Broadcast notification (SuperAdmin & Admin_eng)
     */
    public function broadcast(Request $request)
    {
        if (!in_array(Auth::user()->role, ['superadmin', 'admin_eng'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only Super Admin and Admin Engineering can send broadcasts.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'recipient_type' => 'required|in:all,role,department',
            'title' => 'required|string|max:100',
            'message' => 'required|string|max:500',
            'priority' => 'required|in:info,success,warning,danger',
            'role' => 'required_if:recipient_type,role|in:user,technician,admin_eng,manager,om,gm,superadmin',
            'department_id' => 'required_if:recipient_type,department|exists:departments,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $query = User::where('status', 'active');
        $recipientInfo = '';

        if ($request->recipient_type === 'role') {
            $query->where('role', $request->role);
            $recipientInfo = "role: {$request->role}";
        } elseif ($request->recipient_type === 'department') {
            $department = Department::find($request->department_id);
            $query->where('department_id', $request->department_id);
            $recipientInfo = "department: {$department->name}";
        } else {
            $recipientInfo = "all users";
        }

        $users = $query->get();
        $count = 0;
        $notificationIds = [];

        foreach ($users as $user) {
            $notification = Notification::create([
                'user_id' => $user->id,
                'title' => $request->title,
                'message' => $request->message,
                'type' => $request->priority,
                'is_read' => false
            ]);
            $notificationIds[] = $notification->id;
            $count++;
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Broadcast',
            'description' => "Broadcast sent to {$count} users ({$recipientInfo}): {$request->title}",
            'old_values' => json_encode(['broadcast_data' => ['recipient_count' => 0]]),
            'new_values' => json_encode([
                'broadcast_data' => [
                    'recipient_count' => $count,
                    'recipient_type' => $request->recipient_type,
                    'recipient_info' => $recipientInfo,
                    'title' => $request->title,
                    'message' => $request->message,
                    'priority' => $request->priority,
                    'notification_ids' => $notificationIds
                ]
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return response()->json([
            'success' => true,
            'recipient_count' => $count,
            'recipient_info' => $recipientInfo,
            'message' => "Broadcast sent to {$count} users ({$recipientInfo})"
        ]);
    }

    /**
     * Export notifications to CSV
     */
    public function export(Request $request)
    {
        $user = Auth::user();

        $query = Notification::with(['ticket'])
            ->where('user_id', $user->id);

        if ($request->filled('type') && $request->type !== 'all') {
            if ($request->type === 'unread') {
                $query->where('is_read', false);
            } elseif ($request->type === 'read') {
                $query->where('is_read', true);
            } else {
                $query->where('type', $request->type);
            }
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $notifications = $query->orderBy('created_at', 'desc')->get();

        $filename = 'notifications_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w+');

        fputcsv($handle, ['ID', 'Title', 'Message', 'Type', 'Status', 'Ticket #', 'Created At', 'Read At']);

        foreach ($notifications as $notification) {
            fputcsv($handle, [
                $notification->id,
                $notification->title,
                $notification->message,
                $notification->type,
                $notification->is_read ? 'Read' : 'Unread',
                $notification->ticket ? $notification->ticket->ticket_number : '-',
                $notification->created_at->format('Y-m-d H:i:s'),
                $notification->read_at ? $notification->read_at->format('Y-m-d H:i:s') : '-',
            ]);
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return response($csvContent, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Get unread count (AJAX)
     */
    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get latest notifications (AJAX)
     */
    public function getLatest()
    {
        $notifications = Notification::with(['ticket'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => \Illuminate\Support\Str::limit($notification->message, 50),
                    'type' => $notification->type,
                    'is_read' => $notification->is_read,
                    'time' => $notification->created_at->diffForHumans(),
                    'ticket_id' => $notification->ticket_id,
                    'ticket_number' => $notification->ticket ? $notification->ticket->ticket_number : null,
                ];
            });

        return response()->json($notifications);
    }
}
