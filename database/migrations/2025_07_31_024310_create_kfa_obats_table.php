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
        Schema::create('kfa_obats', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kfa_aktual', 20)->nullable();
            $table->text('nama_obat_aktual')->nullable();
            $table->string('kode_kfa_virtual', 20)->nullable();
            $table->text('nama_obat_virtual')->nullable();
            $table->string('bahan_baku', 100)->nullable();
            $table->decimal('jumlah_bahan_baku', 10, 2)->nullable();
            $table->string('satuan_bahan_baku', 20)->nullable();
            $table->string('bentuk_sediaan', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obat_kfa');
    }
};
