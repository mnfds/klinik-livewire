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
        Schema::create('tanda_vitals', function (Blueprint $table) {
            $table->id();

            // Foreign key ke kajian_awals
            $table->foreignId('kajian_awal_id')->constrained('kajian_awals')->onDelete('cascade');

            // Kolom data tanda vital
            $table->decimal('suhu_tubuh', 4, 1)->nullable();           // ex: 36.7 °C
            $table->integer('nadi')->nullable();                      // ex: 75 bpm
            $table->integer('sistole')->nullable();                   // ex: 120 mmHg
            $table->integer('diastole')->nullable();                  // ex: 80 mmHg
            $table->integer('frekuensi_pernapasan')->nullable();      // ex: 18 per menit

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanda_vitals');
    }
};
