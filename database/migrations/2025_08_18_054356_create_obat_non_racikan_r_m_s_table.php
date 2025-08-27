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
        Schema::create('obat_non_racikan_r_m_s', function (Blueprint $table) {
            $table->id();
            // Relasi ke rekam medis
            $table->foreignId('rekam_medis_id')->constrained('rekam_medis')->onDelete('cascade');
            
            $table->text('nama_obat_non_racikan')->nullable();
            $table->integer('jumlah_obat_non_racikan')->nullable();
            $table->text('satuan_obat_non_racikan')->nullable();
            $table->text('dosis_obat_non_racikan')->nullable();
            $table->text('hari_obat_non_racikan')->nullable();
            $table->text('aturan_pakai_obat_non_racikan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obat_non_racikan_r_m_s');
    }
};
