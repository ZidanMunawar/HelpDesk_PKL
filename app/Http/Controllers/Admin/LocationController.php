<?php
// app/Http/Controllers/Admin/LocationController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LocationController extends Controller
{
    public function __construct()
    {
        // Hanya superadmin yang bisa mengakses semua method
        $this->middleware('superadmin')->except(['index', 'getLocations']);
        // Untuk index dan getLocations, bisa diakses oleh admin juga
    }

    public function index()
    {
        $locations = Location::latest()->get();
        return view('admin.locations.index', compact('locations'));
    }

    public function store(Request $request)
    {
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
                Rule::in(['GF', 'M', '3A', '4', '5', '6', '7', '8', '9'])
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

        try {
            $location = Location::create([
                'name' => $request->name,
                'location_type' => $request->location_type,
                'floor_number' => $request->floor_number,
                'hotel' => $request->hotel,
                'description' => $request->description,
                'status' => $request->status,
            ]);

            // Log ActivityLog
            ActivityLog('location_created')
                ->causedBy(auth()->user())
                ->withProperties([
                    'location_id' => $location->id,
                    'name' => $location->name,
                    'type' => $location->location_type,
                    'hotel' => $location->hotel,
                ])
                ->log('Location created: ' . $location->name);

            return response()->json([
                'success' => true,
                'message' => 'Location created successfully!',
                'data' => $location
            ]);
        } catch (\Exception $e) {
            \Log::error('Location creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create location: ' . $e->getMessage()
            ], 500);
        }
    }

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
                Rule::in(['GF', 'M', '3A', '4', '5', '6', '7', '8', '9'])
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

        try {
            // Simpan data lama untuk log
            $oldData = $location->toArray();

            $location->update([
                'name' => $request->name,
                'location_type' => $request->location_type,
                'floor_number' => $request->floor_number,
                'hotel' => $request->hotel,
                'description' => $request->description,
                'status' => $request->status,
            ]);

            // Log ActivityLog
            ActivityLogLog('location_updated')
                ->causedBy(auth()->user())
                ->withProperties([
                    'location_id' => $location->id,
                    'old_data' => $oldData,
                    'new_data' => $location->toArray(),
                ])
                ->log('Location updated: ' . $location->name);

            return response()->json([
                'success' => true,
                'message' => 'Location updated successfully!',
                'data' => $location
            ]);
        } catch (\Exception $e) {
            \Log::error('Location update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update location: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Location $location)
    {
        // Hanya superadmin yang bisa delete location
        if (auth()->user()->role !== 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only superadmin can delete locations.'
            ], 403);
        }

        try {
            // Check if location is used in tickets
            if ($location->tickets()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete location. It is currently used in ' . $location->tickets()->count() . ' ticket(s).'
                ], 422);
            }

            $locationName = $location->name;
            $location->delete();

            // Log ActivityLog
            ActivityLog('location_deleted')
                ->causedBy(auth()->user())
                ->withProperties([
                    'location_name' => $locationName,
                    'deleted_at' => now(),
                ])
                ->log('Location deleted: ' . $locationName);

            return response()->json([
                'success' => true,
                'message' => 'Location deleted successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Location deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete location: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Request $request, Location $location)
    {
        // Hanya superadmin yang bisa toggle status
        if (auth()->user()->role !== 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only superadmin can change location status.'
            ], 403);
        }

        try {
            $oldStatus = $location->status;
            $newStatus = $oldStatus === 'active' ? 'inactive' : 'active';

            $location->update(['status' => $newStatus]);

            // Log ActivityLog
            ActivityLog('location_status_changed')
                ->causedBy(auth()->user())
                ->withProperties([
                    'location_id' => $location->id,
                    'location_name' => $location->name,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                ])
                ->log('Location status changed: ' . $location->name . ' from ' . $oldStatus . ' to ' . $newStatus);

            return response()->json([
                'success' => true,
                'message' => 'Location status updated successfully!',
                'data' => $location
            ]);
        } catch (\Exception $e) {
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
