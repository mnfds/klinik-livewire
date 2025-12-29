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
        Schema::create('transaksi_kliniks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rekam_medis_id')->constrained('rekam_medis')->onDelete('cascade');
            $table->string('no_transaksi')->unique();
            $table->dateTime('tanggal_transaksi')->default(now());
            $table->unsignedBigInteger('total_tagihan')->default(0);
            $table->unsignedBigInteger('diskon')->nullable()->default(0);
            $table->unsignedBigInteger('potongan')->nullable()->default(0);
            $table->enum('status', ['lunas', 'belum_bayar', 'batal'])->default('belum_bayar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_kliniks');
    }
};
