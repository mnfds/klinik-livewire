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
        Schema::create('produk_bundling_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rekam_medis_id')->constrained('rekam_medis')->onDelete('cascade');
            $table->boolean('is_pembelian_baru')->default(false);
            $table->foreignId('bundling_id')->constrained('bundlings')->cascadeOnDelete();
            $table->foreignId('produk_obat_id')->constrained('produk_dan_obats')->cascadeOnDelete();
            $table->integer('jumlah_dipakai')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_bundling_usages');
    }
};
