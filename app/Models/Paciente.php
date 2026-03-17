<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Paciente extends Model
{
    protected $fillable = [
        'nombre',
        'apellido',
        'dni',
        'fecha_nacimiento',
        'domicilio',
        'telefono',
        'obra_social_id',
        'estado_civil',
        'ocupacion',
        'alergias',
        'detalle_alergias',
        'enfermedades_hereditarias',
        'medicacion_actual',
        'cirugias',
        'detalle_cirugias',
        'peso',
        'presion_arterial',
    ];

    public function obraSocial()
    {
        return $this->belongsTo(ObraSocial::class);
    }

    // Edad automática
    public function getEdadAttribute()
    {
        return \Carbon\Carbon::parse($this->fecha_nacimiento)->age;
    }

    public function getNombreCompletoAttribute()
    {
        return "{$this->apellido}, {$this->nombre}";
    }
}
