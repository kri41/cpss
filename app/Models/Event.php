<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_event',
        'tingkat',
        'tanggal_mulai',
        'tanggal_selesai',
        'deskripsi_kegiatan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    /**
     * Relasi ke User (Admin/Relawan yang membuat event)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope untuk filter berdasarkan tingkat event
     */
    public function scopeTingkat($query, $tingkat)
    {
        return $query->where('tingkat', $tingkat);
    }

    /**
     * Scope untuk event yang akan datang
     */
    public function scopeAkanDatang($query)
    {
        return $query->where('tanggal_mulai', '>=', now()->format('Y-m-d'))
                     ->orderBy('tanggal_mulai', 'asc');
    }

    /**
     * Scope untuk event yang sedang berlangsung
     */
    public function scopeBerlangsung($query)
    {
        return $query->where('tanggal_mulai', '<=', now()->format('Y-m-d'))
                     ->where(function($q) {
                         $q->whereNull('tanggal_selesai')
                           ->orWhere('tanggal_selesai', '>=', now()->format('Y-m-d'));
                     });
    }

    /**
     * Accessor untuk status event
     */
    public function getStatusAttribute(): string
    {
        $now = now()->format('Y-m-d');
        
        if ($this->tanggal_mulai > $now) {
            return 'Akan Datang';
        }
        
        if ($this->tanggal_selesai && $this->tanggal_selesai < $now) {
            return 'Selesai';
        }
        
        return 'Berlangsung';
    }
}
