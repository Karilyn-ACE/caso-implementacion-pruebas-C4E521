
<?php

use App\Models\Curso;
use App\Models\Grupo;
use App\Models\Docente;
use App\Models\EstudianteMatriculado;

test('CP-01 grupo con toda su informacion y curso', function () {
    $curso = new Curso([
        'sigla' => 'IF6100',
        'nombreCurso' => 'Análisis y Diseño de Sistemas',
        'creditos' => 4,
    ]);

    $grupo = new Grupo([
        'numeroCiclo' => 2,
        'ano' => 2026,
        'numeroGrupo' => 1,
        'abierto' => true,
    ]);

    $grupo->setRelation('curso', $curso);

    expect($grupo->numeroCiclo)->toBe(2)
        ->and($grupo->ano)->toBe(2026)
        ->and($grupo->numeroGrupo)->toBe(1)
        ->and($grupo->abierto)->toBeTrue()
        ->and($grupo->curso->sigla)->toBe('IF6100')
        ->and($grupo->curso->creditos)->toBe(4);
});

test('CP-02 curso con tres grupos y dos abiertos', function () {
    $curso = new Curso(['sigla' => 'IF6100']);

    $grupos = collect([
        new Grupo(['numeroGrupo' => 1, 'abierto' => true]),
        new Grupo(['numeroGrupo' => 2, 'abierto' => true]),
        new Grupo(['numeroGrupo' => 3, 'abierto' => false]),
    ]);

    $curso->setRelation('grupos', $grupos);

    $abiertos = $curso->grupos->filter(
        fn ($grupo) => $grupo->abierto
    );

    expect($curso->grupos)->toHaveCount(3)
        ->and($abiertos)->toHaveCount(2)
        ->and($abiertos->pluck('numeroGrupo')->sort()->values()->all())
        ->toBe([1, 2]);
});

test('CP-03 grupo acepta dos docentes', function () {
    $grupo = new Grupo();

    $grupo->asignarDocente(1001);
    $grupo->asignarDocente(1002);

    expect($grupo->docentes)->toHaveCount(2)
        ->and($grupo->docentes->pluck('codEmpleado')->all())
        ->toBe([1001, 1002]);
});

test('CP-04 grupo rechaza tercer docente', function () {
    $grupo = new Grupo();

    $grupo->asignarDocente(1001);
    $grupo->asignarDocente(1002);

    expect(fn () => $grupo->asignarDocente(1003))
        ->toThrow(DomainException::class);

    expect($grupo->docentes)->toHaveCount(2)
        ->and($grupo->docentes->contains('codEmpleado', 1003))
        ->toBeFalse();
});

test('CP-05 grupo con 14 estudiantes no puede abrirse', function () {
    $grupo = new Grupo([
        'numeroCiclo' => 2,
        'ano' => 2026,
        'numeroGrupo' => 1,
        'abierto' => false,
    ]);

    $grupo->asignarDocente(1001);

    $matriculas = collect();

    for ($i = 1; $i <= 14; $i++) {
        $matriculas->push(
            new EstudianteMatriculado(['nota' => null])
        );
    }

    $grupo->setRelation('matriculas', $matriculas);

    expect($grupo->puedeAbrirse())->toBeFalse();

    expect(fn () => $grupo->abrir())
        ->toThrow(DomainException::class);

    expect($grupo->abierto)->toBeFalse();
});


test('CP-06 grupo con 15 estudiantes y un docente puede abrirse', function () {
    $grupo = new Grupo([
        'numeroCiclo' => 2,
        'ano' => 2026,
        'numeroGrupo' => 1,
        'abierto' => false,
    ]);

    $grupo->asignarDocente(1001);

    $matriculas = collect();

    for ($i = 1; $i <= 15; $i++) {
        $matriculas->push(
            new EstudianteMatriculado(['nota' => null])
        );
    }

    $grupo->setRelation('matriculas', $matriculas);

    expect($grupo->puedeAbrirse())->toBeTrue();

    $grupo->abrir();

    expect($grupo->abierto)->toBeTrue();

    expect($grupo->docentes)->toHaveCount(1)
        ->and($grupo->matriculas)->toHaveCount(15);
});

