<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
       protected $fillable = [
        'numeroCiclo',
        'ano',
        'numeroGrupo',
        'abierto',
    ];

    protected $casts = [
        'numeroCiclo' => 'integer',
        'ano' => 'integer',
        'numeroGrupo' => 'integer',
        'abierto' => 'boolean',
    ];
    
public function curso()
{
    return $this->belongsTo(Curso::class, 'curso_sigla', 'sigla');
}

public function docentes()
{
    return $this->belongsToMany(
        Docente::class,
        'docente_grupo',
        'grupo_id',
        'docente_codEmpleado',
        'id',
        'codEmpleado'
    );
}

public function matriculas()
{
    return $this->hasMany(
        EstudianteMatriculado::class,
        'grupo_id',
        'id'
    );
}

public function estudiantes()
{
    return $this->belongsToMany(
        Estudiante::class,
        'estudiante_matriculados',
        'grupo_id',
        'estudiante_carne',
        'id',
        'carne'
    )->withPivot('nota');
}


}