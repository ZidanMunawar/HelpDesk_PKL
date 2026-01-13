<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('tickets')
            ->with('department')
            ->latest()
            ->get();
        $departments = Department::where('status', 'active')->get();

        return view('admin.categories.index', compact('categories', 'departments'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
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
            $category = Category::create([
                'name' => $request->name,
                'description' => $request->description,
                'department_id' => $request->department_id,
                'status' => $request->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully!',
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($category->id)],
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
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
            $category->update([
                'name' => $request->name,
                'description' => $request->description,
                'department_id' => $request->department_id,
                'status' => $request->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully!',
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Category $category)
    {
        try {
            // Check if category has tickets
            if ($category->tickets()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category with existing tickets. Please reassign tickets first.'
                ], 422);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Request $request, Category $category)
    {
        try {
            $newStatus = $category->status === 'active' ? 'inactive' : 'active';
            $category->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => 'Category status updated successfully!',
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }
}
