<?php
// app/Http/Controllers/SignatureController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SignatureController extends Controller
{
    /**
     * Constructor - Check permission
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Only admin_eng, om, gm, manager can access signature pages
            $allowedRoles = ['admin_eng', 'om', 'gm', 'manager'];
            if (!in_array(Auth::user()->role, $allowedRoles)) {
                abort(403, 'Unauthorized access. Only admin_eng, om, gm, and manager can manage signatures.');
            }
            return $next($request);
        });
    }

    /**
     * Show signature management page
     */
    public function index()
    {
        $user = Auth::user();
        return view('signature.index', compact('user'));
    }

    /**
     * Upload signature from signature pad
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'signature' => 'required|image|mimes:png|max:1024', // Max 1MB
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

            // Baca file sebagai gambar
            $imageContent = file_get_contents($file->getRealPath());
            $image = imagecreatefromstring($imageContent);

            if (!$image) {
                throw new \Exception('Invalid image file');
            }

            // Validasi dimensi asli
            $originalWidth = imagesx($image);
            $originalHeight = imagesy($image);

            // Kasih toleransi
            $expectedWidth = 300;
            $expectedHeight = 200;
            $tolerance = 0.1; // 10% tolerance

            $minWidth = $expectedWidth * (1 - $tolerance);
            $maxWidth = $expectedWidth * (1 + $tolerance);
            $minHeight = $expectedHeight * (1 - $tolerance);
            $maxHeight = $expectedHeight * (1 + $tolerance);

            if (
                $originalWidth < $minWidth || $originalWidth > $maxWidth ||
                $originalHeight < $minHeight || $originalHeight > $maxHeight
            ) {
                imagedestroy($image);
                return response()->json([
                    'success' => false,
                    'message' => "Signature dimensions harus sekitar {$expectedWidth}x{$expectedHeight} pixels. Current: {$originalWidth}x{$originalHeight}"
                ], 422);
            }

            // Buat canvas baru dengan background transparan
            $resizedImage = imagecreatetruecolor(300, 200);

            // Aktifkan alpha channel
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);

            // Isi dengan transparan penuh
            $transparent = imagecolorallocatealpha($resizedImage, 0, 0, 0, 127);
            imagefill($resizedImage, 0, 0, $transparent);

            // Set blending untuk copy gambar
            imagealphablending($resizedImage, true);

            // Copy dan resize gambar asli ke canvas baru
            imagecopyresampled(
                $resizedImage,
                $image,
                0,
                0,
                0,
                0,
                300,
                200,
                $originalWidth,
                $originalHeight
            );

            // Simpan dengan kualitas tinggi dan pertahankan transparansi
            ob_start();
            imagepng($resizedImage, null, 9); // Compression level 9 (maksimum)
            $imageData = ob_get_clean();

            // Clean up memory
            imagedestroy($image);
            imagedestroy($resizedImage);

            // Delete old signature if exists
            if ($user->signature_path && Storage::disk('public')->exists($user->signature_path)) {
                Storage::disk('public')->delete($user->signature_path);
            }

            // Store new signature
            $filename = 'signature_' . $user->id . '_' . time() . '.png';
            $path = 'signatures/' . $filename;

            // Simpan gambar
            Storage::disk('public')->put($path, $imageData);

            // Update user signature
            $user->updateSignature($path);

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'signature_uploaded',
                'description' => 'Digital signature uploaded via signature pad',
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
    public function remove(Request $request)
    {
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
            // Hapus signature menggunakan method dari model
            $user->removeSignature();

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'signature_removed',
                'description' => 'Digital signature removed',
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
    public function getInfo()
    {
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'data' => [
                'has_signature' => $user->has_signature,
                'signature_url' => $user->signature_url,
                'uploaded_at' => $user->signature_updated_at ?
                    $user->signature_updated_at->format('d M Y H:i') : null,
            ]
        ]);
    }
}
