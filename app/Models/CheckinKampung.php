<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckinKampung extends Model
{
    use HasFactory;

    protected $table = 'checkin_kampung';

    protected $fillable = [
        'kampung_olahraga_id', 'prasarana_id', 'club_id', 'nama_peserta', 'umur',
        'jenis_olahraga_id', 'jenis_olahraga_nama', 'foto',
    ];

    protected $casts = [
        'umur' => 'integer',
    ];

    public function kampung(): BelongsTo
    {
        return $this->belongsTo(KampungOlahraga::class, 'kampung_olahraga_id');
    }

    public function jenisOlahraga(): BelongsTo
    {
        return $this->belongsTo(JenisOlahraga::class);
    }

    public function prasarana(): BelongsTo
    {
        return $this->belongsTo(Prasarana::class, 'prasarana_id');
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'club_id');
    }
}
