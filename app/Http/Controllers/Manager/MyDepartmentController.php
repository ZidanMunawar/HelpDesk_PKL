<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // Ambil semua user di department ini (bukan cuma teknisi)
        $departmentUsers = User::where('department_id', $department->id)
            ->where('status', 'active')
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        // Hitung statistik ticket
        $ticketStats = $this->getTicketStats($department->id);

        // Ambil 5 ticket terbaru
        $recentTickets = Ticket::where('department_id', $department->id)
            ->with(['user', 'priority'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('manager.department.index', compact(
            'department',
            'ticketStats',
            'departmentUsers',
            'recentTickets'
        ));
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

        // CEK: hanya manager
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
            $oldName = $department->name;
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

    /**
     * Get ticket statistics
     */
    private function getTicketStats($departmentId)
    {
        $tickets = Ticket::where('department_id', $departmentId);

        return [
            'total' => $tickets->count(),
            'open' => (clone $tickets)->where('status', 'open')->count(),
            'in_progress' => (clone $tickets)->whereIn('status', ['in_progress', 'pending_om', 'pending_vr'])->count(),
            'completed' => (clone $tickets)->where('status', 'completed')->count(),
            'pending_gm' => (clone $tickets)->where('status', 'pending_gm')->count(),
            'closed' => (clone $tickets)->whereIn('status', ['closed', 'cancelled'])->count(),
            'overdue' => (clone $tickets)
                ->where('due_date', '<', now())
                ->whereNotIn('status', ['closed', 'cancelled', 'completed'])
                ->count(),
        ];
    }
}
