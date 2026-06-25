<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MyDepartmentController extends Controller
{
    /**
     * Display department overview for manager
     */
    public function index()
    {
        $user = Auth::user();

        // CEK: hanya manager yang boleh akses
        if ($user->role !== 'manager') {
            abort(403, 'Unauthorized access. Only managers can view this page.');
        }

        // CEK: manager harus memiliki department_id
        if (!$user->department_id) {
            return view('manager.department.not-linked');
        }

        $department = Department::find($user->department_id);

        if (!$department) {
            return view('manager.department.not-linked');
        }

        // CEK: pastikan benar-benar manager dari department ini
        if ($department->manager_id !== $user->id) {
            return view('manager.department.not-linked');
        }

        // Ambil semua user di department ini dengan count tickets
        $departmentUsers = User::where('department_id', $department->id)
            ->where('status', 'active')
            ->withCount(['tickets'])
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        // Hitung statistik ticket (simplified untuk card)
        $ticketStats = $this->getTicketStats($department->id);

        // Hitung statistik pengirim ticket terbanyak untuk chart
        $topSenders = $this->getTopTicketSenders($department->id);

        // Ambil 5 ticket terbaru
        $recentTickets = Ticket::where('department_id', $department->id)
            ->with(['user', 'priority'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Detail info untuk tooltip
        $detailedStats = $this->getDetailedStats($department->id);

        return view('manager.department.index', compact(
            'department',
            'ticketStats',
            'departmentUsers',
            'recentTickets',
            'topSenders',
            'detailedStats'
        ));
    }

    /**
     * Get ticket statistics (simplified for cards)
     */
    private function getTicketStats($departmentId)
    {
        $tickets = Ticket::where('department_id', $departmentId);

        return [
            'total' => $tickets->count(),
            'in_progress' => (clone $tickets)->whereIn('status', ['received', 'in_progress', 'pending_om', 'pending_vr', 'ready_for_closure'])->count(),
            'completed' => (clone $tickets)->where('status', 'completed')->count(),
            'canceled' => (clone $tickets)->where('status', 'cancelled')->count(),
            'closed' => (clone $tickets)->where('status', 'closed')->count(),
        ];
    }

    /**
     * Get detailed statistics for tooltip
     */
    private function getDetailedStats($departmentId)
    {
        $tickets = Ticket::where('department_id', $departmentId);

        return [
            'open' => (clone $tickets)->where('status', 'open')->count(),
            'received' => (clone $tickets)->where('status', 'received')->count(),
            'in_progress' => (clone $tickets)->where('status', 'in_progress')->count(),
            'pending_om' => (clone $tickets)->where('status', 'pending_om')->count(),
            'pending_vr' => (clone $tickets)->where('status', 'pending_vr')->count(),
            'pending_gm' => (clone $tickets)->where('status', 'pending_gm')->count(),
            'ready_for_closure' => (clone $tickets)->where('status', 'ready_for_closure')->count(),
            'completed' => (clone $tickets)->where('status', 'completed')->count(),
            'closed' => (clone $tickets)->where('status', 'closed')->count(),
            'cancelled' => (clone $tickets)->where('status', 'cancelled')->count(),
            'overdue' => (clone $tickets)
                ->where('due_date', '<', now())
                ->whereNotIn('status', ['closed', 'cancelled', 'completed'])
                ->count(),
        ];
    }

    /**
     * Get top ticket senders for chart
     */
    private function getTopTicketSenders($departmentId)
    {
        $topSenders = Ticket::where('department_id', $departmentId)
            ->select('user_id', DB::raw('count(*) as total'))
            ->with('user')
            ->groupBy('user_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->user ? $item->user->name : 'Unknown',
                    'total' => $item->total,
                ];
            });

        return $topSenders;
    }

    /**
     * Update department name
     */
    public function updateName(Request $request)
    {
        $request->validate([
            'department_name' => 'required|string|max:255',
        ]);

        $user = Auth::user();

        if ($user->role !== 'manager') {
            return response()->json([
                'success' => false,
                'message' => 'Only managers can perform this action'
            ], 403);
        }

        if (!$user->department_id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not linked to any department'
            ], 403);
        }

        $department = Department::find($user->department_id);

        if (!$department || $department->manager_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found or you are not the manager'
            ], 404);
        }

        try {
            $department->name = $request->department_name;
            $department->save();

            return response()->json([
                'success' => true,
                'message' => 'Department name updated successfully',
                'new_name' => $department->name
            ]);

        } catch (\Exception $e) {
            Log::error('Update department name error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update department name'
            ], 500);
        }
    }
}
