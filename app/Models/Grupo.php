<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grupo extends Model
{
    use HasFactory;

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

    public function curso(): BelongsTo
    {
        return $this->belongsTo(
            Curso::class,
            'curso_sigla',
            'sigla'
        );
    }

    public function docentes(): BelongsToMany
    {
        return $this->belongsToMany(
            Docente::class,
            'grupo_docente',
            'grupo_id',
            'docente_id',
            'id',
            'codEmpleado'
        );
    }

    public function matriculas(): HasMany
    {
        return $this->hasMany(
            EstudianteMatriculado::class,
            'grupo_id',
            'id'
        );
    }

    public function estudiantes(): BelongsToMany
    {
        return $this->belongsToMany(
            Estudiante::class,
            'estudiantes_matriculados',
            'grupo_id',
            'estudiante_id',
            'id',
            'carne'
        )
            ->using(EstudianteMatriculado::class)
            ->withPivot('nota');
    }

    public function asignarDocente(Docente $docente): void
    {
        if ($this->docentes()->count() >= 2) {
            throw new \DomainException(
                'Un grupo no puede tener más de dos docentes.'
            );
        }

        $this->docentes()->attach($docente->codEmpleado);
    }

    public function puedeAbrirse(): bool
    {
        $cantidadDocentes = $this->docentes()->count();
        $cantidadEstudiantes = $this->estudiantes()->count();

        return $cantidadDocentes >= 1
            && $cantidadDocentes <= 2
            && $cantidadEstudiantes >= 15;
    }

    public function abrir(): void
    {
        if (! $this->puedeAbrirse()) {
            throw new \DomainException(
                'El grupo no cumple los requisitos para abrirse.'
            );
        }

        $this->abierto = true;
        $this->save();
    }

    public function registrarNota(
        Estudiante $estudiante,
        float $nota
    ): void {
        if ($nota < 0 || $nota > 10) {
            throw new \InvalidArgumentException(
                'La nota debe estar entre 0 y 10.'
            );
        }

        $this->estudiantes()->updateExistingPivot(
            $estudiante->carne,
            ['nota' => $nota]
        );
    }
}
