<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TicketDetailController extends Controller
{
    /**
     * Display ticket detail
     */
    public function show($id)
    {
        $ticket = Ticket::with([
            'user',
            'category',
            'priority',
            'location',
            'assignedUser',
            'attachments',
            'comments.user',
            'comments.attachments',
            'activities.user'
        ])->findOrFail($id);

        // Check permission
        $canView = auth()->user()->role === 'admin'
            || $ticket->user_id === auth()->id()
            || $ticket->assigned_to === auth()->id();

        if (!$canView) {
            abort(403, 'Unauthorized access to this ticket');
        }

        // Get assignable users (for admin)
        $assignableUsers = [];
        if (auth()->user()->role === 'admin') {
            $assignableUsers = User::whereNotNull('department_id')
                ->where('status', 'active')
                ->get();
        }

        return view('tickets.show', compact('ticket', 'assignableUsers'));
    }

    /**
     * Add comment to ticket
     */
    public function addComment(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string',
            'is_internal' => 'boolean',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        $ticket = Ticket::findOrFail($id);

        DB::beginTransaction();
        try {
            // Create comment
            $comment = TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => auth()->id(),
                'comment' => $request->comment,
                'is_internal' => $request->is_internal ?? 0,
            ]);

            // Handle attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('comment_attachments', $fileName, 'public');

                    $comment->attachments()->create([
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'ticket_id' => $ticket->id,
                'action' => 'commented',
                'description' => auth()->user()->name . ' added a comment',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'comment' => $comment->load('user', 'attachments')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add comment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update ticket status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,pending,resolved,closed,cancelled'
        ]);

        $ticket = Ticket::findOrFail($id);
        $oldStatus = $ticket->status;

        DB::beginTransaction();
        try {
            $ticket->update([
                'status' => $request->status,
                'resolved_at' => $request->status === 'resolved' ? now() : null,
                'closed_at' => $request->status === 'closed' ? now() : null,
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'ticket_id' => $ticket->id,
                'action' => 'status_changed',
                'description' => "Status changed from {$oldStatus} to {$request->status}",
                'old_values' => json_encode(['status' => $oldStatus]),
                'new_values' => json_encode(['status' => $request->status]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ticket status updated successfully'
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
     * Assign ticket to user (Admin only)
     */
    public function assignTicket(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'assigned_to' => 'required|exists:users,id'
        ]);

        $ticket = Ticket::findOrFail($id);
        $oldAssignee = $ticket->assigned_to;

        DB::beginTransaction();
        try {
            $ticket->update([
                'assigned_to' => $request->assigned_to,
                'status' => $ticket->status === 'open' ? 'in_progress' : $ticket->status
            ]);

            $assignedUser = User::find($request->assigned_to);

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'ticket_id' => $ticket->id,
                'action' => 'assigned',
                'description' => "Ticket assigned to {$assignedUser->name}",
                'old_values' => json_encode(['assigned_to' => $oldAssignee]),
                'new_values' => json_encode(['assigned_to' => $request->assigned_to]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Ticket assigned to {$assignedUser->name}",
                'assigned_user' => $assignedUser
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete ticket (Admin only)
     */
    public function destroy($id)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $ticket = Ticket::findOrFail($id);

        DB::beginTransaction();
        try {
            // Delete attachments from storage
            foreach ($ticket->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            foreach ($ticket->comments as $comment) {
                foreach ($comment->attachments as $attachment) {
                    Storage::disk('public')->delete($attachment->file_path);
                }
            }

            // Soft delete ticket (will cascade to related records)
            $ticket->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ticket deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete ticket: ' . $e->getMessage()
            ], 500);
        }
    }
}
