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
        Schema::create('nomor_antrians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poli_id');
            $table->string('kode');
            $table->integer('nomor_antrian');
            $table->enum('status', ['masuk', 'dipanggil','nonaktif'])->default('masuk');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nomor_antrians');
    }
};
