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
        Schema::create('produk_dan_obats', function (Blueprint $table) {
            $table->id();
            $table->string('nama_dagang');
            $table->string('kode')->unique();
            $table->string('sediaan');
            $table->unsignedBigInteger('harga_dasar');
            $table->unsignedBigInteger('diskon')->nullable();
            $table->unsignedBigInteger('harga_bersih')->nullable();
            $table->integer('stok');
            $table->date('expired_at')->nullable();
            $table->string('batch')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('supplier')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_dan_obats');
    }
};
