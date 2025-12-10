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
        Schema::create('obat_racikan_r_m_s', function (Blueprint $table) {
            $table->id();
            // Relasi ke rekam medis
            $table->foreignId('rekam_medis_id')->constrained('rekam_medis')->onDelete('cascade');

            $table->text('nama_racikan')->nullable();
            $table->integer('jumlah_racikan')->nullable();
            $table->text('satuan_racikan')->nullable();
            $table->text('dosis_obat_racikan')->nullable();
            $table->text('hari_obat_racikan')->nullable();
            $table->text('aturan_pakai_racikan')->nullable();
            $table->text('metode_racikan')->nullable();
            $table->text('medication_id')->nullable();
            $table->text('medication_request_id')->nullable();
            $table->text('medication_dispense_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obat_racikan_r_m_s');
    }
};
