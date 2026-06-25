<?php
// app/Http/Controllers/ProfileController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Ticket;
use App\Models\VoucherRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * Show profile page
     */
    public function index()
    {
        $user = Auth::user()->load('department');

        // Get statistics berdasarkan role
        $stats = $this->getUserStatistics($user);

        return view('profile.index', compact('user', 'stats'));
    }

    /**
     * Get user statistics based on role
     */
    private function getUserStatistics($user)
    {
        $stats = [
            'days_active' => $user->created_at->diffInDays(now()),
            'total_tickets' => 0,
            'resolved_tickets' => 0,
            'pending_approvals' => 0,
        ];

        switch ($user->role) {
            case 'technician':
                $stats['total_tickets'] = $user->assignedTickets()->count();
                $stats['resolved_tickets'] = $user->assignedTickets()
                    ->where('status', 'closed')->count();
                $stats['in_progress'] = $user->assignedTickets()
                    ->whereIn('status', ['in_progress', 'pending_vr'])->count();
                break;

            case 'user':
            case 'manager':
                $stats['total_tickets'] = $user->tickets()->count();
                $stats['resolved_tickets'] = $user->tickets()
                    ->where('status', 'closed')->count();
                $stats['open_tickets'] = $user->tickets()
                    ->where('status', 'open')->count();
                break;

            case 'admin_eng':
                $stats['pending_receive'] = Ticket::where('status', 'open')->count();
                $stats['ready_close'] = Ticket::where('status', 'ready_for_closure')->count();
                break;

            case 'om':
                $stats['pending_approvals'] = Ticket::where('status', 'pending_om')->count();
                $stats['pending_vr'] = VoucherRequest::where('status', 'admin_approved')->count();
                break;

            case 'gm':
                $stats['pending_approvals'] = Ticket::where('status', 'pending_gm')->count();
                $stats['pending_vr'] = VoucherRequest::where('status', 'om_approved')->count();
                break;

            case 'superadmin':
                $stats['total_users'] = User::count();
                $stats['total_tickets'] = Ticket::count();
                break;
        }

        return $stats;
    }

    /**
     * Update profile information
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
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

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'ticket_id' => null,
                'action' => 'updated',
                'description' => 'Profile updated by user',
                'old_values' => $oldData,
                'new_values' => $user->fresh()->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully!',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload profile picture with crop support
     */
    public function uploadProfilePicture(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB
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
            $user = Auth::user();

            // Delete old profile picture if exists
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            // Process and optimize image
            $file = $request->file('profile_picture');
            $image = $this->optimizeProfileImage($file);

            $filename = 'profile_' . $user->id . '_' . time() . '.jpg';
            $path = 'profile_pictures/' . $filename;

            // Save optimized image
            Storage::disk('public')->put($path, $image);

            $oldPicture = $user->profile_picture;
            $user->update(['profile_picture' => $path]);

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'ticket_id' => null,
                'action' => 'updated',
                'description' => 'Profile picture uploaded',
                'old_values' => ['profile_picture' => $oldPicture],
                'new_values' => ['profile_picture' => $path],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profile picture updated successfully!',
                'data' => [
                    'profile_picture_url' => asset('storage/' . $path)
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload profile picture: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload cropped profile picture
     */
    public function uploadCroppedProfilePicture(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|string',
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
            $user = Auth::user();
            $imageData = $request->image;

            // Remove base64 header
            $imageData = str_replace('data:image/jpeg;base64,', '', $imageData);
            $imageData = str_replace('data:image/png;base64,', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            $imageData = base64_decode($imageData);

            // Delete old profile picture
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $filename = 'profile_' . $user->id . '_' . time() . '.jpg';
            $path = 'profile_pictures/' . $filename;

            Storage::disk('public')->put($path, $imageData);

            $oldPicture = $user->profile_picture;
            $user->update(['profile_picture' => $path]);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'ticket_id' => null,
                'action' => 'updated',
                'description' => 'Profile picture uploaded with crop',
                'old_values' => ['profile_picture' => $oldPicture],
                'new_values' => ['profile_picture' => $path],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profile picture updated successfully!',
                'data' => [
                    'profile_picture_url' => asset('storage/' . $path)
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload profile picture: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Optimize profile image
     */
    private function optimizeProfileImage($file)
    {
        $image = imagecreatefromstring(file_get_contents($file));

        if (!$image) {
            throw new \Exception('Invalid image format');
        }

        // Get original dimensions
        $width = imagesx($image);
        $height = imagesy($image);

        // Calculate new dimensions (max 400x400)
        $maxSize = 400;
        $newWidth = $width;
        $newHeight = $height;

        if ($width > $maxSize || $height > $maxSize) {
            if ($width > $height) {
                $newWidth = $maxSize;
                $newHeight = intval($height * $maxSize / $width);
            } else {
                $newHeight = $maxSize;
                $newWidth = intval($width * $maxSize / $height);
            }
        }

        // Create new image
        $optimized = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG
        if ($file->getClientOriginalExtension() == 'png') {
            imagealphablending($optimized, false);
            imagesavealpha($optimized, true);
            $transparent = imagecolorallocatealpha($optimized, 0, 0, 0, 127);
            imagefill($optimized, 0, 0, $transparent);
        }

        // Resize
        imagecopyresampled($optimized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Start output buffer
        ob_start();
        imagejpeg($optimized, null, 85);
        $imageData = ob_get_clean();

        // Clean up
        imagedestroy($image);
        imagedestroy($optimized);

        return $imageData;
    }

    /**
     * Remove profile picture
     */
    public function removeProfilePicture()
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            $oldPicture = $user->profile_picture;

            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $user->update(['profile_picture' => null]);

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'ticket_id' => null,
                'action' => 'deleted',
                'description' => 'Profile picture removed',
                'old_values' => ['profile_picture' => $oldPicture],
                'new_values' => ['profile_picture' => null],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profile picture removed successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove profile picture: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect!'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'ticket_id' => null,
                'action' => 'updated',
                'description' => 'Password changed by user',
                'old_values' => null,
                'new_values' => null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update password: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== SIGNATURE METHODS ====================

    /**
     * Upload signature from signature pad
     */
    public function uploadSignature(Request $request)
    {
        // Check permission
        if (!Auth::user()->canManageSignature()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to upload signature.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'signature' => 'required|image|mimes:png|max:1024',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password is incorrect!'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $file = $request->file('signature');

            // Validasi dimensi - 300x200 dengan tolerance
            list($width, $height) = getimagesize($file);

            $expectedWidth = 300;
            $expectedHeight = 200;
            $tolerance = 0.1; // 10% tolerance

            $minWidth = $expectedWidth * (1 - $tolerance);
            $maxWidth = $expectedWidth * (1 + $tolerance);
            $minHeight = $expectedHeight * (1 - $tolerance);
            $maxHeight = $expectedHeight * (1 + $tolerance);

            if ($width < $minWidth || $width > $maxWidth || $height < $minHeight || $height > $maxHeight) {
                return response()->json([
                    'success' => false,
                    'message' => "Signature dimensions harus sekitar {$expectedWidth}x{$expectedHeight} pixels. Current: {$width}x{$height}"
                ], 422);
            }

            // Load gambar PNG asli
            $srcImage = imagecreatefrompng($file->getRealPath());

            // Buat canvas baru dengan ukuran 300x200
            $dstImage = imagecreatetruecolor(300, 200);

            // Matiin alpha blending & enable alpha save
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);

            // Isi background dengan transparan
            $transparent = imagecolorallocatealpha($dstImage, 0, 0, 0, 127);
            imagefill($dstImage, 0, 0, $transparent);

            // Copy gambar dengan resampling
            imagecopyresampled(
                $dstImage,
                $srcImage,
                0,
                0,
                0,
                0,
                300,
                200,
                $width,
                $height
            );

            // Simpan ke buffer dengan PNG
            ob_start();
            imagepng($dstImage, null, 9);
            $imageData = ob_get_clean();

            // Clean up memory
            imagedestroy($srcImage);
            imagedestroy($dstImage);

            // Delete old signature if exists
            if ($user->signature_path && Storage::disk('public')->exists($user->signature_path)) {
                Storage::disk('public')->delete($user->signature_path);
            }

            // Store new signature
            $filename = 'signature_' . $user->id . '_' . time() . '.png';
            $path = 'signatures/' . $filename;
            Storage::disk('public')->put($path, $imageData);

            $oldSignature = $user->signature_path;
            $user->updateSignature($path);

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'ticket_id' => null,
                'action' => 'updated',
                'description' => 'Digital signature uploaded via signature pad',
                'old_values' => ['signature_path' => $oldSignature],
                'new_values' => ['signature_path' => $path],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Signature uploaded successfully!',
                'data' => [
                    'signature_url' => asset('storage/' . $path),
                    'uploaded_at' => $user->signature_updated_at ?
                        $user->signature_updated_at->format('d M Y H:i') : now()->format('d M Y H:i')
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload signature: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove signature
     */
    public function removeSignature(Request $request)
    {
        // Check permission
        if (!Auth::user()->canManageSignature()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to remove signature.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Password is required'
            ], 422);
        }

        $user = Auth::user();

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password is incorrect!'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $oldSignature = $user->signature_path;
            $user->removeSignature();

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'ticket_id' => null,
                'action' => 'deleted',
                'description' => 'Digital signature removed',
                'old_values' => ['signature_path' => $oldSignature],
                'new_values' => ['signature_path' => null],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Signature removed successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove signature: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get signature info
     */
    public function getSignatureInfo()
    {
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'data' => [
                'has_signature' => $user->has_signature,
                'signature_url' => $user->signature_url,
                'uploaded_at' => $user->signature_updated_at ?
                    $user->signature_updated_at->format('d M Y H:i') : null,
                'can_manage' => $user->canManageSignature()
            ]
        ]);
    }


    public function getSignatureInfoForPR()
    {
        $user = Auth::user();

        $hasSignature = !empty($user->signature_path) && Storage::disk('public')->exists($user->signature_path);

        return response()->json([
            'has_signature' => $hasSignature,
            'signature_date' => $user->signature_updated_at ? $user->signature_updated_at->format('d M Y H:i') : null,
            'signature_path' => $hasSignature ? Storage::url($user->signature_path) : null,
        ]);
    }

}
