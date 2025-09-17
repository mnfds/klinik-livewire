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
        Schema::create('treatment_bundling_r_m_s', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('pasiens')->onDelete('cascade');
            $table->foreignId('bundling_id')->constrained('bundlings')->onDelete('cascade');
            $table->foreignId('treatments_id')->constrained('treatments')->onDelete('cascade');
            $table->integer('jumlah_awal')->default(0);
            $table->integer('jumlah_terpakai')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatment_bundling_r_m_s');
    }
};
