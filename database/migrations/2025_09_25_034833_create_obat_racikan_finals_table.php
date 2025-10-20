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
        Schema::create('obat_racikan_finals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obat_final_id')->constrained('obat_finals')->onDelete('cascade');
            $table->string('nama_racikan')->nullable();
            $table->integer('jumlah_racikan')->nullable();
            $table->string('satuan_racikan')->nullable();
            $table->string('total_racikan')->nullable();
            $table->string('dosis')->nullable();
            $table->string('hari')->nullable();
            $table->string('aturan_pakai')->nullable();
            $table->string('metode_racikan')->nullable();
            $table->string('konfirmasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obat_racikan_finals');
    }
};
