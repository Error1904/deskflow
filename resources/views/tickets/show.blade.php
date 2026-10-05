@extends('layouts.app')

@section('title', $ticket->ticket_number)

@section('content')
@php
    $statusColors = ['open' => 'primary', 'in_progress' => 'warning', 'escalated' => 'danger', 'resolved' => 'success'];
    $priorityColors = ['low' => 'secondary', 'medium' => 'info', 'high' => 'warning', 'critical' => 'danger'];
@endphp

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('tickets.index') }}">Tickets</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $ticket->ticket_number }}</li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $ticket->subject }}</h5>
                <span class="badge rounded-pill bg-{{ $statusColors[$ticket->status] }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
            </div>
            <div class="card-body">
                <p class="text-secondary">{{ $ticket->description }}</p>
                <hr>
                <div id="comment-thread">
                    @forelse ($ticket->comments as $comment)
                        @include('tickets.partials.comment', ['comment' => $comment, 'ticket' => $ticket])
                    @empty
                        <p id="no-comments" class="text-muted mb-0">No comments yet. Start the conversation below.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-chat-left-text"></i> Add a comment</h6>
            </div>
            <div class="card-body">
                <form id="comment-form" data-url="{{ route('tickets.comments.store', $ticket) }}">
                    @csrf
                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment</label>
                        <textarea name="comment" id="comment" class="form-control" rows="4" maxlength="5000" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="attachments" class="form-label">Attachments</label>
                        <input type="file" name="attachments[]" id="attachments" class="form-control" multiple accept=".jpg,.jpeg,.png,.pdf">
                        <div class="form-text">Allowed: JPG, PNG, PDF only. Maximum 2MB per file, up to 5 files.</div>
                    </div>
                    <div id="form-errors" class="alert alert-danger d-none"></div>
                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        <i class="bi bi-send"></i> Post Comment
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Ticket details</h6>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">Ticket #</span>
                    <strong>{{ $ticket->ticket_number }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">Department</span>
                    <strong>{{ $ticket->department }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">Priority</span>
                    <span class="badge rounded-pill bg-{{ $priorityColors[$ticket->priority] }}">{{ ucfirst($ticket->priority) }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">Status</span>
                    <span class="badge rounded-pill bg-{{ $statusColors[$ticket->status] }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">Reported by</span>
                    <strong>{{ $ticket->user->name }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">Assigned agent</span>
                    <strong>{{ $ticket->agent ? $ticket->agent->name : 'Unassigned' }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">Created</span>
                    <strong>{{ $ticket->created_at->format('M d, Y H:i') }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">Last update</span>
                    <strong>{{ $ticket->updated_at->diffForHumans() }}</strong>
                </li>
            </ul>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-paperclip"></i> Activity</h6>
            </div>
            <div class="card-body">
                <p class="card-text small">
                    <strong id="comment-count">{{ $ticket->comments->count() }}</strong> comment(s) &bull;
                    <strong>{{ $ticket->comments->sum(function ($c) { return $c->attachments->count(); }) }}</strong> attachment(s)
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/ticket.js') }}"></script>
@endpush
