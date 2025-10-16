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
        Schema::create('reservasis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pasien_id')->constrained('pasiens')->cascadeOnDelete();
            $table->foreignId('poli_id')->constrained('poli_kliniks')->cascadeOnDelete();
            $table->foreignId('dokter_id')->nullable()->constrained('dokters')->nullOnDelete();
            $table->date('tanggal_reservasi');
            $table->time('jam_reservasi')->nullable();
            $table->enum('status', ['belum bayar', 'belum lunas', 'lunas', 'selesai', 'batal'])->default('belum bayar');
            $table->unsignedBigInteger('nominal_pembayaran')->nullable();
            $table->text('catatan')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservasis');
    }
};
