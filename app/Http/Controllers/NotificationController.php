<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Display all notifications for the authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get notifications
        $notifications = Notification::with(['ticket', 'ticket.category', 'ticket.priority'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Mark all as read if requested
        if ($request->has('mark_all_read') && $request->boolean('mark_all_read')) {
            Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);

            return redirect()->route('notifications.index')->with('success', 'All notifications marked as read');
        }

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
            'this_week' => Notification::where('user_id', $user->id)
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
        ];

        return view('notifications.index', compact('notifications', 'stats', 'unreadCount'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);

        $notification->update([
            'is_read' => true,
            'read_at' => now()
        ]);

        // Redirect to ticket if exists
        if ($notification->ticket_id) {
            return redirect()->route('tickets.show', $notification->ticket_id);
        }

        return back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return back()->with('success', 'All notifications marked as read');
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->delete();

        return back()->with('success', 'Notification deleted');
    }

    /**
     * Clear all notifications
     */
    public function clearAll()
    {
        Notification::where('user_id', Auth::id())->delete();

        return back()->with('success', 'All notifications cleared');
    }

    /**
     * Filter notifications by type
     */
    public function filter(Request $request)
    {
        $user = Auth::user();
        $type = $request->get('type', 'all');

        $query = Notification::with(['ticket'])
            ->where('user_id', $user->id);

        // Apply filters
        if ($type !== 'all') {
            if ($type === 'unread') {
                $query->where('is_read', false);
            } elseif ($type === 'read') {
                $query->where('is_read', true);
            } else {
                $query->where('type', $type);
            }
        }

        // Apply date filter
        if ($request->has('date_filter')) {
            $dateFilter = $request->get('date_filter');
            $today = today();

            switch ($dateFilter) {
                case 'today':
                    $query->whereDate('created_at', $today);
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', $today->subDay());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                    break;
            }
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);
        $stats = $this->getStats($user->id);

        return view('notifications.index', compact('notifications', 'stats'));
    }

    /**
     * Get notification statistics
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
     * AJAX: Get latest notifications
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

    /**
     * AJAX: Mark notification as read
     */
    public function ajaxMarkAsRead(Request $request)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->find($request->id);

        if ($notification) {
            $notification->update(['is_read' => true, 'read_at' => now()]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }
}
