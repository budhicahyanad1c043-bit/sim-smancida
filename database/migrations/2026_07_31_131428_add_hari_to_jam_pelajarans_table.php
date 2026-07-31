<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jam_pelajarans', function (Blueprint $table) {
            $table->enum('hari', ['Lainnya', 'Jumat'])->default('Lainnya')->after('jam_ke');
            
            // Mengubah index unik agar jam_ke boleh sama asal beda kategori hari
            $table->unique(['jam_ke', 'hari'], 'unique_jam_ke_hari');
        });
    }

    public function down(): void
    {
        Schema::table('jam_pelajarans', function (Blueprint $table) {
            $table->dropUnique('unique_jam_ke_hari');
            $table->dropColumn('hari');
        });
    }
};