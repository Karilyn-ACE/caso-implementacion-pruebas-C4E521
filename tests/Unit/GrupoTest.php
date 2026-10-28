
<?php

use App\Models\Grupo;
use App\Models\Curso;
use App\Models\Docente;
use App\Models\Estudiante;
use App\Models\EstudianteMatriculado;

test('grupo contiene toda su informacion asociada', function () {

    // Crear el curso
    $curso = new Curso([
        'sigla' => 'IF6100',
        'nombreCurso' => 'Análisis y Diseño de Sistemas',
        'creditos' => 4,
    ]);

    // Crear el docente
    $docente = new Docente([
        'codEmpleado' => 1001,
        'nombreEmpleado' => 'María',
        'apellidosEmpleado' => 'Vargas',
    ]);

    // Crear el estudiante
    $estudiante = new Estudiante([
        'carne' => 'C4E521',
        'nombre' => 'Estudiante',
        'apellidos' => 'Ejemplo',
    ]);

    // Crear el grupo
    $grupo = new Grupo([
        'numeroCiclo' => 2,
        'ano' => 2026,
        'numeroGrupo' => 1,
        'abierto' => true,
    ]);

    // Crear la matrícula
    $matricula = new EstudianteMatriculado([
        'nota' => 95.0,
    ]);

    // Asociar los objetos
    $matricula->setRelation('estudiante', $estudiante);

    $grupo->setRelation('curso', $curso);
    $grupo->setRelation('docentes', collect([$docente]));
    $grupo->setRelation('matriculas', collect([$matricula]));

    // Verificar los atributos del grupo
    expect($grupo->numeroCiclo)->toBe(2);
    expect($grupo->ano)->toBe(2026);
    expect($grupo->numeroGrupo)->toBe(1);
    expect($grupo->abierto)->toBeTrue();

    // Verificar el curso
    expect($grupo->curso->sigla)->toBe('IF6100');

    // Verificar el docente
    expect($grupo->docentes)->toHaveCount(1);
    expect($grupo->docentes->first()->codEmpleado)->toBe(1001);

    // Verificar el estudiante y su nota
    expect($grupo->matriculas)->toHaveCount(1);
    expect($grupo->matriculas->first()->estudiante->carne)
        ->toBe('C4E521');

    expect($grupo->matriculas->first()->nota)
        ->toBe(95.0);
});
