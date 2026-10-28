<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Estudiante extends Model
{
    protected $primaryKey = 'carne';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'carne',
        'nombre',
        'apellidos',
        'docente_codEmpleado',
    ];

    public function consejero(): BelongsTo
    {
        return $this->belongsTo(
            Docente::class,
            'docente_codEmpleado',
            'codEmpleado'
        );
    }
}