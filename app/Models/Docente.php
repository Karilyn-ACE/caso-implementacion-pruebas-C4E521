<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
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
    
public function grupos()
{
    return $this->belongsToMany(
        Grupo::class,
        'docente_grupo',
        'docente_codEmpleado',
        'grupo_id',
        'codEmpleado',
        'id'
    );
}

}