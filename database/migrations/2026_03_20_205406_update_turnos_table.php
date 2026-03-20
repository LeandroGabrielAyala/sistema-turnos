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
        Schema::table('turnos', function (Blueprint $table) {

            // 🔹 Renombrar observaciones (motivo del paciente)
            $table->renameColumn('observaciones', 'motivo_consulta');

            // 🔹 Nueva observación del médico
            $table->text('observacion_medica')->nullable();

            // 🔹 Campo estudios (JSON para múltiples valores tipo tags)
            $table->json('estudios')->nullable();

            // 🔹 Cambiar enum (IMPORTANTE)
            $table->enum('estado', ['confirmado', 'cancelado', 'atendido'])
                ->default('confirmado')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
