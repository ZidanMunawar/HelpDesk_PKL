<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments
     */
    public function index()
    {
        $departments = Department::with(['manager', 'users'])
            ->withCount([
                'users as active_users_count' => function ($query) {
                    $query->where('status', 'active');
                }
            ])
            ->latest()
            ->get();

        // Hanya ambil user dengan role 'manager' yang aktif
        $managers = User::where('role', 'manager')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.departments.index', compact('departments', 'managers'));
    }

    /**
     * Store a newly created department
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:departments,name',
            'manager_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'has_manager_access' => 'sometimes|boolean', // <-- TAMBAHKAN
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $department = Department::create([
                'name' => $request->name,
                'manager_id' => $request->manager_id,
                'description' => $request->description,
                'status' => $request->status,
                'has_manager_access' => $request->has_manager_access ?? false, // <-- TAMBAHKAN
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'created',
                'description' => 'Created new department: ' . $department->name,
                'old_values' => null,
                'new_values' => $department->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Department created successfully!',
                'data' => $department->load('manager')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create department: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified department
     */
    public function update(Request $request, Department $department)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments')->ignore($department->id)
            ],
            'manager_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'has_manager_access' => 'sometimes|boolean', // <-- TAMBAHKAN
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $oldValues = $department->toArray();

            $department->update([
                'name' => $request->name,
                'manager_id' => $request->manager_id,
                'description' => $request->description,
                'status' => $request->status,
                'has_manager_access' => $request->has_manager_access ?? false, // <-- TAMBAHKAN
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'description' => 'Updated department: ' . $department->name,
                'old_values' => $oldValues,
                'new_values' => $department->fresh()->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Department updated successfully!',
                'data' => $department->load('manager')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update department: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified department
     */
    public function destroy(Request $request, Department $department)
    {
        DB::beginTransaction();
        try {
            // Check if department has users
            if ($department->users()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete department with existing users. Please reassign users first.'
                ], 422);
            }

            $oldValues = $department->toArray();
            $departmentName = $department->name;

            $department->delete();

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'description' => 'Deleted department: ' . $departmentName,
                'old_values' => $oldValues,
                'new_values' => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Department deleted successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete department: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle department status
     */
    public function toggleStatus(Request $request, Department $department)
    {
        DB::beginTransaction();
        try {
            $oldStatus = $department->status;
            $newStatus = $department->status === 'active' ? 'inactive' : 'active';

            $oldValues = ['status' => $oldStatus];
            $newValues = ['status' => $newStatus];

            $department->update(['status' => $newStatus]);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'ticket_id' => null,
                'action' => 'status_changed',
                'description' => 'Changed department status from ' . $oldStatus . ' to ' . $newStatus . ': ' . $department->name,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Department status updated successfully!',
                'data' => $department->fresh()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle manager access for department
     */
    public function toggleManagerAccess(Request $request, Department $department)
    {
        DB::beginTransaction();
        try {
            $oldAccess = $department->has_manager_access;
            $newAccess = !$oldAccess;

            $oldValues = ['has_manager_access' => $oldAccess];
            $newValues = ['has_manager_access' => $newAccess];

            $department->update(['has_manager_access' => $newAccess]);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'ticket_id' => null,
                'action' => 'manager_access_changed',
                'description' => 'Changed manager access for department ' . $department->name . ' from ' . ($oldAccess ? 'Yes' : 'No') . ' to ' . ($newAccess ? 'Yes' : 'No'),
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Manager access updated successfully!',
                'data' => $department->fresh()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update manager access: ' . $e->getMessage()
            ], 500);
        }
    }
}
