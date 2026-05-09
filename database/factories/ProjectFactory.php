<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'student_name' => fake()->name(),
            'supervisor' => fake()->name(),
            'title' => fake()->sentence(5),
            'project_type' => fake()->randomElement(['Research', 'Capstone', 'Thesis']),
            'abstract' => fake()->paragraph(),
            'keywords' => implode(', ', fake()->words(4)),
            'completion_year' => fake()->numberBetween(2018, now()->year),
            'pdf_file' => null,
            'pdf_path' => null,
            'pdf_original_name' => null,
            'pdf_mime' => null,
            'pdf_size' => null,
            'file_hash' => null,
            'pdf_text' => fake()->paragraph(),
            'uploaded_by' => User::factory()->admin(),
        ];
    }
}
