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
        Schema::create('kolestrols', function (Blueprint $table) {
            $table->id();
            //foreign key ke kajian_awals
            $table->foreignId('kajian_awal_id')->constrained('kajian_awals')->onDelete('cascade');
       
            // Kolom data Kolestrol
            $table->integer('kolestrol_hdl')->nullable(); // ex: 50 mg/dL
            $table->integer('kolestrol_ldl')->nullable(); // ex: 80 mg/dL
            $table->integer('trigliserida')->nullable(); // ex: 120 mg/dL
            $table->integer('kolestrol_total')->nullable(); // ex: 150 mg/dL

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kolestrols');
    }
};
