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
        Schema::create('pendapatanlainnyas', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi')->unique();
            $table->dateTime('tanggal_transaksi')->default(now());

            $table->unsignedBigInteger('total_tagihan')->default(0);
            $table->text('keterangan');

            $table->enum('unit_usaha',['Klinik', 'Apotik', 'Sewa Multifunction', 'Coffeshop', 'Dll']);
            $table->enum('status', ['belum lunas','lunas', 'belum bayar', 'batal'])->default('belum bayar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendapatanlainnyas');
    }
};
