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
        Schema::create('bahan_racikan_r_m_s', function (Blueprint $table) {
            $table->id();
            // Relasi ke obat racikan
            $table->foreignId('obat_racikan_id')->constrained('obat_racikan_r_m_s')->onDelete('cascade');

            $table->text('nama_obat_racikan')->nullable();
            $table->integer('jumlah_obat_racikan')->nullable();
            $table->text('satuan_obat_racikan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahan_racikan_r_m_s');
    }
};
