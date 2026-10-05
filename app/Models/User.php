<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_agent_id');
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    public function isAgent(): bool
    {
        return $this->role === 'agent';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function canManageTickets(): bool
    {
        return in_array($this->role, ['agent', 'manager'], true);
    }
}
