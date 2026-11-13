
<?php

use App\Models\Docente;
use App\Models\Estudiante;
use App\Models\EstudianteMatriculado;
use App\Models\Grupo;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

mutates(Grupo::class, Estudiante::class, Docente::class, EstudianteMatriculado::class);

test('un grupo con 15 estudiantes respeta el limite de docentes para abrirse', function (int $cantidadDocentes, bool $puedeAbrirse) {
    $grupo = Grupo::factory()->create();
    $docentes = Docente::factory()->count($cantidadDocentes)->create();
    $estudiantes = Estudiante::factory()->count(15)->create();

    $grupo->docentes()->attach($docentes->modelKeys());
    $grupo->estudiantes()->attach($estudiantes->modelKeys());

    expect($grupo->puedeAbrirse())->toBe($puedeAbrirse);
})->with([
    'dos docentes' => [2, true],
    'tres docentes' => [3, false],
]);

test('registrar nota acepta los limites y valores cercanos del rango', function (float $nota) {
    $grupo = Grupo::factory()->create();
    $estudiante = Estudiante::factory()->create();
    $grupo->estudiantes()->attach($estudiante->carne);

    $grupo->registrarNota($estudiante, $nota);

    expect($grupo->matriculas()->firstOrFail()->nota)->toBe($nota);
})->with([
    'cero' => [0.0],
    'cercana a cero' => [0.5],
    'cercana a diez' => [9.5],
    'diez' => [10.0],
]);
test('CP-07: un grupo con 20 estudiantes y sin docentes no puede abrirse', function () {

    $grupo = Grupo::factory()->create([
        'numeroGrupo' => 1,
        'abierto' => false,
    ]);

    $estudiantes = Estudiante::factory()->count(20)->create();

    foreach ($estudiantes as $estudiante) {
        $grupo->estudiantes()->attach($estudiante->carne);
    }

    expect($grupo->estudiantes()->count())->toBe(20);
    expect($grupo->docentes()->count())->toBe(0);

    expect($grupo->puedeAbrirse())->toBeFalse();

    expect(fn () => $grupo->abrir())
        ->toThrow(DomainException::class);

    expect($grupo->fresh()->abierto)->toBeFalse();
});

test('CP-08: estudiante tiene un docente consejero y la relacion es bidireccional', function () {

    $docente = Docente::factory()->create([
        'codEmpleado' => 1001,
    ]);

    $estudiante = Estudiante::factory()->create([
        'carne' => 'C12345',
        'consejero_id' => 1001,
    ]);

    expect($estudiante->fresh()->consejero_id)->toBe(1001);

    expect($estudiante->consejero->codEmpleado)->toBe(1001);

    expect($docente->estudiantes()->count())->toBe(1);

    expect($docente->estudiantes()->first()->carne)
        ->toBe('C12345');
});

test('CP-09: estudiante cambia de docente consejero', function () {
    $docente1 = Docente::factory()->create([
        'codEmpleado' => 1001,
    ]);

    $docente2 = Docente::factory()->create([
        'codEmpleado' => 1002,
    ]);

    $estudiante = Estudiante::factory()->create([
        'carne' => 'C12345',
        'consejero_id' => 1001,
    ]);

    expect($docente1->estudiantes()->count())->toBe(1);

    $estudiante->consejero_id = 1002;
    $estudiante->save();

    expect($estudiante->fresh()->consejero_id)->toBe(1002);
    expect($estudiante->fresh()->consejero->codEmpleado)->toBe(1002);
    expect($docente1->estudiantes()->count())->toBe(0);
    expect($docente2->estudiantes()->count())->toBe(1);
});

test('CP-10: estudiante se matricula con nota inicial nula', function () {
    $grupo = Grupo::factory()->create();

    $estudiante = Estudiante::factory()->create([
        'carne' => 'C12345',
    ]);

    $grupo->estudiantes()->attach($estudiante->carne);

    $matricula = DB::table('estudiantes_matriculados')
        ->where('grupo_id', $grupo->id)
        ->where('estudiante_id', $estudiante->carne)
        ->first();

    expect($matricula)->not->toBeNull();
    expect($matricula->nota)->toBeNull();

    expect(
        DB::table('estudiantes_matriculados')
            ->where('grupo_id', $grupo->id)
            ->where('estudiante_id', $estudiante->carne)
            ->count()
    )->toBe(1);

    expect($grupo->estudiantes()->count())->toBe(1);
});

test('CP-11: registrar nota actualiza solo la matricula correspondiente', function () {
    $grupo = Grupo::factory()->create();

    $estudiante1 = Estudiante::factory()->create();
    $estudiante2 = Estudiante::factory()->create();

    $grupo->estudiantes()->attach($estudiante1->carne);
    $grupo->estudiantes()->attach($estudiante2->carne);

    $grupo->registrarNota($estudiante1, 8.5);

    $nota1 = DB::table('estudiantes_matriculados')
        ->where('grupo_id', $grupo->id)
        ->where('estudiante_id', $estudiante1->carne)
        ->value('nota');

    $nota2 = DB::table('estudiantes_matriculados')
        ->where('grupo_id', $grupo->id)
        ->where('estudiante_id', $estudiante2->carne)
        ->value('nota');

    expect((float) $nota1)->toBe(8.5);
    expect($nota2)->toBeNull();
});

test('CP-12: registrar nota rechaza valores fuera del rango permitido', function (float $notaInvalida) {
    $grupo = Grupo::factory()->create();

    $estudiante = Estudiante::factory()->create();

    $grupo->estudiantes()->attach($estudiante->carne);

    expect(fn () => $grupo->registrarNota($estudiante, $notaInvalida))
        ->toThrow(InvalidArgumentException::class);

    $notaGuardada = DB::table('estudiantes_matriculados')
        ->where('grupo_id', $grupo->id)
        ->where('estudiante_id', $estudiante->carne)
        ->value('nota');

    expect($notaGuardada)->toBeNull();
})->with([
    'nota negativa' => [-1.0],
    'nota superior a diez' => [10.5],
]);

test('CP-13: no permite matricular dos veces al mismo estudiante en un grupo', function () {
    $grupo = Grupo::factory()->create();

    $estudiante = Estudiante::factory()->create();

    $grupo->estudiantes()->attach($estudiante->carne);

    expect(fn () => $grupo->estudiantes()->attach($estudiante->carne))
        ->toThrow(QueryException::class);

    $cantidadMatriculas = DB::table('estudiantes_matriculados')
        ->where('grupo_id', $grupo->id)
        ->where('estudiante_id', $estudiante->carne)
        ->count();

    expect($cantidadMatriculas)->toBe(1);
});
