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
        Schema::create('pengajuancutitanggals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_cuti_id')->constrained('pengajuancutis')->onDelete('cascade');
            $table->date('tanggal');
            $table->foreignId('jadwal_id')->nullable()->constrained('jadwals')->nullOnDelete();
            $table->foreignId('jamkerja_id_sebelumnya')->nullable()->constrained('jam_kerjas')->nullOnDelete();
            $table->timestamps();
            $table->unique(['pengajuan_cuti_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuancutitanggals');
    }
};
