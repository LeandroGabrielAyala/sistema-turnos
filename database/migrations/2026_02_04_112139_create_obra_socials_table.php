<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obras_socials', function (Blueprint $table) {
            $table->id();

            $table->string('alias')->unique(); // Ej: OSDE
            $table->string('nombre');          // Ej: Organización de Servicios Directos Empresarios

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obras_socials');
    }
};
