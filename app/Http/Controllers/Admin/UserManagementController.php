<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\ActivityLog;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::with('department')->latest();

        // Filter by role (single role or array of roles)
        if ($request->has('role')) {
            if (is_array($request->role)) {
                $query->whereIn('role', $request->role);
            } else {
                $query->where('role', $request->role);
            }
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by department
        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by search keyword
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        $users = $query->get();
        $departments = Department::active()->get();

        // Pass filter parameters to view
        $filters = $request->only(['role', 'status', 'department_id', 'search']);

        return view('admin.users.index', compact('users', 'departments', 'filters'));
    }

    /**
     * Get user statistics for sidebar
     */
    public function getUserStatistics()
    {
        return [
            'pending_users' => User::where('status', 'pending')->count(),
            'pending_technicians' => User::where('role', 'technician')->where('status', 'pending')->count(),
            'inactive_users' => User::where('status', 'inactive')->count(),
            'admin_manager_count' => User::whereIn('role', ['admin', 'manager', 'gm', 'om'])->count(),
        ];
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,manager,gm,om,technician,user',
            'status' => 'required|in:active,inactive,pending',
            'department_id' => 'nullable|exists:departments,id',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
            $data = $request->except(['password', 'password_confirmation', 'profile_picture']);
            $data['password'] = Hash::make($request->password);

            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('profile_pictures', $filename, 'public');
                $data['profile_picture'] = $path;
            }

            $user = User::create($data);

            // Create activity log
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'user_created',
                'description' => 'User created: ' . $user->name,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Create notification for user if email is verified
            if ($user->status === 'active' && $user->email_verified_at) {
                Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Account Created',
                    'message' => 'Your account has been created successfully.',
                    'type' => 'success',
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User created successfully!',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,manager,gm,om,technician,user',
            'status' => 'required|in:active,inactive,pending',
            'department_id' => 'nullable|exists:departments,id',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
            $oldData = $user->toArray();
            $data = $request->except(['password', 'password_confirmation', 'profile_picture']);

            // Update password if provided
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                // Delete old profile picture if exists
                if ($user->profile_picture && \Storage::disk('public')->exists($user->profile_picture)) {
                    \Storage::disk('public')->delete($user->profile_picture);
                }

                $file = $request->file('profile_picture');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('profile_pictures', $filename, 'public');
                $data['profile_picture'] = $path;
            }

            $user->update($data);

            // Create activity log
            ActivityLog::create([
                'user_id' => auth()->id(),
                'ticket_id' => null,
                'action' => 'user_updated',
                'description' => 'User updated: ' . $user->name,
                'old_values' => json_encode($oldData),
                'new_values' => json_encode($user->toArray()),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Create notification for user if status changed
            if ($oldData['status'] !== $user->status) {
                Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Account Status Updated',
                    'message' => 'Your account status has been changed to: ' . ucfirst($user->status),
                    'type' => $user->status === 'active' ? 'success' : 'warning',
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully!',
                'data' => $user->load('department')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle user status (with department auto-assign from registration)
     */
    public function toggleStatus(Request $request, User $user)
    {
        try {
            // Prevent changing own status
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot change your own status!'
                ], 403);
            }

            DB::beginTransaction();

            $oldStatus = $user->status;
            $newStatus = $user->status === 'active' ? 'inactive' : 'active';

            // If activating a pending user, use their existing department from registration
            if ($oldStatus === 'pending' && $newStatus === 'active') {
                // User already has department from registration, keep it
                $data = ['status' => 'active'];

                // Create welcome notification
                Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Account Activated',
                    'message' => 'Your account has been activated. You can now login.',
                    'type' => 'success',
                ]);
            } else {
                $data = ['status' => $newStatus];

                // Create status change notification
                if ($oldStatus !== $newStatus) {
                    Notification::create([
                        'user_id' => $user->id,
                        'title' => 'Account Status Changed',
                        'message' => 'Your account status has been changed to: ' . ucfirst($newStatus),
                        'type' => $newStatus === 'active' ? 'success' : 'warning',
                    ]);
                }
            }

            $user->update($data);

            // Create activity log
            ActivityLog::create([
                'user_id' => auth()->id(),
                'ticket_id' => null,
                'action' => 'user_status_changed',
                'description' => 'User status changed: ' . $user->name . ' from ' . $oldStatus . ' to ' . $newStatus,
                'old_values' => json_encode(['status' => $oldStatus]),
                'new_values' => json_encode(['status' => $newStatus]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User status updated successfully!',
                'data' => $user->load('department')
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
     * Remove the specified user
     */
    public function destroy(Request $request, User $user)
    {
        DB::beginTransaction();
        try {
            // 1. Cek apakah user mencoba menghapus akun sendiri
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete your own account!'
                ], 403);
            }

            // 2. Verifikasi password admin
            if (!$request->has('admin_password')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin password is required to delete user.'
                ], 422);
            }

            // 3. Validasi password admin
            if (!Hash::check($request->admin_password, auth()->user()->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid admin password. Please try again.'
                ], 422);
            }

            $userName = $user->name;

            // 4. Delete profile picture jika ada
            if ($user->profile_picture && \Storage::disk('public')->exists($user->profile_picture)) {
                \Storage::disk('public')->delete($user->profile_picture);
            }

            // 5. Create activity log sebelum deletion
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'user_deleted',
                'description' => 'User deleted: ' . $userName,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // 6. Delete user
            $user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }

}
