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
        'observaciones'
    ];

    protected $casts = [
        'fecha' => 'date',
        'estudios' => 'array',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }
}