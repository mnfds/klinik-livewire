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
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode')->unique()->nullable();
            $table->string('satuan')->default('pcs');
            $table->integer('stok')->default(0);
            $table->unsignedBigInteger('harga_dasar');
            $table->unsignedBigInteger('diskon')->nullable();
            $table->unsignedBigInteger('potongan')->nullable();
            $table->unsignedBigInteger('harga_bersih')->nullable();
            $table->string('lokasi')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
