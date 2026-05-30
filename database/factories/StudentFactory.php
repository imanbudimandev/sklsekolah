<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exam_number' => $this->faker->unique()->numerify('###.###'),
            'nis' => $this->faker->unique()->numerify('##########'),
            'nisn' => $this->faker->unique()->numerify('##########'),
            'name' => $this->faker->name(),
            'birth_place' => $this->faker->city(),
            'birth_date' => $this->faker->dateTimeBetween('-18 years', '-10 years')->format('Y-m-d'),
            'class' => $this->faker->randomElement(['X', 'XI', 'XII']),
            'status' => $this->faker->randomElement(['Active', 'Inactive', 'Graduated']),
            'photo' => null,
        ];
    }
}
