<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Signature;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use App\Models\VoucherRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TicketReportController extends Controller
{
    /**
     * Generate HTML report for ticket with options:
     * - full: Main report + attachments + PR photos
     * - main: Main report only
     * - attachments: Attachments only
     * - pr_photos: PR photos only
     */
    public function generateReport($id, Request $request)
    {
        $type = $request->get('type', 'full');

        $ticket = Ticket::with([
            'user',
            'category',
            'priority',
            'location',
            'department',
            'assignedUser',
            'attachments',
            'comments' => function ($query) {
                $query->where('is_followup', true)
                    ->where('is_internal', 0)
                    ->with('user')
                    ->orderBy('created_at', 'asc');
            },
            'signatures.user',
            'voucherRequests.attachments',
            'approval'
        ])->findOrFail($id);

        $user = Auth::user();

        // Authorization check
        if (!$this->canViewTicket($user, $ticket)) {
            abort(403, 'Unauthorized access to this ticket report');
        }

        // Get signatures
        $signatures = $this->getSignatures($ticket);

        // Get PR photos (dari voucher_requests attachments)
        $prPhotos = $this->getPRPhotos($ticket);

        // Get follow-up comments
        $followUpComments = $this->getFollowUpComments($ticket);

        // Filter attachments
        $imageAttachments = $this->getImageAttachments($ticket);
        $fileAttachments = $this->getFileAttachments($ticket);

        // Get status display text
        $statusDisplay = $this->getStatusDisplay($ticket->status);

        // Data untuk main report
        $mainData = [
            'ticket' => $ticket,
            'signatures' => $signatures,
            'followUpComments' => $followUpComments,
            'fileAttachments' => $fileAttachments,
            'currentDate' => now()->format('d F Y'),
            'currentDateTime' => now()->format('d F Y, H:i'),
            'statusDisplay' => $statusDisplay,
            'helper' => $this,
        ];

        // Data untuk attachments (foto dari ticket)
        $attachmentData = [
            'ticket' => $ticket,
            'imageAttachments' => $imageAttachments,
            'helper' => $this,
        ];

        // Data untuk PR photos
        $prPhotosData = [
            'ticket' => $ticket,
            'prPhotos' => $prPhotos,
            'helper' => $this,
        ];

        // Generate berdasarkan type
        switch ($type) {
            case 'pr_photos':
                // Kalau gak ada PR photos, kasih response sesuai
                if (count($prPhotos) == 0) {
                    // Kalau request AJAX/API
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'No PR photos found for this ticket'
                        ], 404);
                    }
                    // Kalau request browser biasa
                    abort(404, 'No PR photos found');
                }
                return view('tickets.report_pr_photos', $prPhotosData);

            case 'attachments':
                // Kalau gak ada image attachments
                if (count($imageAttachments) == 0) {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'No image attachments found for this ticket'
                        ], 404);
                    }
                    abort(404, 'No image attachments found');
                }
                return view('tickets.report_attachments', $attachmentData);

            case 'main':
                return view('tickets.report_main', $mainData);

            case 'full':
            default:
                // Full report selalu tampil walaupun attachments/PR photos kosong
                return view('tickets.report_full', [
                    'mainData' => $mainData,
                    'attachmentData' => $attachmentData,
                    'prPhotosData' => $prPhotosData,
                    'hasPrPhotos' => count($prPhotos) > 0,
                    'hasAttachments' => count($imageAttachments) > 0,
                    'ticket' => $ticket,
                ]);
        }
    }

    /**
     * View HTML report (alias dari generateReport)
     */
    public function viewReport($id, Request $request)
    {
        return $this->generateReport($id, $request);
    }

    /**
     * Detect if request is from mobile device
     */
    private function isMobileDevice(Request $request)
    {
        $userAgent = $request->header('User-Agent');
        $mobileKeywords = ['Mobile', 'Android', 'iPhone', 'iPad', 'iPod', 'webOS', 'BlackBerry', 'Windows Phone'];

        foreach ($mobileKeywords as $keyword) {
            if (stripos($userAgent, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get PR photos from voucher requests
     */
    private function getPRPhotos($ticket)
    {
        $prPhotos = [];

        foreach ($ticket->voucherRequests as $pr) {
            foreach ($pr->attachments as $attachment) {
                $extension = strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION));
                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg']);

                if (Storage::disk('public')->exists($attachment->file_path)) {
                    $prPhotos[] = (object) [
                        'id' => $attachment->id,
                        'file_name' => $attachment->file_name,
                        'file_path' => $attachment->file_path,
                        'file_size' => $attachment->file_size,
                        'created_at' => $attachment->created_at,
                        'vr_number' => $pr->vr_number,
                        'extension' => $extension,
                        'is_image' => $isImage,
                        'url' => asset('storage/' . $attachment->file_path),
                        'absolute_path' => storage_path('app/public/' . $attachment->file_path),
                    ];
                }
            }
        }

        usort($prPhotos, function ($a, $b) {
            return strtotime($a->created_at) - strtotime($b->created_at);
        });

        return $prPhotos;
    }

    /**
     * Get image attachments only
     */
    private function getImageAttachments($ticket)
    {
        $imageAttachments = [];

        foreach ($ticket->attachments as $attachment) {
            $extension = strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION));
            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg']);

            if (Storage::disk('public')->exists($attachment->file_path)) {
                $imageAttachments[] = (object) [
                    'id' => $attachment->id,
                    'file_name' => $attachment->file_name,
                    'file_path' => $attachment->file_path,
                    'file_size' => $attachment->file_size,
                    'created_at' => $attachment->created_at,
                    'extension' => $extension,
                    'is_image' => $isImage,
                    'url' => asset('storage/' . $attachment->file_path),
                    'absolute_path' => storage_path('app/public/' . $attachment->file_path),
                ];
            }
        }

        return $imageAttachments;
    }

    /**
     * Get file attachments (non-image)
     */
    private function getFileAttachments($ticket)
    {
        $fileAttachments = [];

        foreach ($ticket->attachments as $attachment) {
            $extension = strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION));
            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg']);

            if (!$isImage && Storage::disk('public')->exists($attachment->file_path)) {
                $fileAttachments[] = (object) [
                    'id' => $attachment->id,
                    'file_name' => $attachment->file_name,
                    'file_path' => $attachment->file_path,
                    'file_size' => $attachment->file_size,
                    'file_type' => $attachment->file_type,
                    'created_at' => $attachment->created_at,
                    'extension' => $extension,
                    'url' => asset('storage/' . $attachment->file_path),
                ];
            }
        }

        return $fileAttachments;
    }

    /**
     * Get follow-up comments
     */
    private function getFollowUpComments($ticket)
    {
        $followUps = [];

        foreach ($ticket->comments as $comment) {
            $isFollowUp = $comment->is_followup;

            $content = strtolower($comment->comment);
            $hasFollowUpKeyword = str_contains($content, 'follow-up') ||
                str_contains($content, 'followup') ||
                str_contains($content, 'completion notes');

            if (!$isFollowUp && !$hasFollowUpKeyword) {
                continue;
            }

            $text = $comment->comment;

            $text = preg_replace('/^Work completed by technician\.?\s*/i', '', $text);
            $text = preg_replace('/^Completion Notes:\s*/i', '', $text);
            $text = preg_replace('/^VR Requested\.?\s*/i', '', $text);
            $text = preg_replace('/^Admin Follow-up Notes:\s*/i', '', $text);
            $text = preg_replace('/^PR Requested\.?\s*/i', '', $text);
            $text = trim($text);

            if (!empty($text) && $text !== '-') {
                $followUps[] = [
                    'user' => $comment->user->name ?? 'System',
                    'date' => $comment->created_at->format('d/m H:i'),
                    'text' => $text
                ];
            }
        }

        return $followUps;
    }

    /**
     * Get signatures helper
     */
    private function getSignatures($ticket)
    {
        $signatures = [];
        foreach ($ticket->signatures as $signature) {
            if ($signature->stage && $signature->user) {
                $signatures[$signature->stage] = $signature;
            }
        }
        return $signatures;
    }

    /**
     * Get status display text
     */
    private function getStatusDisplay($status)
    {
        $statusMap = [
            'open' => 'OPEN',
            'received' => 'RECEIVED',
            'pending_om' => 'OM APPROVAL',
            'in_progress' => 'IN PROGRESS',
            'pending_vr' => 'PR APPROVAL',
            'completed' => 'COMPLETED',
            'pending_gm' => 'GM APPROVAL',
            'ready_for_closure' => 'READY FOR CLOSURE',
            'closed' => 'CLOSED',
            'cancelled' => 'CANCELLED',
        ];
        return $statusMap[$status] ?? strtoupper(str_replace('_', ' ', $status));
    }

    /**
     * Save report as HTML
     */
    public function saveReport($id, Request $request)
    {
        $type = $request->get('type', 'full');

        $ticket = Ticket::findOrFail($id);
        $user = Auth::user();

        if (!$this->canViewTicket($user, $ticket)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        try {
            $this->ensureDirectoryExists(storage_path('app/public/reports'));

            $signatures = $this->getSignatures($ticket);
            $prPhotos = $this->getPRPhotos($ticket);
            $followUpComments = $this->getFollowUpComments($ticket);
            $imageAttachments = $this->getImageAttachments($ticket);
            $fileAttachments = $this->getFileAttachments($ticket);
            $statusDisplay = $this->getStatusDisplay($ticket->status);

            $mainData = [
                'ticket' => $ticket,
                'signatures' => $signatures,
                'followUpComments' => $followUpComments,
                'fileAttachments' => $fileAttachments,
                'currentDate' => now()->format('d F Y'),
                'statusDisplay' => $statusDisplay,
                'helper' => $this,
            ];

            $attachmentData = [
                'ticket' => $ticket,
                'imageAttachments' => $imageAttachments,
                'helper' => $this,
            ];

            $prPhotosData = [
                'ticket' => $ticket,
                'prPhotos' => $prPhotos,
                'helper' => $this,
            ];

            $html = '';

            switch ($type) {
                case 'pr_photos':
                    if (count($prPhotos) == 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'No PR photos found'
                        ], 404);
                    }
                    $html = view('tickets.report_pr_photos', $prPhotosData)->render();
                    $filename = 'reports/pr-photos-' . $ticket->ticket_number . '-' . time() . '.html';
                    break;

                case 'attachments':
                    if (count($imageAttachments) == 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'No image attachments found'
                        ], 404);
                    }
                    $html = view('tickets.report_attachments', $attachmentData)->render();
                    $filename = 'reports/attachments-' . $ticket->ticket_number . '-' . time() . '.html';
                    break;

                case 'main':
                    $html = view('tickets.report_main', $mainData)->render();
                    $filename = 'reports/report-' . $ticket->ticket_number . '-' . time() . '.html';
                    break;

                case 'full':
                default:
                    $html = view('tickets.report_full', [
                        'mainData' => $mainData,
                        'attachmentData' => $attachmentData,
                        'prPhotosData' => $prPhotosData,
                        'hasPrPhotos' => count($prPhotos) > 0,
                        'hasAttachments' => count($imageAttachments) > 0,
                        'ticket' => $ticket,
                    ])->render();
                    $filename = 'reports/ticket-' . $ticket->ticket_number . '-full-' . time() . '.html';
                    break;
            }

            $fullPath = storage_path('app/public/' . $filename);
            file_put_contents($fullPath, $html);

            return response()->json([
                'success' => true,
                'message' => 'Report saved successfully',
                'path' => $filename,
                'url' => asset('storage/' . $filename)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper methods
     */
    public function getUserName($user, $fallback = '-')
    {
        if (!$user)
            return $fallback;
        return $user->name ?? $fallback;
    }

    public function getUserRole($user, $fallback = '-')
    {
        if (!$user)
            return $fallback;
        return ucfirst($user->role) ?? $fallback;
    }

    public function formatDate($date, $format = 'd/m/Y', $fallback = '-')
    {
        if (!$date)
            return $fallback;
        try {
            return $date->format($format);
        } catch (\Exception $e) {
            return $fallback;
        }
    }

    public function hasSignature($stage, $signatures)
    {
        if (!isset($signatures[$stage]))
            return false;
        $signature = $signatures[$stage];
        if (!$signature->signature_path)
            return false;
        return Storage::disk('public')->exists($signature->signature_path);
    }

    public function getSignaturePath($stage, $signatures, $fallback = null)
    {
        if ($this->hasSignature($stage, $signatures)) {
            return asset('storage/' . $signatures[$stage]->signature_path);
        }
        return $fallback;
    }

    public function getSignatureData($stage, $signatures, $field = 'user', $fallback = '-')
    {
        if (!isset($signatures[$stage]))
            return $fallback;
        $signature = $signatures[$stage];
        switch ($field) {
            case 'user':
                return $this->getUserName($signature->user ?? null, $fallback);
            case 'role':
                return $this->getUserRole($signature->user ?? null, $fallback);
            case 'date':
                return $this->formatDate($signature->signed_at ?? null, 'd/m/Y', $fallback);
            default:
                return $fallback;
        }
    }

    private function canViewTicket($user, $ticket)
    {
        if (!$user)
            return false;
        switch ($user->role) {
            case 'superadmin':
            case 'admin_eng':
            case 'om':
            case 'gm':
                return true;
            case 'user':
                return $ticket->user_id === $user->id;
            case 'technician':
                return $ticket->assigned_to === $user->id || $ticket->user_id === $user->id;
            case 'manager':
                return $ticket->department_id === $user->department_id;
            default:
                return false;
        }
    }

    private function ensureDirectoryExists($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }

    public function cleanupTempFiles()
    {
        $reportDir = storage_path('app/public/reports');
        if (!is_dir($reportDir))
            return;

        $files = glob($reportDir . '/*.html');
        $now = time();

        foreach ($files as $file) {
            if (is_file($file) && ($now - filemtime($file)) > 86400) {
                @unlink($file);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Old reports cleaned up'
        ]);
    }
}
