<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'kelas_id',
        'nama_siswa',
        'gender',
        'nisn',
        'nik',
        'nis',
        'tempat_lahir',
        'tgl_lahir',
        'agama',
        'nama_ibu',
        'nik_ibu',
        'nama_ayah',
        'nik_ayah',
        'alamat',
        'desa',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'kode_pos',
    ];

    /**
     * Relasi ke Model User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Model Kelas
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}