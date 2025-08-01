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
        Schema::create('data_kesehatans', function (Blueprint $table) {
            $table->id();

            // Relasi ke kajian_awals
            $table->foreignId('kajian_awal_id')->constrained('kajian_awals')->onDelete('cascade');

            // Kolom data kesehatan
            $table->text('keluhan_utama')->nullable();
            $table->string('status_perokok')->nullable(); // Atau enum('ya', 'tidak') jika fixed

            $table->json('riwayat_penyakit')->nullable();
            $table->json('riwayat_alergi_obat')->nullable();
            $table->json('obat_sedang_dikonsumsi')->nullable();
            $table->json('riwayat_alergi_lainnya')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_kesehatans');
    }
};
