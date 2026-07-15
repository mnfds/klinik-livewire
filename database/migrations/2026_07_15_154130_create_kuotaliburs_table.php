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
        Schema::create('kuotaliburs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedTinyInteger('bulan'); // 1-12
            $table->unsignedSmallInteger('tahun');
            $table->unsignedTinyInteger('kuota_dimiliki')->default(4);
            $table->timestamps();

            $table->unique(['user_id', 'bulan', 'tahun']); // cegah duplikat per user per bulan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kuotaliburs');
    }
};
