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
        Schema::create('bahan_racikan_finals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obat_racikan_final_id')->constrained('obat_racikan_finals')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produk_dan_obats')->onDelete('restrict');
            $table->integer('jumlah_obat')->nullable();
            $table->string('satuan_obat')->nullable();
            $table->unsignedBigInteger('harga_obat')->nullable();
            $table->unsignedBigInteger('total_obat')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahan_racikan_finals');
    }
};
