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
        Schema::create('pemeriksaan_fisik_r_m_s', function (Blueprint $table) {
            $table->id();
            // Relasi ke rekam medis
            $table->foreignId('rekam_medis_id')->constrained('rekam_medis')->onDelete('cascade');

            // Data pemeriksaan fisik
            $table->decimal('tinggi_badan', 5, 2)->nullable(); // Contoh: 170.50 cm
            $table->decimal('berat_badan', 5, 2)->nullable();  // Contoh: 60.25 kg
            $table->decimal('imt', 5, 2)->nullable();          // Contoh: 21.42

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_fisik_r_m_s');
    }
};
