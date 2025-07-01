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
        Schema::create('biodatas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained() // otomatis refer ke tabel 'users' dan kolom 'id'
                ->unique()      // menjamin satu user hanya punya satu biodata
                ->onDelete('cascade'); // jika user dihapus, biodata ikut terhapus

            $table->string('nama_lengkap');
            $table->string('alamat')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable(); // bisa pakai date kalau formatnya valid
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('telepon')->nullable();
            $table->date('mulai_bekerja')->nullable();
            $table->string('foto_wajah')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biodatas');
    }
};
