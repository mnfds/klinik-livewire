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
        Schema::create('kuotacutis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedSmallInteger('tahun');
            $table->unsignedTinyInteger('kuota_dimiliki')->default(12);
            $table->timestamps();

            $table->unique(['user_id', 'tahun']); // cegah duplikat per user per tahun
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kuotacutis');
    }
};
