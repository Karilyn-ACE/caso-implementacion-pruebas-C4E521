<?php

namespace Database\Factories;

use App\Models\Docente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Docente>
 */
class DocenteFactory extends Factory
{
    protected $model = Docente::class;

    public function definition(): array
    {
        return [
            'codEmpleado' => fake()->unique()->numberBetween(1000, 999999),
            'nombreEmpleado' => 'Docente',
            'apellidosEmpleado' => 'Prueba',
        ];
    }
}
