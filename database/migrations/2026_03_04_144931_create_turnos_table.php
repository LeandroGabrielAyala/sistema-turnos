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
        Schema::create('turnos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('paciente_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('fecha');
            $table->time('hora');

            $table->enum('estado', [
                'pendiente',
                'confirmado',
                'cancelado',
                'atendido'
            ])->default('pendiente');

            $table->text('observaciones')->nullable();

            $table->timestamps();

            // Evita doble turno misma fecha/hora
            $table->unique(['fecha', 'hora']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('turnos');
    }
};
