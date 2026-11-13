<?php

namespace Database\Factories;

use App\Models\Curso;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Curso>
 */
class CursoFactory extends Factory
{
    protected $model = Curso::class;

    public function definition(): array
    {
        return [
            'sigla' => 'IF'.fake()->unique()->numberBetween(1000, 9999),
            'nombreCurso' => 'Curso de prueba',
            'creditos' => 4,
        ];
    }
}
