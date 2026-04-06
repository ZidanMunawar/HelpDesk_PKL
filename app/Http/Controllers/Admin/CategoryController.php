<?php
// app/Http/Controllers/Admin/CategoryController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories
     */
    public function index()
    {
        $categories = Category::withCount('tickets')
            ->latest()
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        // Hanya superadmin yang bisa membuat category
        if (auth()->user()->role !== 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only superadmin can create categories.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
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
            $category = Category::create([
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status,
            ]);


            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'created',
                'description' => 'Created new category: ' . $category->name,
                'old_values' => null,
                'new_values' => $category->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully!',
                'data' => $category
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Category creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, Category $category)
    {
        // Hanya superadmin yang bisa update category
        if (auth()->user()->role !== 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only superadmin can update categories.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')->ignore($category->id)
            ],
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
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
            $oldValues = $category->toArray();

            $category->update([
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status,
            ]);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'description' => 'Updated category: ' . $category->name,
                'old_values' => $oldValues,
                'new_values' => $category->fresh()->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully!',
                'data' => $category
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Category update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified category
     */
    public function destroy(Request $request, Category $category)
    {
        // Hanya superadmin yang bisa delete category
        if (auth()->user()->role !== 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only superadmin can delete categories.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Check if category has tickets
            if ($category->tickets()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category with existing tickets. Please reassign tickets first.'
                ], 422);
            }

            $oldValues = $category->toArray();
            $categoryName = $category->name;

            $category->delete();


            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'description' => 'Deleted category: ' . $categoryName,
                'old_values' => $oldValues,
                'new_values' => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Category deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle category status
     */
    public function toggleStatus(Request $request, Category $category)
    {
        // Hanya superadmin yang bisa toggle status
        if (auth()->user()->role !== 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only superadmin can change category status.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $oldStatus = $category->status;
            $newStatus = $oldStatus === 'active' ? 'inactive' : 'active';

            $oldValues = ['status' => $oldStatus];
            $newValues = ['status' => $newStatus];

            $category->update(['status' => $newStatus]);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'status_changed',
                'description' => 'Changed category status from ' . $oldStatus . ' to ' . $newStatus . ': ' . $category->name,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Category status updated successfully!',
                'data' => $category->fresh()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Category status toggle failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get categories for dropdown (AJAX)
     */
    public function getCategories(Request $request)
    {
        $query = Category::active();

        // Hapus filter department_id

        $categories = $query->orderBy('name')->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
}
