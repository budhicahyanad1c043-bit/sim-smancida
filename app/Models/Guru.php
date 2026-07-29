<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_guru',
        'nip',
        'nik',
        'gender',
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
     * Relasi ke Model Kelas (sebagai Wali Kelas)
     */
    public function kelasDiampu()
    {
        return $this->hasOne(Kelas::class, 'walikelas_id');
    }
}