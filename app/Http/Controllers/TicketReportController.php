<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Signature;
use App\Models\TicketComment;
use App\Models\VoucherRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\Enums\Format;
use Spatie\LaravelPdf\Enums\Orientation;
use Illuminate\Support\Str;

class TicketReportController extends Controller
{
    /**
     * Generate PDF report for ticket
     */
    public function generateReport($id)
    {
        $ticket = Ticket::with([
            'user',
            'category',
            'priority',
            'location',
            'department',
            'assignedUser',
            'attachments',
            'comments.user',
            'signatures.user',
            'voucherRequests.items',
            'approval'
        ])->findOrFail($id);

        $user = Auth::user();

        // Check permission
        if (!$this->canViewTicket($user, $ticket)) {
            abort(403, 'Unauthorized access to this ticket report');
        }

        // Get signatures grouped by stage dengan validasi
        $signatures = [];
        foreach ($ticket->signatures as $signature) {
            if ($signature->stage && $signature->user) {
                $signatures[$signature->stage] = $signature;
            }
        }

        // Get PR data if exists (Voucher Request)
        $prData = null;
        $prItems = [];
        $totalPRAmount = 0;

        if ($ticket->voucherRequests->count() > 0) {
            $prData = $ticket->voucherRequests->first();
            $prItems = $prData->items;
            $totalPRAmount = $prData->total_amount;
        }

        // Get follow-up comments dari completion notes
        $followUpComments = $this->getFollowUpComments($ticket);

        // Filter attachments
        $imageAttachments = [];
        $fileAttachments = [];

        foreach ($ticket->attachments as $attachment) {
            $extension = strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION));
            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);

            if ($isImage) {
                $imageAttachments[] = $attachment;
            } else {
                $fileAttachments[] = $attachment;
            }
        }

        // Get status display text
        $statusDisplay = $this->getStatusDisplay($ticket->status);

        // Format data untuk PDF
        $data = [
            'ticket' => $ticket,
            'signatures' => $signatures,
            'prData' => $prData,
            'prItems' => $prItems,
            'totalPRAmount' => $totalPRAmount,
            'followUpComments' => $followUpComments,
            'fileAttachments' => $fileAttachments,
            'imageAttachments' => $imageAttachments,
            'currentDate' => now()->format('d F Y'),
            'currentDateTime' => now()->format('d F Y, H:i'),
            'hasImages' => count($imageAttachments) > 0,
            'statusDisplay' => $statusDisplay,
            'helper' => $this,
        ];

        // Generate PDF dengan Spatie
        return Pdf::view('tickets.report', $data)
            ->format(Format::A4)
            ->orientation(Orientation::Portrait)
            ->margins(8, 10, 8, 10)
            ->name('ticket-' . $ticket->ticket_number . '-report.pdf')
            ->download();
    }

    /**
     * Get follow-up comments dari completion notes
     */
    private function getFollowUpComments($ticket)
    {
        $comments = $ticket->comments()
            ->where('is_internal', 0)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $followUps = [];

        foreach ($comments as $comment) {
            // Cek apakah ini completion note dari technician
            if (
                str_contains($comment->comment, 'Work completed by technician') ||
                str_contains($comment->comment, 'Completion Notes:')
            ) {

                // Ekstrak notes dari comment
                $text = $comment->comment;
                $text = preg_replace('/Work completed by technician\.?\s*/i', '', $text);
                $text = preg_replace('/Completion Notes:\s*/i', '', $text);
                $text = trim($text);

                if (!empty($text)) {
                    $followUps[] = [
                        'user' => $comment->user->name ?? 'Technician',
                        'date' => $comment->created_at->format('d/m H:i'),
                        'text' => Str::limit($text, 100)
                    ];
                }
            }
            // Jika tidak ada completion notes, ambil 3 comment biasa
            elseif (count($followUps) < 3) {
                $followUps[] = [
                    'user' => $comment->user->name ?? 'User',
                    'date' => $comment->created_at->format('d/m H:i'),
                    'text' => Str::limit($comment->comment, 70)
                ];
            }
        }

        // Pastikan selalu ada 3 baris
        while (count($followUps) < 3) {
            $followUps[] = [
                'user' => '-',
                'date' => '',
                'text' => ''
            ];
        }

        return $followUps;
    }

    /**
     * View PDF in browser
     */
    public function viewReport($id)
    {
        $ticket = Ticket::with([
            'user',
            'category',
            'priority',
            'location',
            'department',
            'assignedUser',
            'attachments',
            'comments.user',
            'signatures.user',
            'voucherRequests.items',
        ])->findOrFail($id);

        $user = Auth::user();

        if (!$this->canViewTicket($user, $ticket)) {
            abort(403, 'Unauthorized access to this ticket report');
        }

        // Get signatures dengan validasi
        $signatures = [];
        foreach ($ticket->signatures as $signature) {
            if ($signature->stage && $signature->user) {
                $signatures[$signature->stage] = $signature;
            }
        }

        $prData = null;
        $prItems = [];
        $totalPRAmount = 0;

        if ($ticket->voucherRequests->count() > 0) {
            $prData = $ticket->voucherRequests->first();
            $prItems = $prData->items;
            $totalPRAmount = $prData->total_amount;
        }

        // Get follow-up comments
        $followUpComments = $this->getFollowUpComments($ticket);

        $statusDisplay = $this->getStatusDisplay($ticket->status);

        $data = [
            'ticket' => $ticket,
            'signatures' => $signatures,
            'prData' => $prData,
            'prItems' => $prItems,
            'totalPRAmount' => $totalPRAmount,
            'followUpComments' => $followUpComments,
            'currentDate' => now()->format('d F Y'),
            'currentDateTime' => now()->format('d F Y, H:i'),
            'statusDisplay' => $statusDisplay,
            'helper' => $this,
        ];

        return Pdf::view('tickets.report', $data)
            ->format(Format::A4)
            ->orientation(Orientation::Portrait)
            ->margins(8, 10, 8, 10)
            ->name('ticket-' . $ticket->ticket_number . '-report.pdf')
            ->inline();
    }

    /**
     * Helper method untuk mendapatkan display status
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
     * Helper method untuk mendapatkan nama user dengan fallback
     */
    public function getUserName($user, $fallback = '-')
    {
        if (!$user)
            return $fallback;
        return $user->name ?? $fallback;
    }

    /**
     * Helper method untuk mendapatkan role user dengan fallback
     */
    public function getUserRole($user, $fallback = '-')
    {
        if (!$user)
            return $fallback;
        return ucfirst($user->role) ?? $fallback;
    }

    /**
     * Helper method untuk format tanggal dengan fallback
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
     * Helper method untuk mendapatkan signature path dengan validasi
     */
    public function getSignaturePath($stage, $signatures, $fallback = null)
    {
        if ($this->hasSignature($stage, $signatures)) {
            return storage_path('app/public/' . $signatures[$stage]->signature_path);
        }
        return $fallback;
    }

    /**
     * Helper untuk mendapatkan data signature dengan aman
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
     * Save PDF to storage
     */
    public function saveReport($id)
    {
        // ... (kode sama seperti sebelumnya)
    }

    /**
     * Check if user can view ticket
     */
    private function canViewTicket($user, $ticket)
    {
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
}
