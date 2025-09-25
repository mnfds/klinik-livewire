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
            $table->text('nama_obat_racikan')->nullable();
            $table->integer('jumlah_obat_racikan')->nullable();
            $table->text('satuan_obat_racikan')->nullable();
            $table->unsignedBigInteger('harga_obat_racikan')->nullable();
            $table->unsignedBigInteger('subtotal_obat_racikan')->nullable();
            
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
