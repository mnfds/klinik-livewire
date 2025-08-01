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
        Schema::create('pemeriksaan_fisiks', function (Blueprint $table) {
            $table->id();

            // Relasi ke kajian_awals
            $table->foreignId('kajian_awal_id')->constrained('kajian_awals')->onDelete('cascade');

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
        Schema::dropIfExists('pemeriksaan_fisiks');
    }
};
