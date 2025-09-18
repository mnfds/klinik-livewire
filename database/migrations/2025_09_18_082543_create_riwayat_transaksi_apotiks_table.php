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
        Schema::create('riwayat_transaksi_apotiks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksi_apotiks')->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produk_dan_obats')->onDelete('cascade');
            $table->integer('jumlah_produk')->nullable();
            $table->integer('potongan')->nullable();
            $table->integer('diskon')->nullable();
            $table->integer('subtotal')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_transaksi_apotiks');
    }
};
