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



public function asignarDocente(int $codEmpleado): void
{
    $docentes = $this->relationLoaded('docentes')
        ? $this->getRelation('docentes')
        : collect();

    if ($docentes->count() >= 2) {
        throw new \DomainException(
            'Un grupo no puede tener más de dos docentes.'
        );
    }

    if ($docentes->contains('codEmpleado', $codEmpleado)) {
        return;
    }

    $docentes->push(new Docente([
        'codEmpleado' => $codEmpleado,
    ]));

    $this->setRelation('docentes', $docentes);
}

public function puedeAbrirse(): bool
{
    $docentes = $this->relationLoaded('docentes')
        ? $this->getRelation('docentes')->count()
        : 0;

    $estudiantes = $this->relationLoaded('matriculas')
        ? $this->getRelation('matriculas')->count()
        : 0;

    return $docentes >= 1
        && $docentes <= 2
        && $estudiantes >= 15;
}

public function abrir(): void
{
    if (!$this->puedeAbrirse()) {
        throw new \DomainException(
            'El grupo no cumple los requisitos para abrirse.'
        );
    }

    $this->abierto = true;
}




}