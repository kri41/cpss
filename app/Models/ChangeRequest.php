<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ChangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'changeable_type', 'changeable_id',
        'alasan', 'status', 'catatan_admin',
        'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function changeable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function typeLabel(): string
    {
        return match ($this->changeable_type) {
            'prasarana' => 'Prasarana',
            'club' => 'Klub/Komunitas',
            'event' => 'Event',
            default => ucfirst($this->changeable_type),
        };
    }
}
