<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisOlahraga extends Model
{
    use HasFactory;

    protected $table = 'jenis_olahraga';

    protected $fillable = ['nama', 'kategori', 'aktif'];

    protected $casts = [
        'aktif' => 'boolean',
    ];
}
