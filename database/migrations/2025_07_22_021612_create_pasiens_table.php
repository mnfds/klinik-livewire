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
        Schema::create('pasiens', function (Blueprint $table) {
            $table->id(); // bigint unsigned AUTO_INCREMENT
            $table->char('no_register', 255);
            $table->string('nik', 255)->nullable();
            $table->string('no_ihs', 255)->nullable();
            $table->string('nama', 255);
            $table->string('alamat', 255)->nullable();
            $table->string('no_telp', 255)->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Wanita']);
            $table->string('agama', 255)->nullable();
            $table->string('profesi', 255)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('status', 255)->nullable();
            $table->string('foto_pasien', 255)->nullable();
            $table->text('deskripsi')->nullable();

            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pasiens');
    }
};
