<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObraSocial extends Model
{
    protected $table = 'obras_socials';
    
    protected $fillable = ['alias', 'nombre'];

    public function pacientes()
    {
        return $this->hasMany(Paciente::class);
    }

    public function turnos()
    {
        return $this->hasManyThrough(
            Turno::class,
            Paciente::class,
            'obra_social_id', // FK en pacientes
            'paciente_id',    // FK en turnos
            'id',             // PK en obra_social
            'id'              // PK en pacientes
        );
    }
}
