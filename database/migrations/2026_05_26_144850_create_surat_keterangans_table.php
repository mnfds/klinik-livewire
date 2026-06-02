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
        Schema::create('surat_keterangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_terdaftar_id')->constrained('pasien_terdaftars')->onDelete('cascade');
            $table->string('no_surat')->unique();
            $table->date('mulai_berlaku')->nullable();
            $table->date('selesai_berlaku')->nullable();
            $table->enum('tipe_ttd',['digital', 'basah'])->nullable();
            $table->unsignedBigInteger('harga_surat')->nullable();
            $table->enum('jenis_surat', ['standar', 'lengkap', 'sakit'])->default('standar');
            $table->string('sakit')->nullable(); //gejala sakit yang dialami
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_keterangans');
    }
};
