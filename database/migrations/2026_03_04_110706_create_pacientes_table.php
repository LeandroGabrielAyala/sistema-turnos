<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();

            // Datos personales
            $table->string('nombre');
            $table->string('apellido');
            $table->string('dni')->unique();

            $table->date('fecha_nacimiento');

            // Contacto
            $table->string('domicilio')->nullable();
            $table->string('telefono')->nullable();

            // Relación con obra social
            $table->foreignId('obra_social_id')
                ->nullable()
                ->constrained('obras_socials')
                ->nullOnDelete();

            // Información social
            $table->enum('estado_civil', [
                'soltero',
                'casado',
                'divorciado',
                'viudo'
            ])->nullable();
            $table->string('ocupacion')->nullable();

            // Información médica básica
            $table->boolean('alergias')->default(false);
            $table->text('detalle_alergias')->nullable();

            $table->text('enfermedades_hereditarias')->nullable();
            $table->text('medicacion_actual')->nullable();
            $table->boolean('cirugias')->default(false);
            $table->text('detalle_cirugias')->nullable();

            // Datos clínicos (recomendado mover a tabla consultas luego)
            $table->decimal('peso', 5, 2)->nullable(); // Ej: 72.50 kg
            $table->string('presion_arterial')->nullable(); // Ej: 120/80

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
