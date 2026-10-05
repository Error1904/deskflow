<div class="comment mb-3" id="comment-{{ $comment->id }}">
    <div class="d-flex">
        <div class="flex-shrink-0">
            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                {{ strtoupper(substr($comment->user->name, 0, 1)) }}
            </div>
        </div>
        <div class="flex-grow-1 ms-3">
            <div class="bg-white border rounded p-3 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong class="small">{{ $comment->user->name }}</strong>
                    <small class="text-muted" title="{{ $comment->created_at->format('M d, Y H:i') }}">{{ $comment->created_at->diffForHumans() }}</small>
                </div>
                <p class="mb-0 text-break">{{ $comment->comment }}</p>
                @if ($comment->attachments->count())
                    <div class="mt-2">
                        @foreach ($comment->attachments as $attachment)
                            <a href="{{ route('tickets.attachments.download', [$ticket, $attachment]) }}"
                               class="badge bg-light text-dark border me-1 mb-1 text-decoration-none"
                               title="Download ({{ $attachment->sizeInKb() }} KB)">
                                <i class="bi bi-{{ $attachment->isImage() ? 'image' : 'file-earmark-pdf' }}"></i>
                                {{ $attachment->file_name }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
