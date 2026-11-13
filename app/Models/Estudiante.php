<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estudiante extends Model
{
    use HasFactory;

    protected $primaryKey = 'carne';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'carne',
        'nombre',
        'apellidos',
        'consejero_id',
    ];

    public function consejero(): BelongsTo
    {
        return $this->belongsTo(
            Docente::class,
            'consejero_id',
            'codEmpleado'
        );
    }

    public function matriculas(): HasMany
    {
        return $this->hasMany(
            EstudianteMatriculado::class,
            'estudiante_id',
            'carne'
        );
    }

    public function grupos(): BelongsToMany
    {
        return $this->belongsToMany(
            Grupo::class,
            'estudiantes_matriculados',
            'estudiante_id',
            'grupo_id',
            'carne',
            'id'
        )->using(EstudianteMatriculado::class)
            ->withPivot('nota');
    }
}
