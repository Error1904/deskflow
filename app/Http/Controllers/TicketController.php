<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketCommentRequest;
use App\Models\Attachment;
use App\Models\Ticket;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can.access.ticket')->only(['show', 'addComment', 'downloadAttachment']);
    }

    public function index(Request $request): View
    {
        $tickets = Ticket::with(['user', 'agent'])
            ->filter($request->only(['status', 'priority', 'search']))
            ->when(
                $request->user()->role === 'employee',
                function ($query) use ($request) {
                    $query->where('user_id', $request->user()->id);
                }
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket): View
    {
        $ticket->load(['user', 'agent', 'comments.user', 'comments.attachments']);

        return view('tickets.show', compact('ticket'));
    }

    public function addComment(TicketCommentRequest $request, Ticket $ticket): JsonResponse
    {
        $validated = $request->validated();

        $comment = DB::transaction(function () use ($request, $ticket, $validated) {
            $comment = $ticket->comments()->create([
                'user_id' => Auth::id(),
                'comment' => $validated['comment'],
            ]);

            foreach ($request->file('attachments', []) as $file) {
                $path = $file->store('tickets/' . $ticket->id . '/comments/' . $comment->id, 'private');

                $comment->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }

            return $comment;
        });

        $comment->load(['user', 'attachments']);

        return response()->json([
            'status' => 'success',
            'message' => 'Comment posted successfully.',
            'data' => [
                'comment_id' => $comment->id,
                'comment' => $comment,
                'html' => view('tickets.partials.comment', [
                    'comment' => $comment,
                    'ticket' => $ticket,
                ])->render(),
            ],
        ], 201);
    }

    public function downloadAttachment(Ticket $ticket, Attachment $attachment): BinaryFileResponse
    {
        if ($attachment->comment === null || (int) $attachment->comment->ticket_id !== (int) $ticket->id) {
            abort(404);
        }

        if (!Storage::disk('private')->exists($attachment->file_path)) {
            abort(404, 'The requested attachment no longer exists.');
        }

        return Storage::disk('private')->download($attachment->file_path, $attachment->file_name, [
            'Content-Type' => $attachment->file_type,
            'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
        ]);
    }
}
