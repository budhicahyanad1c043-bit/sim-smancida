<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturans', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert nilai default titik lokasi sekolah & radius
        DB::table('pengaturans')->insert([
            ['key' => 'latitude_sekolah', 'value' => '-6.200000', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'longitude_sekolah', 'value' => '106.816666', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'radius_meter', 'value' => '100', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturans');
    }
};