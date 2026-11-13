<?php

use App\Models\Curso;
use App\Models\Docente;
use App\Models\Estudiante;
use App\Models\EstudianteMatriculado;
use App\Models\Grupo;

mutates(Curso::class, Docente::class, Estudiante::class, EstudianteMatriculado::class, Grupo::class);

test('los modelos conservan los atributos del dominio asignados en conjunto', function (string $modelClass, array $attributes, array $expectedAttributes) {
    $model = new $modelClass($attributes);

    foreach ($expectedAttributes as $attribute => $expectedValue) {
        expect($model->getAttribute($attribute))->toBe($expectedValue);
    }
})->with([
    'curso' => [Curso::class, ['sigla' => 'IF6100', 'nombreCurso' => 'Sistemas', 'creditos' => 4], ['sigla' => 'IF6100', 'nombreCurso' => 'Sistemas', 'creditos' => 4]],
    'docente' => [Docente::class, ['codEmpleado' => '1001', 'nombreEmpleado' => 'Maria', 'apellidosEmpleado' => 'Vargas'], ['codEmpleado' => 1001, 'nombreEmpleado' => 'Maria', 'apellidosEmpleado' => 'Vargas']],
    'estudiante' => [Estudiante::class, ['carne' => 'C12345', 'nombre' => 'Ana', 'apellidos' => 'Mora', 'consejero_id' => 1001], ['carne' => 'C12345', 'nombre' => 'Ana', 'apellidos' => 'Mora', 'consejero_id' => 1001]],
    'matricula' => [EstudianteMatriculado::class, ['grupo_id' => 1, 'estudiante_id' => 'C12345', 'nota' => '8.5'], ['grupo_id' => 1, 'estudiante_id' => 'C12345', 'nota' => 8.5]],
    'grupo' => [Grupo::class, ['numeroCiclo' => '2', 'ano' => '2026', 'numeroGrupo' => '1', 'abierto' => '1'], ['numeroCiclo' => 2, 'ano' => 2026, 'numeroGrupo' => 1, 'abierto' => true]],
]);

test('los grupos interpretan el estado cerrado recibido como cero', function () {
    $grupo = new Grupo(['abierto' => '0']);

    expect($grupo->abierto)->toBeFalse();
});

test('guardar conserva la clave propia del curso', function () {
    $curso = Curso::factory()->make(['sigla' => 'IF6100']);

    $curso->save();

    expect($curso->getKey())->toBe('IF6100');
    expect($curso->fresh()->sigla)->toBe('IF6100');
    $this->assertModelExists($curso);
});

test('guardar conserva el carne propio del estudiante', function () {
    $estudiante = Estudiante::factory()->make(['carne' => 'C12345']);

    $estudiante->save();

    expect($estudiante->getKey())->toBe('C12345');
    expect($estudiante->fresh()->carne)->toBe('C12345');
    $this->assertModelExists($estudiante);
});

test('guardar conserva el codigo propio del docente', function () {
    $docente = Docente::factory()->make(['codEmpleado' => 1001]);

    $docente->save();

    expect($docente->getKey())->toBe(1001);
    expect($docente->fresh()->codEmpleado)->toBe(1001);
    $this->assertModelExists($docente);
});

test('una matricula se guarda con las columnas existentes', function () {
    $grupo = Grupo::factory()->create();
    $estudiante = Estudiante::factory()->create();
    $matricula = new EstudianteMatriculado([
        'grupo_id' => $grupo->id,
        'estudiante_id' => $estudiante->carne,
        'nota' => 8.5,
    ]);

    $matricula->save();

    $this->assertDatabaseHas('estudiantes_matriculados', [
        'grupo_id' => $grupo->id,
        'estudiante_id' => $estudiante->carne,
        'nota' => 8.5,
    ]);
});
