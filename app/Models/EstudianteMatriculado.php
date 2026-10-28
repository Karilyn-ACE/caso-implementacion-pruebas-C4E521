<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EstudianteMatriculado extends Model
{
    protected $table = 'estudiante_matriculados';

    public $timestamps = false;

    protected $fillable = [
        'grupo_id',
        'estudiante_carne',
        'nota',
    ];

    protected $casts = [
        'nota' => 'float',
    ];

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(
            Grupo::class,
            'grupo_id',
            'id'
        );
    }

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(
            Estudiante::class,
            'estudiante_carne',
            'carne'
        );
    }
    
public function matriculas()
{
    return $this->hasMany(
        EstudianteMatriculado::class,
        'estudiante_carne',
        'carne'
    );
}

public function grupos()
{
    return $this->belongsToMany(
        Grupo::class,
        'estudiante_matriculados',
        'estudiante_carne',
        'grupo_id',
        'carne',
        'id'
    )->withPivot('nota');
}

}