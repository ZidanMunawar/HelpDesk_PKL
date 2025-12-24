<?php
// app/Http/Controllers/Admin/PriorityController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Priority;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PriorityController extends Controller
{
    public function index()
    {
        $priorities = Priority::orderByLevel()->get();
        return view('admin.priorities.index', compact('priorities'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
            'level' => 'required|integer|min:1|max:10',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $priority = Priority::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Priority created successfully!',
                'data' => $priority
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create priority: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Priority $priority)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
            'level' => 'required|integer|min:1|max:10',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $priority->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Priority updated successfully!',
                'data' => $priority
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update priority: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Priority $priority)
    {
        try {
            $priority->delete();

            return response()->json([
                'success' => true,
                'message' => 'Priority deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete priority: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Request $request, Priority $priority)
    {
        try {
            $newStatus = $priority->status === 'active' ? 'inactive' : 'active';
            $priority->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => 'Priority status updated successfully!',
                'data' => $priority
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }
}
