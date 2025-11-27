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
        Schema::create('pasien_terdaftars', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('pasien_id')->constrained('pasiens')->onDelete('cascade');
            $table->foreignId('poli_id')->constrained('poli_kliniks')->onDelete('cascade');
            $table->foreignId('dokter_id')->constrained('dokters')->onDelete('cascade');
            $table->foreignId('encounter_id')->nullable();

            $table->string('jenis_kunjungan');
            $table->date('tanggal_kunjungan');
            $table->enum('status_terdaftar', 
            [
                'terdaftar',    // muncul untuk perawat mengisi KAJIAN AWAL
                'konsultasi',   // muncul untuk dokter mengisi SOAP
                'peresepan',    //muncul untuk apoteker mengisi obat dari inventory berdasarkan resep dokter 
                'pembayaran',   //muncul di kasir untuk pembayaran dan konfirmasi obat yang ingin ditebus
                'lunas',      // muncul di apoteker kalau sudah melakukan pembayaran sudah lunas dan obat sudah dikonfimasi
                'selesai',      // menandakan obat sudah diserahkan dan kunjungan pasien selesai
            ]);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pasien_terdaftars');
    }
};
