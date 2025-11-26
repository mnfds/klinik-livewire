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
        Schema::create('practitioners', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable;
            $table->string('gender')->nullable;
            $table->string('birthdate')->nullable;
            $table->string('id_satusehat')->nullable;
            $table->string('nik')->nullable;
            $table->string('ihs')->nullable;
            $table->string('city')->nullable;
            $table->string('address_line')->nullable;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practitioners');
    }
};
