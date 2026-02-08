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
        Schema::create('bahan_bakus', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode')->unique()->nullable();
            $table->integer('stok_besar')->default(0);
            $table->string('satuan_besar')->default('Box');
            $table->integer('pengali')->nullable();
            $table->integer('stok_kecil')->default(0);
            $table->string('satuan_kecil')->default('Pcs');
            $table->string('lokasi')->nullable(); // lokasi tujuan/asal
            $table->date('expired_at')->nullable();
            $table->integer('reminder')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahan_bakus');
    }
};
