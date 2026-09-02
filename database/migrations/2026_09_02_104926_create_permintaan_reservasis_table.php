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
        Schema::create('permintaan_reservasis', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 255);
            $table->string('nik', 255)->nullable();
            $table->string('no_register', 255)->nullable();
            $table->string('no_telp', 255)->nullable();
            $table->text('catatan')->nullable();

            $table->foreignId('poli_id')->constrained('poli_kliniks')->cascadeOnDelete();
            $table->foreignId('dokter_id')->nullable()->constrained('dokters')->nullOnDelete();
            $table->date('tanggal_reservasi');
            $table->time('jam_reservasi')->nullable();
            $table->boolean('pasien_baru')->default(false);
            $table->enum('status', ['menunggu','disetujui', 'ditolak'])->default('menunggu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_reservasis');
    }
};
