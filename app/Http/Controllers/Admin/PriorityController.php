<?php
// app/Http/Controllers/Admin/PriorityController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Priority;
use Illuminate\Http\Request;

class PriorityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $priorities = Priority::withCount('tickets')->orderBy('level', 'asc')->get();
        return view('admin.priorities.index', compact('priorities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7', // Hex color code
            'level' => 'required|integer|min:1|unique:priorities,level',
            'status' => 'required|in:active,inactive',
        ]);

        try {
            Priority::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Priority created successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create priority: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Priority $priority)
    {
        $priority->load('tickets');
        return response()->json($priority);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Priority $priority)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
            'level' => 'required|integer|min:1|unique:priorities,level,' . $priority->id,
            'status' => 'required|in:active,inactive',
        ]);

        try {
            $priority->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Priority updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update priority: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Priority $priority)
    {
        try {
            // Check if priority has tickets
            if ($priority->tickets()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete priority with existing tickets!'
                ], 422);
            }

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

    /**
     * Toggle priority status
     */
    public function toggleStatus(Priority $priority)
    {
        try {
            $priority->update([
                'status' => $priority->status === 'active' ? 'inactive' : 'active'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Priority status updated successfully!',
                'status' => $priority->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }
}
