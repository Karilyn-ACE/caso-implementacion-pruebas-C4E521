<?php

namespace Database\Factories;

use App\Models\Docente;
use App\Models\Estudiante;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Estudiante>
 */
class EstudianteFactory extends Factory
{
    protected $model = Estudiante::class;

    public function definition(): array
    {
        return [
            'carne' => 'C'.fake()->unique()->numberBetween(10000, 99999),
            'nombre' => 'Estudiante',
            'apellidos' => 'Prueba',
            'consejero_id' => Docente::factory(),
        ];
    }
}
