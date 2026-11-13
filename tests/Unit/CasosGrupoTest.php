
<?php

use App\Models\Curso;
use App\Models\Docente;
use App\Models\Estudiante;
use App\Models\Grupo;

mutates(Curso::class, Grupo::class, Docente::class, Estudiante::class);

test('CP-01 grupo con toda su informacion y curso', function () {
    $curso = Curso::factory()->create([
        'sigla' => 'IF6100',
        'nombreCurso' => 'Análisis y Diseño de Sistemas',
        'creditos' => 4,
    ]);

    $grupo = Grupo::factory()->create([
        'curso_sigla' => $curso->sigla,
        'numeroCiclo' => 2,
        'ano' => 2026,
        'numeroGrupo' => 1,
        'abierto' => true,
    ]);

    expect($grupo->numeroCiclo)->toBe(2)
        ->and($grupo->ano)->toBe(2026)
        ->and($grupo->numeroGrupo)->toBe(1)
        ->and($grupo->abierto)->toBeTrue()
        ->and($grupo->curso->sigla)->toBe('IF6100')
        ->and($grupo->curso->creditos)->toBe(4);
});

test('CP-02 curso con tres grupos y dos abiertos', function () {
    $curso = Curso::factory()->create([
        'sigla' => 'IF6100',
    ]);

    Grupo::factory()->create([
        'curso_sigla' => $curso->sigla,
        'numeroGrupo' => 1,
        'abierto' => true,
    ]);

    Grupo::factory()->create([
        'curso_sigla' => $curso->sigla,
        'numeroGrupo' => 2,
        'abierto' => true,
    ]);

    Grupo::factory()->create([
        'curso_sigla' => $curso->sigla,
        'numeroGrupo' => 3,
        'abierto' => false,
    ]);

    $grupos = $curso->grupos;
    $abiertos = $grupos->filter(fn ($grupo) => $grupo->abierto);

    expect($grupos)->toHaveCount(3)
        ->and($abiertos)->toHaveCount(2)
        ->and($abiertos->pluck('numeroGrupo')->sort()->values()->all())
        ->toBe([1, 2]);
});

test('CP-03 grupo acepta dos docentes', function () {
    $grupo = Grupo::factory()->create();

    $docente1 = Docente::factory()->create([
        'codEmpleado' => 1001,
    ]);

    $docente2 = Docente::factory()->create([
        'codEmpleado' => 1002,
    ]);

    $grupo->asignarDocente($docente1);
    $grupo->asignarDocente($docente2);

    expect($grupo->docentes()->count())->toBe(2)
        ->and($grupo->docentes->pluck('codEmpleado')->sort()->values()->all())
        ->toBe([1001, 1002]);
});

test('CP-04 grupo rechaza tercer docente', function () {
    $grupo = Grupo::factory()->create();

    $docente1 = Docente::factory()->create([
        'codEmpleado' => 1001,
    ]);

    $docente2 = Docente::factory()->create([
        'codEmpleado' => 1002,
    ]);

    $docente3 = Docente::factory()->create([
        'codEmpleado' => 1003,
    ]);

    $grupo->asignarDocente($docente1);
    $grupo->asignarDocente($docente2);

    expect(fn () => $grupo->asignarDocente($docente3))
        ->toThrow(DomainException::class);

    expect($grupo->docentes()->count())->toBe(2)
        ->and($grupo->docentes()->where('codEmpleado', 1003)->exists())
        ->toBeFalse();
});

test('CP-05 grupo con 14 estudiantes no puede abrirse', function () {
    $grupo = Grupo::factory()->create([
        'numeroGrupo' => 1,
        'abierto' => false,
    ]);

    $docente = Docente::factory()->create([
        'codEmpleado' => 1001,
    ]);

    $grupo->asignarDocente($docente);

    $estudiantes = Estudiante::factory()->count(14)->create();

    foreach ($estudiantes as $estudiante) {
        $grupo->estudiantes()->attach($estudiante->carne);
    }

    expect($grupo->docentes()->count())->toBe(1)
        ->and($grupo->estudiantes()->count())->toBe(14)
        ->and($grupo->puedeAbrirse())->toBeFalse();

    expect(fn () => $grupo->abrir())
        ->toThrow(DomainException::class);

    expect($grupo->fresh()->abierto)->toBeFalse();
});

test('CP-06 grupo con 15 estudiantes y un docente puede abrirse', function () {
    $grupo = Grupo::factory()->create([
        'numeroGrupo' => 1,
        'abierto' => false,
    ]);

    $docente = Docente::factory()->create([
        'codEmpleado' => 1001,
    ]);

    $grupo->asignarDocente($docente);

    $estudiantes = Estudiante::factory()->count(15)->create();

    foreach ($estudiantes as $estudiante) {
        $grupo->estudiantes()->attach($estudiante->carne);
    }

    expect($grupo->puedeAbrirse())->toBeTrue();

    $grupo->abrir();

    expect($grupo->fresh()->abierto)->toBeTrue()
        ->and($grupo->docentes()->count())->toBe(1)
        ->and($grupo->estudiantes()->count())->toBe(15);
});
