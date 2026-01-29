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
        Schema::create('mutasi_produk_dan_obats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk_dan_obats')->onDelete('cascade');
            $table->enum('tipe', ['masuk', 'keluar']);
            $table->integer('jumlah');
            $table->string('diajukan_oleh')->nullable(); // pegawai yang memasukkan atau megeluarkan barang
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_produk_dan_obats');
    }
};
