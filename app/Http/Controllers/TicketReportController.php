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
use Spatie\LaravelPdf\Enums\Format;
use Spatie\LaravelPdf\Enums\Orientation;
use Spatie\LaravelPdf\Facades\Pdf;
use setasign\Fpdi\Fpdi;

class TicketReportController extends Controller
{
    /**
     * Generate PDF report for ticket with 3 options:
     * - full: Main report + attachments
     * - main: Main report only
     * - attachments: Attachments only
     */
    public function generateReport($id, Request $request)
    {
        $type = $request->get('type', 'full'); // full, main, attachments

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
            'voucherRequests.items',
            'approval'
        ])->findOrFail($id);

        $user = Auth::user();

        // Check permission
        if (!$this->canViewTicket($user, $ticket)) {
            abort(403, 'Unauthorized access to this ticket report');
        }

        // Buat folder temp jika belum ada
        $this->ensureDirectoryExists(storage_path('app/temp'));

        // Get signatures
        $signatures = $this->getSignatures($ticket);

        // Get PR data
        $prData = null;
        $prItems = collect([]);
        $totalPRAmount = 0;

        if ($ticket->voucherRequests->count() > 0) {
            $prData = $ticket->voucherRequests->first();
            $prItems = $prData->items;
            $totalPRAmount = $prData->total_amount;
        }

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
            'prData' => $prData,
            'prItems' => $prItems,
            'totalPRAmount' => $totalPRAmount,
            'followUpComments' => $followUpComments,
            'fileAttachments' => $fileAttachments,
            'currentDate' => now()->format('d F Y'),
            'currentDateTime' => now()->format('d F Y, H:i'),
            'statusDisplay' => $statusDisplay,
            'helper' => $this,
        ];

        // Data untuk attachments
        $attachmentData = [
            'ticket' => $ticket,
            'imageAttachments' => $imageAttachments,
            'helper' => $this,
        ];

        // Generate berdasarkan type
        switch ($type) {
            case 'attachments':
                // Validasi apakah ada attachments
                if (count($imageAttachments) == 0) {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'No image attachments found for this ticket'
                        ], 404);
                    }
                    abort(404, 'No image attachments found');
                }

                // Generate hanya attachments
                $pdfPath = storage_path('app/temp/attachments_' . uniqid() . '.pdf');

                Pdf::view('tickets.report_attachments', $attachmentData)
                    ->format(Format::A4)
                    ->orientation(Orientation::Portrait)
                    ->margins(8, 8, 8, 8)
                    ->save($pdfPath);

                $filename = 'attachments-' . $ticket->ticket_number . '.pdf';

                return response()->file($pdfPath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"'
                ])->deleteFileAfterSend(true);

            case 'main':
                // Generate hanya main report
                $pdfPath = storage_path('app/temp/main_' . uniqid() . '.pdf');

                Pdf::view('tickets.report_main', $mainData)
                    ->format(Format::A4)
                    ->orientation(Orientation::Portrait)
                    ->margins(8, 8, 8, 8)
                    ->save($pdfPath);

                $filename = 'report-' . $ticket->ticket_number . '.pdf';

                return response()->file($pdfPath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"'
                ])->deleteFileAfterSend(true);

            case 'full':
            default:
                // Generate main report
                $mainPdfPath = storage_path('app/temp/main_' . uniqid() . '.pdf');

                Pdf::view('tickets.report_main', $mainData)
                    ->format(Format::A4)
                    ->orientation(Orientation::Portrait)
                    ->margins(8, 8, 8, 8)
                    ->save($mainPdfPath);

                // Jika ada attachments, merge
                if (count($imageAttachments) > 0) {
                    $attachmentPdfPath = storage_path('app/temp/attachment_' . uniqid() . '.pdf');

                    Pdf::view('tickets.report_attachments', $attachmentData)
                        ->format(Format::A4)
                        ->orientation(Orientation::Portrait)
                        ->margins(8, 8, 8, 8)
                        ->save($attachmentPdfPath);

                    $outputPath = storage_path('app/temp/final_' . uniqid() . '.pdf');
                    $this->mergePdfs([$mainPdfPath, $attachmentPdfPath], $outputPath);

                    // Hapus file sementara
                    @unlink($mainPdfPath);
                    @unlink($attachmentPdfPath);

                    $filename = 'ticket-' . $ticket->ticket_number . '-full.pdf';

                    return response()->file($outputPath, [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'attachment; filename="' . $filename . '"'
                    ])->deleteFileAfterSend(true);
                }

                // Jika tidak ada attachments, return main report langsung
                $filename = 'ticket-' . $ticket->ticket_number . '-full.pdf';

                return response()->file($mainPdfPath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"'
                ])->deleteFileAfterSend(true);
        }
    }

    /**
     * View PDF in browser with 3 options
     */
    public function viewReport($id, Request $request)
    {
        $type = $request->get('type', 'full'); // full, main, attachments

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
            'voucherRequests.items',
        ])->findOrFail($id);

        $user = Auth::user();

        if (!$this->canViewTicket($user, $ticket)) {
            abort(403, 'Unauthorized access to this ticket report');
        }

        // Buat folder temp
        $this->ensureDirectoryExists(storage_path('app/temp'));

        // Get signatures
        $signatures = $this->getSignatures($ticket);

        // Get PR data
        $prData = null;
        $prItems = collect([]);
        $totalPRAmount = 0;

        if ($ticket->voucherRequests->count() > 0) {
            $prData = $ticket->voucherRequests->first();
            $prItems = $prData->items;
            $totalPRAmount = $prData->total_amount;
        }

        // Get follow-up comments
        $followUpComments = $this->getFollowUpComments($ticket);

        // Filter attachments
        $imageAttachments = $this->getImageAttachments($ticket);
        $fileAttachments = $this->getFileAttachments($ticket);

        $statusDisplay = $this->getStatusDisplay($ticket->status);

        // Data untuk main report
        $mainData = [
            'ticket' => $ticket,
            'signatures' => $signatures,
            'prData' => $prData,
            'prItems' => $prItems,
            'totalPRAmount' => $totalPRAmount,
            'followUpComments' => $followUpComments,
            'fileAttachments' => $fileAttachments,
            'currentDate' => now()->format('d F Y'),
            'currentDateTime' => now()->format('d F Y, H:i'),
            'statusDisplay' => $statusDisplay,
            'helper' => $this,
        ];

        // Data untuk attachments
        $attachmentData = [
            'ticket' => $ticket,
            'imageAttachments' => $imageAttachments,
            'helper' => $this,
        ];

        // Generate berdasarkan type
        switch ($type) {
            case 'attachments':
                // Validasi attachments
                if (count($imageAttachments) == 0) {
                    abort(404, 'No image attachments found');
                }

                $pdfPath = storage_path('app/temp/attachments_' . uniqid() . '.pdf');

                Pdf::view('tickets.report_attachments', $attachmentData)
                    ->format(Format::A4)
                    ->orientation(Orientation::Portrait)
                    ->margins(8, 8, 8, 8)
                    ->save($pdfPath);

                return response()->file($pdfPath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="attachments-' . $ticket->ticket_number . '.pdf"'
                ])->deleteFileAfterSend(true);

            case 'main':
                $pdfPath = storage_path('app/temp/main_' . uniqid() . '.pdf');

                Pdf::view('tickets.report_main', $mainData)
                    ->format(Format::A4)
                    ->orientation(Orientation::Portrait)
                    ->margins(8, 8, 8, 8)
                    ->save($pdfPath);

                return response()->file($pdfPath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="report-' . $ticket->ticket_number . '.pdf"'
                ])->deleteFileAfterSend(true);

            case 'full':
            default:
                $mainPdfPath = storage_path('app/temp/main_' . uniqid() . '.pdf');

                Pdf::view('tickets.report_main', $mainData)
                    ->format(Format::A4)
                    ->orientation(Orientation::Portrait)
                    ->margins(8, 8, 8, 8)
                    ->save($mainPdfPath);

                if (count($imageAttachments) > 0) {
                    $attachmentPdfPath = storage_path('app/temp/attachment_' . uniqid() . '.pdf');

                    Pdf::view('tickets.report_attachments', $attachmentData)
                        ->format(Format::A4)
                        ->orientation(Orientation::Portrait)
                        ->margins(8, 8, 8, 8)
                        ->save($attachmentPdfPath);

                    $outputPath = storage_path('app/temp/final_' . uniqid() . '.pdf');
                    $this->mergePdfs([$mainPdfPath, $attachmentPdfPath], $outputPath);

                    @unlink($mainPdfPath);
                    @unlink($attachmentPdfPath);

                    return response()->file($outputPath, [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'inline; filename="ticket-' . $ticket->ticket_number . '-full.pdf"'
                    ])->deleteFileAfterSend(true);
                }

                return response()->file($mainPdfPath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="ticket-' . $ticket->ticket_number . '-full.pdf"'
                ])->deleteFileAfterSend(true);
        }
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

            if ($isImage && Storage::disk('public')->exists($attachment->file_path)) {
                $imageAttachments[] = $attachment;
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

            if (!$isImage) {
                $fileAttachments[] = $attachment;
            }
        }

        return $fileAttachments;
    }

    /**
     * Get follow-up comments - HANYA yang memiliki flag is_followup = true
     */
    private function getFollowUpComments($ticket)
    {
        $followUps = [];

        foreach ($ticket->comments as $comment) {
            if (!$comment->is_followup)
                continue;

            $text = $comment->comment;

            // Bersihkan teks dari prefix
            $text = preg_replace('/^Work completed by technician\.?\s*/i', '', $text);
            $text = preg_replace('/^Completion Notes:\s*/i', '', $text);
            $text = preg_replace('/^VR Requested\.?\s*/i', '', $text);
            $text = preg_replace('/^Admin Follow-up Notes:\s*/i', '', $text);
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
            'pending_vr' => 'VR APPROVAL',
            'completed' => 'COMPLETED',
            'pending_gm' => 'GM APPROVAL',
            'ready_for_closure' => 'READY FOR CLOSURE',
            'closed' => 'CLOSED',
            'cancelled' => 'CANCELLED',
        ];
        return $statusMap[$status] ?? strtoupper(str_replace('_', ' ', $status));
    }

    /**
     * Merge multiple PDF files into one using FPDI
     */
    private function mergePdfs($inputFiles, $outputFile)
    {
        $pdf = new Fpdi();

        foreach ($inputFiles as $file) {
            if (!file_exists($file)) {
                continue;
            }

            $pageCount = $pdf->setSourceFile($file);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);
            }
        }

        $pdf->Output('F', $outputFile);
    }

    /**
     * Ensure directory exists
     */
    private function ensureDirectoryExists($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }

    /**
     * Save PDF to storage
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
            // Buat folder reports jika belum ada
            $this->ensureDirectoryExists(storage_path('app/public/reports'));
            $this->ensureDirectoryExists(storage_path('app/temp'));

            // Get data
            $signatures = $this->getSignatures($ticket);
            $followUpComments = $this->getFollowUpComments($ticket);
            $imageAttachments = $this->getImageAttachments($ticket);
            $fileAttachments = $this->getFileAttachments($ticket);

            // Main report data
            $mainData = [
                'ticket' => $ticket,
                'signatures' => $signatures,
                'prData' => $ticket->voucherRequests->first(),
                'prItems' => $ticket->voucherRequests->first()?->items ?? collect([]),
                'totalPRAmount' => $ticket->voucherRequests->first()?->total_amount ?? 0,
                'followUpComments' => $followUpComments,
                'fileAttachments' => $fileAttachments,
                'currentDate' => now()->format('d F Y'),
                'statusDisplay' => $this->getStatusDisplay($ticket->status),
                'helper' => $this,
            ];

            // Attachment data
            $attachmentData = [
                'ticket' => $ticket,
                'imageAttachments' => $imageAttachments,
                'helper' => $this,
            ];

            $filename = '';

            switch ($type) {
                case 'attachments':
                    if (count($imageAttachments) == 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'No image attachments found'
                        ], 404);
                    }

                    $pdfPath = storage_path('app/temp/attachments_' . uniqid() . '.pdf');

                    Pdf::view('tickets.report_attachments', $attachmentData)
                        ->format(Format::A4)
                        ->orientation(Orientation::Portrait)
                        ->margins(8, 8, 8, 8)
                        ->save($pdfPath);

                    $filename = 'reports/attachments-' . $ticket->ticket_number . '-' . time() . '.pdf';
                    copy($pdfPath, storage_path('app/public/' . $filename));
                    @unlink($pdfPath);
                    break;

                case 'main':
                    $pdfPath = storage_path('app/temp/main_' . uniqid() . '.pdf');

                    Pdf::view('tickets.report_main', $mainData)
                        ->format(Format::A4)
                        ->orientation(Orientation::Portrait)
                        ->margins(8, 8, 8, 8)
                        ->save($pdfPath);

                    $filename = 'reports/report-' . $ticket->ticket_number . '-' . time() . '.pdf';
                    copy($pdfPath, storage_path('app/public/' . $filename));
                    @unlink($pdfPath);
                    break;

                case 'full':
                default:
                    $mainPdfPath = storage_path('app/temp/main_' . uniqid() . '.pdf');

                    Pdf::view('tickets.report_main', $mainData)
                        ->format(Format::A4)
                        ->orientation(Orientation::Portrait)
                        ->margins(8, 8, 8, 8)
                        ->save($mainPdfPath);

                    if (count($imageAttachments) > 0) {
                        $attachmentPdfPath = storage_path('app/temp/attachment_' . uniqid() . '.pdf');

                        Pdf::view('tickets.report_attachments', $attachmentData)
                            ->format(Format::A4)
                            ->orientation(Orientation::Portrait)
                            ->margins(8, 8, 8, 8)
                            ->save($attachmentPdfPath);

                        $outputPath = storage_path('app/temp/final_' . uniqid() . '.pdf');
                        $this->mergePdfs([$mainPdfPath, $attachmentPdfPath], $outputPath);

                        @unlink($mainPdfPath);
                        @unlink($attachmentPdfPath);

                        $filename = 'reports/ticket-' . $ticket->ticket_number . '-full-' . time() . '.pdf';
                        copy($outputPath, storage_path('app/public/' . $filename));
                        @unlink($outputPath);
                    } else {
                        $filename = 'reports/ticket-' . $ticket->ticket_number . '-full-' . time() . '.pdf';
                        copy($mainPdfPath, storage_path('app/public/' . $filename));
                        @unlink($mainPdfPath);
                    }
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => 'Report saved successfully',
                'path' => $filename,
                'url' => Storage::url($filename)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method untuk mendapatkan nama user
     */
    public function getUserName($user, $fallback = '-')
    {
        if (!$user)
            return $fallback;
        return $user->name ?? $fallback;
    }

    /**
     * Helper method untuk mendapatkan role user
     */
    public function getUserRole($user, $fallback = '-')
    {
        if (!$user)
            return $fallback;
        return ucfirst($user->role) ?? $fallback;
    }

    /**
     * Helper method untuk format tanggal
     */
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

    /**
     * Helper method untuk cek apakah signature ada
     */
    public function hasSignature($stage, $signatures)
    {
        if (!isset($signatures[$stage]))
            return false;

        $signature = $signatures[$stage];
        if (!$signature->signature_path)
            return false;

        return Storage::disk('public')->exists($signature->signature_path);
    }

    /**
     * Helper method untuk mendapatkan signature path
     */
    public function getSignaturePath($stage, $signatures, $fallback = null)
    {
        if ($this->hasSignature($stage, $signatures)) {
            return storage_path('app/public/' . $signatures[$stage]->signature_path);
        }
        return $fallback;
    }

    /**
     * Helper untuk mendapatkan data signature
     */
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

    /**
     * Check if user can view ticket
     */
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

    /**
     * Clean up old temp files (bisa dijalankan via cron)
     */
    public function cleanupTempFiles()
    {
        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir))
            return;

        $files = glob($tempDir . '/*.pdf');
        $now = time();

        foreach ($files as $file) {
            // Hapus file yang lebih dari 1 jam
            if (is_file($file) && ($now - filemtime($file)) > 3600) {
                @unlink($file);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Temp files cleaned up'
        ]);
    }
}
