<?php
// app/Http/Controllers/NotificationController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    /**
     * Display all notifications for the authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get notifications with filters
        $query = Notification::with(['ticket', 'ticket.category', 'ticket.priority'])
            ->where('user_id', $user->id);

        // Apply type filter
        if ($request->filled('type') && $request->type !== 'all') {
            if ($request->type === 'unread') {
                $query->where('is_read', false);
            } elseif ($request->type === 'read') {
                $query->where('is_read', true);
            } else {
                $query->where('type', $request->type);
            }
        }

        // Apply date filter
        if ($request->filled('date_filter')) {
            $dateFilter = $request->date_filter;
            switch ($dateFilter) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', today()->subDay());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                    break;
            }
        }

        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Count unread
        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        // Get notification statistics
        $stats = [
            'total' => Notification::where('user_id', $user->id)->count(),
            'unread' => $unreadCount,
            'read' => Notification::where('user_id', $user->id)->where('is_read', true)->count(),
            'today' => Notification::where('user_id', $user->id)
                ->whereDate('created_at', today())
                ->count(),
        ];

        // Get notification types for filter dropdown
        $notificationTypes = [
            'all' => 'All Notifications',
            'unread' => 'Unread',
            'read' => 'Read',
            'info' => 'Info',
            'success' => 'Success',
            'warning' => 'Warning',
            'approval' => 'Approval',
            'assignment' => 'Assignment',
            'comment' => 'Comments',
            'vr_request' => 'VR Requests',
            'closure' => 'Closure',
            'broadcast' => 'Broadcast'
        ];

        return view('notifications.index', compact('notifications', 'stats', 'unreadCount', 'notificationTypes'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);

        $oldValues = $notification->only(['is_read', 'read_at']);

        $notification->update([
            'is_read' => true,
            'read_at' => now()
        ]);

        // Log activity - MENGGUNAKAN ACTION 'updated'
        ActivityLog::create([
            'user_id' => Auth::id(),
            'ticket_id' => $notification->ticket_id,
            'action' => 'updated', // Sesuai dengan yang ada di model: 'updated'
            'description' => 'Notification marked as read: ' . $notification->title,
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode($notification->only(['is_read', 'read_at'])),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);
        }

        // Redirect to ticket if exists
        if ($notification->ticket_id) {
            return redirect()->route('tickets.show', $notification->ticket_id)
                ->with('success', 'Notification marked as read');
        }

        return back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        if ($unreadCount > 0) {
            $oldValues = ['unread_count' => $unreadCount];

            Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);

            // Log activity - MENGGUNAKAN ACTION 'updated'
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'updated', // Sesuai dengan yang ada di model: 'updated'
                'description' => "Marked all {$unreadCount} notifications as read",
                'old_values' => json_encode($oldValues),
                'new_values' => json_encode(['unread_count' => 0]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        }

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read'
            ]);
        }

        return back()->with('success', 'All notifications marked as read');
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);

        // Store data for logging
        $notificationData = $notification->toArray();

        $notification->delete();

        // Log activity - MENGGUNAKAN ACTION 'deleted'
        ActivityLog::create([
            'user_id' => Auth::id(),
            'ticket_id' => $notificationData['ticket_id'] ?? null,
            'action' => 'deleted', // Sesuai dengan yang ada di model: 'deleted'
            'description' => 'Notification deleted: ' . $notificationData['title'],
            'old_values' => json_encode($notificationData),
            'new_values' => null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully'
            ]);
        }

        return back()->with('success', 'Notification deleted');
    }

    /**
     * Clear all notifications
     */
    public function clearAll()
    {
        $user = Auth::user();
        $count = Notification::where('user_id', $user->id)->count();

        if ($count > 0) {
            // Get all notifications for logging
            $notifications = Notification::where('user_id', $user->id)->get();
            $oldValues = ['total_count' => $count];

            Notification::where('user_id', $user->id)->delete();

            // Log activity - MENGGUNAKAN ACTION 'deleted' (bulk delete)
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'deleted', // Sesuai dengan yang ada di model: 'deleted'
                'description' => "Cleared all {$count} notifications",
                'old_values' => json_encode($oldValues),
                'new_values' => json_encode(['total_count' => 0]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        }

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'All notifications cleared'
            ]);
        }

        return back()->with('success', 'All notifications cleared');
    }

    /**
     * Broadcast notification to users (Superadmin only)
     */
    public function broadcast(Request $request)
    {
        // Validate request
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

        // Build user query
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

        // Log activity - MENGGUNAKAN ACTION 'created' (karena membuat banyak notifikasi)
        $oldValues = [
            'broadcast_data' => [
                'recipient_count' => 0,
                'recipients' => []
            ]
        ];

        $newValues = [
            'broadcast_data' => [
                'recipient_count' => $count,
                'recipient_type' => $request->recipient_type,
                'recipient_info' => $recipientInfo,
                'title' => $request->title,
                'message' => $request->message,
                'priority' => $request->priority,
                'notification_ids' => $notificationIds,
                'user_ids' => $users->pluck('id')->toArray()
            ]
        ];

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created', // Sesuai dengan yang ada di model: 'created'
            'description' => "Broadcast sent to {$count} users ({$recipientInfo}): {$request->title}",
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode($newValues),
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
     * AJAX: Get unread notification count
     */
    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * AJAX: Get latest notifications for dropdown
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
                    'time_formatted' => $notification->created_at->format('H:i, d M Y'),
                    'ticket_id' => $notification->ticket_id,
                    'ticket_number' => $notification->ticket ? $notification->ticket->ticket_number : null,
                    'icon' => $this->getNotificationIcon($notification->type),
                ];
            });

        return response()->json($notifications);
    }

    /**
     * AJAX: Mark notification as read
     */
    public function ajaxMarkAsRead(Request $request)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->find($request->id);

        if ($notification) {
            $oldValues = $notification->only(['is_read', 'read_at']);

            $notification->update([
                'is_read' => true,
                'read_at' => now()
            ]);

            // Log activity - MENGGUNAKAN ACTION 'updated'
            ActivityLog::create([
                'user_id' => Auth::id(),
                'ticket_id' => $notification->ticket_id,
                'action' => 'updated', // Sesuai dengan yang ada di model: 'updated'
                'description' => 'Notification marked as read (AJAX): ' . $notification->title,
                'old_values' => json_encode($oldValues),
                'new_values' => json_encode($notification->only(['is_read', 'read_at'])),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    /**
     * Filter notifications by type (deprecated - use index with query params)
     */
    public function filter(Request $request)
    {
        return redirect()->route('notifications.index', $request->query());
    }

    /**
     * Get notification icon based on type
     */
    private function getNotificationIcon($type)
    {
        $icons = [
            'info' => 'fa-info-circle text-info',
            'success' => 'fa-check-circle text-success',
            'warning' => 'fa-exclamation-triangle text-warning',
            'error' => 'fa-times-circle text-danger',
            'danger' => 'fa-times-circle text-danger',
            'approval' => 'fa-clipboard-check text-primary',
            'assignment' => 'fa-user-plus text-success',
            'check' => 'fa-check-double text-primary',
            'rejection' => 'fa-times-circle text-danger',
            'vr_request' => 'fa-file-invoice text-warning',
            'closure' => 'fa-check-circle text-success',
            'comment' => 'fa-comment text-info',
            'broadcast' => 'fa-bullhorn text-warning',
        ];

        return $icons[$type] ?? 'fa-bell text-secondary';
    }

    /**
     * Get notification statistics for a user
     */
    private function getStats($userId)
    {
        return [
            'total' => Notification::where('user_id', $userId)->count(),
            'unread' => Notification::where('user_id', $userId)->where('is_read', false)->count(),
            'read' => Notification::where('user_id', $userId)->where('is_read', true)->count(),
            'today' => Notification::where('user_id', $userId)
                ->whereDate('created_at', today())
                ->count(),
        ];
    }
}
