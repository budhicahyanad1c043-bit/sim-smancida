<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelases';

    protected $fillable = [
        'nama_kelas',
        'tahun_ajaran',
        'walikelas_id',
    ];

    /**
     * Relasi ke Wali Kelas (Model Guru)
     */
    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'walikelas_id');
    }

    /**
     * Relasi ke Siswa
     */
    public function siswas()
    {
        return $this->hasMany(Siswa::class);
    }
}