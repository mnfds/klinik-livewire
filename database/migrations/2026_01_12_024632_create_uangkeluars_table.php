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
        Schema::create('uangkeluars', function (Blueprint $table) {
            $table->id();
            $table->string('diajukan_oleh');
            $table->string('role');
            $table->text('keterangan');
            $table->unsignedBigInteger('jumlah_uang');
            $table->enum('jenis_pengeluaran',['SDM', 'Administrasi', 'Marketing', 'Operasional', 'Rumah Tangga', 'Dll']);
            $table->enum('unit_usaha',['Klinik', 'Apotik', 'Lainnya']);
            $table->enum('status',['Menunggu', 'Disetujui', 'Ditolak']);
            $table->dateTime('tanggal_pengajuan')->default(now());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uangkeluars');
    }
};
