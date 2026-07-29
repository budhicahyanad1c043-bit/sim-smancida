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
        Schema::create('jadwal_pelajarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelases')->cascadeOnDelete();
            $table->foreignId('mapel_id')->constrained('mata_pelajarans')->cascadeOnDelete();
            $table->foreignId('guru_id')->nullable()->constrained('gurus')->nullOnDelete();
            $table->foreignId('jam_pelajaran_id')->constrained('jam_pelajarans')->cascadeOnDelete();
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']);
            $table->timestamps();

            // Mencegah bentrok jadwal guru di jam & hari yang sama
            $table->unique(['guru_id', 'hari', 'jam_pelajaran_id'], 'unique_guru_jadwal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_pelajarans');
    }
};
