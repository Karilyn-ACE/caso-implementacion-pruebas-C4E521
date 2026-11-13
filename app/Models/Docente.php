<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Docente extends Model
{
    use HasFactory;

    protected $primaryKey = 'codEmpleado';

    public $incrementing = false;

    protected $fillable = [
        'codEmpleado',
        'nombreEmpleado',
        'apellidosEmpleado',
    ];

    protected $casts = [
        'codEmpleado' => 'integer',
    ];

    public function grupos(): BelongsToMany
    {
        return $this->belongsToMany(
            Grupo::class,
            'grupo_docente',
            'docente_id',
            'grupo_id',
            'codEmpleado',
            'id'
        );
    }

    public function estudiantes(): HasMany
    {
        return $this->hasMany(
            Estudiante::class,
            'consejero_id',
            'codEmpleado'
        );
    }
}
