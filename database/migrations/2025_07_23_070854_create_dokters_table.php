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
        Schema::create('dokters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained() // otomatis refer ke tabel 'users' dan kolom 'id'
                ->unique()      // menjamin satu user hanya punya satu biodata
                ->onDelete('cascade'); // jika user dihapus, biodata ikut terhapus

            $table->string('nama_dokter');
            $table->string('alamat_dokter')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('telepon')->nullable();
            $table->string('foto_wajah')->nullable();

            $table->string('tingkat_pendidikan')->nullable();
            $table->string('institusi')->nullable();
            $table->string('tahun_kelulusan')->nullable();
            $table->string('no_str')->nullable();
            $table->string('surat_izin_pratik')->nullable();
            $table->string('masa_berlaku_sip')->nullable();
            $table->string('ttd_digital')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokters');
    }
};
