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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('id_lokasi')->nullable();
            $table->string('nama_lokasi')->nullable();
            $table->string('deskripsi')->nullable();
            //kontak
            $table->string('no_telp')->nullable();
            $table->string('email')->nullable();
            $table->string('web')->nullable();
            //full alamat
            $table->string('alamat')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kota')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->string('kode_pos')->nullable();
            // koordinat
            $table->decimal('latitude')->nullable();
            $table->decimal('longitude')->nullable();
            $table->decimal('altitude')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
