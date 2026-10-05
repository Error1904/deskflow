<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    public const STATUSES = ['open', 'in_progress', 'escalated', 'resolved'];

    public const PRIORITIES = ['low', 'medium', 'high', 'critical'];

    protected $fillable = [
        'ticket_number',
        'user_id',
        'assigned_agent_id',
        'department',
        'subject',
        'description',
        'priority',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket) {
            if (blank($ticket->ticket_number)) {
                do {
                    $number = 'DF-' . now()->format('Ymd') . '-' . random_int(10000, 99999);
                } while (self::query()->where('ticket_number', $number)->exists());

                $ticket->ticket_number = $number;
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class)->latest();
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeEscalated($query)
    {
        return $query->where('status', 'escalated');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['status'] ?? null, function ($query, $status) {
            $query->where('status', $status);
        });

        $query->when($filters['priority'] ?? null, function ($query, $priority) {
            $query->where('priority', $priority);
        });

        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('ticket_number', 'like', '%' . $search . '%')
                    ->orWhere('subject', 'like', '%' . $search . '%');
            });
        });

        return $query;
    }
}
