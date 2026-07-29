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
        Schema::create('jam_pelajarans', function (Blueprint $table) {
            $table->id();
            $table->integer('jam_ke'); // Contoh: 1, 2, 3
            $table->time('jam_mulai'); // Contoh: 07:00
            $table->time('jam_selesai'); // Contoh: 07:45
            $table->string('keterangan')->nullable(); // Contoh: KBM / Istirahat
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jam_pelajarans');
    }
};
