<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'comment_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'file_size' => 'integer',
    ];

    public function comment()
    {
        return $this->belongsTo(TicketComment::class);
    }

    public function sizeInKb(): float
    {
        return round($this->file_size / 1024, 1);
    }

    public function isImage(): bool
    {
        return str_starts_with((string) $this->file_type, 'image/');
    }
}
