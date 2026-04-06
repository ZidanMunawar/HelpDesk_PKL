<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Priority;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PriorityController extends Controller
{
    /**
     * Display a listing of priorities
     */
    public function index()
    {
        $priorities = Priority::orderBy('level', 'asc')->get();
        return view('admin.priorities.index', compact('priorities'));
    }

    /**
     * Store a newly created priority
     */
    public function store(Request $request)
    {
        // Hanya superadmin yang bisa membuat priority
        if (auth()->user()->role !== 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only superadmin can create priorities.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:priorities,name',
            'color' => 'required|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
            'level' => 'required|integer|in:1,2,3,4,5',
            'status' => 'required|in:active,inactive',
        ], [
            'name.unique' => 'Priority name already exists.',
            'color.regex' => 'Please enter a valid hex color code (e.g., #FF0000).',
            'level.in' => 'Level must be between 1 and 5.',
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
            $priority = Priority::create([
                'name' => $request->name,
                'color' => $request->color,
                'level' => $request->level,
                'status' => $request->status,
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'created',
                'description' => 'Created new priority: ' . $priority->name . ' (Level ' . $priority->level . ')',
                'old_values' => null,
                'new_values' => $priority->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Priority created successfully!',
                'data' => $priority
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create priority: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified priority
     */
    public function update(Request $request, Priority $priority)
    {
        // Hanya superadmin yang bisa update priority
        if (auth()->user()->role !== 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only superadmin can update priorities.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('priorities', 'name')->ignore($priority->id)
            ],
            'color' => 'required|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
            'level' => 'required|integer|in:1,2,3,4,5',
            'status' => 'required|in:active,inactive',
        ], [
            'name.unique' => 'Priority name already exists.',
            'color.regex' => 'Please enter a valid hex color code (e.g., #FF0000).',
            'level.in' => 'Level must be between 1 and 5.',
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
            $oldValues = $priority->toArray();

            $priority->update([
                'name' => $request->name,
                'color' => $request->color,
                'level' => $request->level,
                'status' => $request->status,
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'description' => 'Updated priority: ' . $priority->name,
                'old_values' => $oldValues,
                'new_values' => $priority->fresh()->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Priority updated successfully!',
                'data' => $priority
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update priority: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified priority
     */
    public function destroy(Request $request, Priority $priority)
    {
        // Hanya superadmin yang bisa delete priority
        if (auth()->user()->role !== 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only superadmin can delete priorities.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Check if priority is used in tickets
            if ($priority->tickets()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete priority. It is currently used in ' . $priority->tickets()->count() . ' ticket(s).'
                ], 422);
            }

            $oldValues = $priority->toArray();
            $priorityName = $priority->name;

            $priority->delete();

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'description' => 'Deleted priority: ' . $priorityName,
                'old_values' => $oldValues,
                'new_values' => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Priority deleted successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete priority: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle priority status
     */
    public function toggleStatus(Request $request, Priority $priority)
    {
        // Hanya superadmin yang bisa toggle status
        if (auth()->user()->role !== 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only superadmin can change priority status.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $oldStatus = $priority->status;
            $newStatus = $oldStatus === 'active' ? 'inactive' : 'active';

            $oldValues = ['status' => $oldStatus];
            $newValues = ['status' => $newStatus];

            $priority->update(['status' => $newStatus]);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'status_changed',
                'description' => 'Changed priority status from ' . $oldStatus . ' to ' . $newStatus . ': ' . $priority->name,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Priority status updated successfully!',
                'data' => $priority->fresh()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }
}
