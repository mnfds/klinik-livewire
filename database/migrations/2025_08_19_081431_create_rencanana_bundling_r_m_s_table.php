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
        Schema::create('rencanana_bundling_r_m_s', function (Blueprint $table) {
            $table->id();
            // Relasi ke rekam medis
            $table->foreignId('rekam_medis_id')->constrained('rekam_medis')->onDelete('cascade');

            $table->foreignId('bundling_id')->constrained('bundlings')->onDelete('cascade');
            $table->integer('jumlah_bundling');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rencanana_bundling_r_m_s');
    }
};
