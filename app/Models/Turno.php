<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    protected $fillable = [
        'paciente_id',
        'fecha',
        'hora',
        'estado',
        'motivo_consulta',
        'observacion_medica',
        'estudios',
    ];

    protected $casts = [
        'fecha' => 'date',
        'estudios' => 'array',
    ];

    public const ESTUDIOS = [
        'radiografia' => 'Radiografía',
        'analisis_sangre' => 'Análisis de sangre',
        'analisis_orina' => 'Análisis de orina',
        'ecografia' => 'Ecografía',
        'resonancia' => 'Resonancia magnética',
        'tomografia' => 'Tomografía computada',
        'electrocardiograma' => 'Electrocardiograma (ECG)',
        'ergometria' => 'Ergometría (prueba de esfuerzo)',
        'holter' => 'Holter cardíaco',
        'endoscopia' => 'Endoscopía',
        'colonoscopia' => 'Colonoscopía',
        'mamografia' => 'Mamografía',
        'densitometria_osea' => 'Densitometría ósea',
        'prueba_covid' => 'Test COVID-19',
        'perfil_lipidico' => 'Perfil lipídico',
        'glucemia' => 'Glucemia',
        'hemograma' => 'Hemograma completo',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }
}