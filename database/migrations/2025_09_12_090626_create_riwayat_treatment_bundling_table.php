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
        Schema::create('riwayat_treatment_bundling', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('pasiens')->onDelete('cascade');
            $table->foreignId('bundling_id')->constrained('bundlings')->onDelete('cascade');
            $table->foreignId('treatment_bundling_r_m_id')->constrained('treatment_bundling_r_m_s')->onDelete('cascade');
            $table->foreignId('rekam_medis_id')->nullable()->constrained('rekam_medis')->onDelete('set null');
            $table->integer('jumlah_dipakai')->default(0);
            $table->timestamp('tanggal_pakai')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_treatment_bundling_r_m_s');
    }
};
