<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('kelas_id')->nullable()->constrained('kelases')->nullOnDelete();
            $table->string('nama_siswa');
            $table->enum('gender', ['L', 'P']);
            $table->string('nisn',10)->nullable()->unique();
            $table->string('nik',16)->nullable()->unique();
            $table->string('nis',10)->nullable()->unique();
            $table->string('tempat_lahir')->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('agama')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('nik_ibu',16)->nullable();
            $table->string('nama_ayah')->nullable();
            $table->string('nik_ayah',16)->nullable();
            $table->text('alamat')->nullable();
            $table->string('desa')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kode_pos')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
