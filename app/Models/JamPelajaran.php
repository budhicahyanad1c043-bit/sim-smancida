<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JamPelajaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'jam_ke',
        'hari',
        'nama',
        'jam_mulai',
        'jam_selesai',
    ];
}