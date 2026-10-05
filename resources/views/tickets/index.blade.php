@extends('layouts.app')

@section('title', 'Tickets')

@section('content')
@php
    $statusColors = ['open' => 'primary', 'in_progress' => 'warning', 'escalated' => 'danger', 'resolved' => 'success'];
    $priorityColors = ['low' => 'secondary', 'medium' => 'info', 'high' => 'warning', 'critical' => 'danger'];
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-ticket-perforated"></i> Service Tickets</h1>
</div>

<form method="GET" action="{{ route('tickets.index') }}" class="card shadow-sm mb-4">
    <div class="card-body row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label small text-muted">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Ticket number or subject" value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted">Status</label>
            <select name="status" class="form-select">
                <option value="">All statuses</option>
                @foreach (App\Models\Ticket::STATUSES as $status)
                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted">Priority</label>
            <select name="priority" class="form-select">
                <option value="">All priorities</option>
                @foreach (App\Models\Ticket::PRIORITIES as $priority)
                    <option value="{{ $priority }}" {{ request('priority') === $priority ? 'selected' : '' }}>
                        {{ ucfirst($priority) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-grid gap-2 d-md-flex">
            <button type="submit" class="btn btn-dark"><i class="bi bi-funnel"></i> Filter</button>
            <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </div>
</form>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Ticket #</th>
                        <th>Subject</th>
                        <th>Department</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th class="text-end">Updated</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $ticket)
                        <tr>
                            <td class="fw-semibold">
                                <a href="{{ route('tickets.show', $ticket) }}" class="text-decoration-none">{{ $ticket->ticket_number }}</a>
                            </td>
                            <td>
                                <a href="{{ route('tickets.show', $ticket) }}" class="text-decoration-none">
                                    {{ Illuminate\Support\Str::limit($ticket->subject, 55) }}
                                </a>
                                <div class="small text-muted">{{ $ticket->user->name }}</div>
                            </td>
                            <td>{{ $ticket->department }}</td>
                            <td>
                                <span class="badge rounded-pill bg-{{ $priorityColors[$ticket->priority] }}">{{ ucfirst($ticket->priority) }}</span>
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-{{ $statusColors[$ticket->status] }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                            </td>
                            <td class="text-end small text-muted">{{ $ticket->updated_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No tickets found matching your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $tickets->links() }}
</div>
@endsection
