<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomponenSyarat extends Model
{
    use HasFactory;

    protected $table = 'komponen_syarat';

    protected $fillable = ['nama', 'deskripsi', 'target_checkin', 'poin', 'urutan', 'aktif'];

    protected $casts = [
        'aktif'          => 'boolean',
        'target_checkin' => 'integer',
        'poin'           => 'integer',
        'urutan'         => 'integer',
    ];
}
