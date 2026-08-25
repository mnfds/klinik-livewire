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
        Schema::create('transaksi_apotiks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->nullable()->constrained('pasiens')->onDelete('cascade');
            $table->string('no_transaksi')->unique();
            $table->string('kasir_nama');
            $table->dateTime('tanggal')->default(now());
            $table->integer('total_harga');
            $table->enum('metode_pembayaran', ['Tunai','Qris', 'Shopeepay', 'Mandiri', 'BCA', 'BRI', 'BNI']);
            $table->unsignedBigInteger('diskon')->nullable()->default(0);
            $table->unsignedBigInteger('potongan')->nullable()->default(0);
            $table->unsignedBigInteger('total_tagihan_bersih')->default(0);
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_apotiks');
    }
};
