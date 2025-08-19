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
        Schema::create('data_estetika_r_m_s', function (Blueprint $table) {
            
            // Relasi ke rekam medis
            $table->foreignId('rekam_medis_id')->constrained('rekam_medis')->onDelete('cascade');

            // --- DATA ESTETIKA FIELDS ---
            $table->json('problem_dihadapi')->nullable();
            $table->string('lama_problem')->nullable();
            $table->json('tindakan_sebelumnya')->nullable();
            $table->text('penyakit_dialami')->nullable();
            $table->text('alergi_kosmetik')->nullable();
            
            $table->enum('sedang_hamil', ['ya', 'tidak'])->nullable();
            $table->unsignedTinyInteger('usia_kehamilan')->nullable(); // Dalam bulan

            $table->json('metode_kb')->nullable();
            $table->text('pengobatan_saat_ini')->nullable();
            $table->text('produk_kosmetik')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_estetika_r_m_s');
    }
};
