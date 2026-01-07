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
        Schema::create('riwayat_transaksi_kliniks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_klinik_id')->constrained('transaksi_kliniks')->onDelete('cascade');
            $table->enum('jenis_item', ['produk', 'pelayanan', 'treatment', 'bundling', 'obat_non_racik', 'obat_racik', 'produk_tambahan']);
            $table->string('nama_item');
            $table->integer('qty')->default(1);
            $table->unsignedBigInteger('harga')->default(0);
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_transaksi_kliniks');
    }
};
