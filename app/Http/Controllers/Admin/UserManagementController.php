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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserManagementController extends Controller
{
    /**
     * Constructor - Pastikan hanya SuperAdmin yang bisa akses
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isSuperAdmin()) {
                abort(403, 'Unauthorized access. Only Super Administrator can manage users.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::with('department')
            ->where('id', '!=', Auth::id()) // Exclude current user
            ->latest();

        // Filter by role (multiple roles)
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
        if ($request->has('department_id') && !empty($request->department_id)) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by email verification
        if ($request->has('verified')) {
            if ($request->verified === 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->verified === 'unverified') {
                $query->whereNull('email_verified_at');
            }
        }

        // Filter by signature
        if ($request->has('signature')) {
            if ($request->signature === 'has') {
                $query->where('has_signature', true);
            } elseif ($request->signature === 'none') {
                $query->where('has_signature', false);
            }
        }

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        // Sort
        $sortField = $request->get('sort_field', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        // Handle AJAX request untuk filter tanpa reload
        if ($request->ajax() && $request->has('filter')) {
            $users = $query->paginate($request->get('per_page', 15));
            return response()->json([
                'success' => true,
                'html' => view('admin.users.partials.user-rows', compact('users'))->render(),
                'pagination' => (string) $users->links('pagination::bootstrap-4')
            ]);
        }

        // Get paginated results
        $users = $query->paginate($request->get('per_page', 15))->withQueryString();

        // Get departments for dropdown
        $departments = Department::active()->orderBy('name')->get();

        // Get statistics for the view
        $statistics = $this->getUserStatistics();

        // Get filter counts for sidebar badges
        $filterCounts = [
            'pending_approval' => User::where('status', 'pending')->count(),
            'technicians' => User::where('role', 'technician')->where('status', 'active')->count(),
            'inactive_users' => User::where('status', 'inactive')->count(),
            'managers' => User::where('role', 'manager')->where('status', 'active')->count(),
            'pending_technicians' => User::where('role', 'technician')->where('status', 'pending')->count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'unverified_users' => User::whereNull('email_verified_at')->count(),
            'with_signature' => User::where('has_signature', true)->count(),
        ];

        return view('admin.users.index', compact(
            'users',
            'departments',
            'statistics',
            'filterCounts'
        ));
    }

    /**
     * Get user statistics for sidebar and dashboard
     */
    public function getUserStatistics()
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'pending_users' => User::where('status', 'pending')->count(),
            'inactive_users' => User::where('status', 'inactive')->count(),
            'superadmin_count' => User::where('role', 'superadmin')->count(),
            'admin_eng_count' => User::where('role', 'admin_eng')->count(),
            'gm_count' => User::where('role', 'gm')->count(),
            'om_count' => User::where('role', 'om')->count(),
            'technician_count' => User::where('role', 'technician')->count(),
            'user_count' => User::where('role', 'user')->count(),
            'manager_count' => User::where('role', 'manager')->count(),
            'pending_technicians' => User::where('role', 'technician')->where('status', 'pending')->count(),
            'verified_emails' => User::whereNotNull('email_verified_at')->count(),
            'unverified_emails' => User::whereNull('email_verified_at')->count(),
            'with_signature' => User::where('has_signature', true)->count(),
        ];
    }

    /**
     * Show the specified user
     */
    public function show(User $user)
    {
        return response()->json([
            'success' => true,
            'data' => $user->load('department')
        ]);
    }

    /**
     * Get user details for edit modal
     */
    public function getUserDetails(User $user)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'status' => $user->status,
                'department_id' => $user->department_id,
                'profile_picture_url' => $user->profile_picture_url,
                'has_signature' => $user->has_signature,
                'email_verified_at' => $user->email_verified_at,
            ]
        ]);
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
            'role' => 'required|in:superadmin,admin_eng,gm,om,technician,user,manager',
            'status' => 'required|in:active,inactive,pending',
            'department_id' => 'nullable|exists:departments,id',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'email.unique' => 'This email is already registered.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
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
            $data['email_verified_at'] = now(); // Auto verify email when created by admin

            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('profile_pictures', $filename, 'public');
                $data['profile_picture'] = $path;
            }

            $user = User::create($data);

            // Create activity log
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'user_created',
                'description' => 'User created: ' . $user->name . ' (' . $user->email . ')',
                'old_values' => null,
                'new_values' => json_encode($user->toArray()),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Create notification for user
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Account Created',
                'message' => 'Your account has been created by administrator. You can now login.',
                'type' => 'success',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User created successfully!',
                'data' => $user->load('department')
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
     * Update the specified user - FIXED VERSION
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:superadmin,admin_eng,gm,om,technician,user,manager',
            'status' => 'required|in:active,inactive,pending',
            'department_id' => 'nullable|exists:departments,id',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'email.unique' => 'This email is already registered.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
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

            // Prepare data for update - exclude fields that need special handling
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => $request->role,
                'status' => $request->status,
                'department_id' => $request->department_id,
            ];

            // Update password only if provided
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                // Delete old profile picture if exists
                if ($user->profile_picture) {
                    Storage::disk('public')->delete($user->profile_picture);
                }

                $file = $request->file('profile_picture');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('profile_pictures', $filename, 'public');
                $data['profile_picture'] = $path;
            }

            // Update the user
            $user->update($data);

            // Refresh the user model to get updated data
            $user = $user->fresh()->load('department');

            // Create activity log
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'user_updated',
                'description' => 'User updated: ' . $user->name . ' (' . $user->email . ')',
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
                'data' => $user
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
     * Toggle user status
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

            // If activating from pending, auto verify email
            $data = ['status' => $newStatus];
            if ($oldStatus === 'pending' && $newStatus === 'active' && !$user->email_verified_at) {
                $data['email_verified_at'] = now();
            }

            $user->update($data);

            // Create activity log
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'user_status_changed',
                'description' => 'User status changed: ' . $user->name . ' from ' . $oldStatus . ' to ' . $newStatus,
                'old_values' => json_encode(['status' => $oldStatus]),
                'new_values' => json_encode(['status' => $newStatus]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Create notification for user
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Account Status Changed',
                'message' => 'Your account status has been changed to: ' . ucfirst($newStatus),
                'type' => $newStatus === 'active' ? 'success' : 'warning',
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
            // Check if trying to delete own account
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete your own account!'
                ], 403);
            }

            // Verify SuperAdmin password
            if (!$request->has('admin_password')) {
                return response()->json([
                    'success' => false,
                    'message' => 'SuperAdmin password is required to delete user.'
                ], 422);
            }

            if (!Hash::check($request->admin_password, Auth::user()->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid SuperAdmin password. Please try again.'
                ], 422);
            }

            $userName = $user->name;

            // Delete profile picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            // Delete signature if exists
            if ($user->signature_path) {
                Storage::disk('public')->delete($user->signature_path);
            }

            // Create activity log before deletion
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'user_deleted',
                'description' => 'User deleted: ' . $userName . ' (' . $user->email . ')',
                'old_values' => json_encode($user->toArray()),
                'new_values' => null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Delete user (soft delete)
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

    /**
     * Get departments list for dropdown
     */
    public function getDepartments(Request $request)
    {
        $departments = Department::active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $departments
        ]);
    }

    /**
     * Bulk update users status
     */
    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'action' => 'required|in:activate,deactivate,delete,verify_email',
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
            $userIds = $request->user_ids;

            // Remove current user from bulk operations
            $userIds = array_diff($userIds, [auth()->id()]);

            switch ($request->action) {
                case 'activate':
                    User::whereIn('id', $userIds)->update(['status' => 'active']);
                    break;
                case 'deactivate':
                    User::whereIn('id', $userIds)->update(['status' => 'inactive']);
                    break;
                case 'verify_email':
                    User::whereIn('id', $userIds)->update(['email_verified_at' => now()]);
                    break;
                case 'delete':
                    User::whereIn('id', $userIds)->delete();
                    break;
            }

            // Create activity log for bulk operation
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'bulk_' . $request->action,
                'description' => 'Bulk ' . $request->action . ' performed on ' . count($userIds) . ' users',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bulk operation completed successfully!',
                'affected' => count($userIds)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk operation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export users data
     */
    public function export(Request $request)
    {
        $query = User::with('department');

        // Apply filters same as index
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $users = $query->get();

        // Generate CSV
        $filename = 'users_export_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                'ID',
                'Name',
                'Email',
                'Phone',
                'Role',
                'Department',
                'Status',
                'Email Verified',
                'Has Signature',
                'Created At'
            ]);

            // Data
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->phone ?? '-',
                    $user->role_name,
                    $user->department->name ?? '-',
                    ucfirst($user->status),
                    $user->email_verified_at ? 'Yes' : 'No',
                    $user->has_signature ? 'Yes' : 'No',
                    $user->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Activate pending user with department selection
     */
    public function activateWithDepartment(Request $request, User $user)
    {
        try {
            // Prevent changing own status
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot change your own status!'
                ], 403);
            }

            // Validate only if user is in pending status
            if ($user->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not in pending status.'
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'department_id' => 'nullable|exists:departments,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $oldStatus = $user->status;

            // Update user: set active, assign department if provided, verify email
            $data = [
                'status' => 'active',
                'email_verified_at' => now(),
            ];

            if ($request->has('department_id') && !empty($request->department_id)) {
                $data['department_id'] = $request->department_id;
            }

            $user->update($data);

            // Create activity log
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'user_activated_with_department',
                'description' => 'User activated from pending: ' . $user->name .
                    ($request->department_id ? ' assigned to department ID: ' . $request->department_id : ' (no department)'),
                'old_values' => json_encode(['status' => $oldStatus, 'department_id' => $user->getOriginal('department_id')]),
                'new_values' => json_encode(['status' => 'active', 'department_id' => $user->department_id]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Create notification for user
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Account Activated',
                'message' => 'Your account has been activated. You can now login.',
                'type' => 'success',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User activated successfully!' . ($request->department_id ? ' Department assigned.' : ''),
                'data' => $user->load('department')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to activate user: ' . $e->getMessage()
            ], 500);
        }
    }
}
