<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    /**
     * Display a listing of locations
     */
    public function index()
    {
        $locations = Location::latest()->get();
        return view('admin.locations.index', compact('locations'));
    }

    // app/Http/Controllers/Admin/LocationController.php - bagian store()
    public function store(Request $request)
    {
        // Debug untuk lihat data yang diterima
        \Log::info('Location Store Data:', $request->all());

        // Hanya superadmin yang bisa membuat location
        if (auth()->user()->role !== 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only superadmin can create locations.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:locations,name',
            'location_type' => 'required|in:room,floor,department,facility,area',
            'floor_number' => [
                'nullable',
                Rule::in(['GF', 'M', '3', '3A', '5', '6', '7', '8', '9'])
            ],
            'hotel' => 'required|in:harris,pop',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ], [
            'name.unique' => 'Location name already exists.',
            'floor_number.in' => 'Invalid floor number. Please select from available floors.',
            'hotel.in' => 'Invalid hotel selection.',
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $location = Location::create([
                'name' => $request->name,
                'location_type' => $request->location_type,
                'floor_number' => $request->floor_number ?: null,
                'hotel' => $request->hotel,
                'description' => $request->description ?: null,
                'status' => $request->status,
            ]);

            \Log::info('Location created:', $location->toArray());

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'created',
                'description' => 'Created new location: ' . $location->name . ' (' . $location->hotel . ')',
                'old_values' => null,
                'new_values' => $location->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Location created successfully!',
                'data' => $location
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Location creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create location: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified location
     */
    public function update(Request $request, Location $location)
    {
        // Hanya superadmin yang bisa update location
        if (auth()->user()->role !== 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only superadmin can update locations.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('locations', 'name')->ignore($location->id)
            ],
            'location_type' => 'required|in:room,floor,department,facility,area',
            'floor_number' => [
                'nullable',
                Rule::in(['GF', 'M', '3', '3A', '5', '6', '7', '8', '9'])
            ],
            'hotel' => 'required|in:harris,pop',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ], [
            'name.unique' => 'Location name already exists.',
            'floor_number.in' => 'Invalid floor number. Please select from available floors.',
            'hotel.in' => 'Invalid hotel selection.',
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
            $oldValues = $location->toArray();

            $location->update([
                'name' => $request->name,
                'location_type' => $request->location_type,
                'floor_number' => $request->floor_number,
                'hotel' => $request->hotel,
                'description' => $request->description,
                'status' => $request->status,
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'description' => 'Updated location: ' . $location->name . ' (' . $location->hotel . ')',
                'old_values' => $oldValues,
                'new_values' => $location->fresh()->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Location updated successfully!',
                'data' => $location
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Location update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update location: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified location
     */
    public function destroy(Request $request, Location $location)
    {
        // Hanya superadmin yang bisa delete location
        if (auth()->user()->role !== 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only superadmin can delete locations.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Check if location is used in tickets
            if ($location->tickets()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete location. It is currently used in ' . $location->tickets()->count() . ' ticket(s).'
                ], 422);
            }

            $oldValues = $location->toArray();
            $locationName = $location->name;
            $hotel = $location->hotel;

            $location->delete();

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'description' => 'Deleted location: ' . $locationName . ' (' . $hotel . ')',
                'old_values' => $oldValues,
                'new_values' => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Location deleted successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Location deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete location: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle location status
     */
    public function toggleStatus(Request $request, Location $location)
    {
        // Hanya superadmin yang bisa toggle status
        if (auth()->user()->role !== 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only superadmin can change location status.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $oldStatus = $location->status;
            $newStatus = $oldStatus === 'active' ? 'inactive' : 'active';

            $oldValues = ['status' => $oldStatus];
            $newValues = ['status' => $newStatus];

            $location->update(['status' => $newStatus]);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'status_changed',
                'description' => 'Changed location status from ' . $oldStatus . ' to ' . $newStatus . ': ' . $location->name . ' (' . $location->hotel . ')',
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Location status updated successfully!',
                'data' => $location->fresh()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Location status toggle failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get locations for dropdown (AJAX)
     */
    public function getLocations(Request $request)
    {
        $query = Location::active();

        if ($request->has('type')) {
            $query->where('location_type', $request->type);
        }

        if ($request->has('floor')) {
            $query->where('floor_number', $request->floor);
        }

        if ($request->has('hotel')) {
            $query->where('hotel', $request->hotel);
        }

        $locations = $query->orderBy('name')->get(['id', 'name', 'location_type', 'floor_number', 'hotel']);

        return response()->json([
            'success' => true,
            'data' => $locations
        ]);
    }
}
