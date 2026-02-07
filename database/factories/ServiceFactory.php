<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'category' => 'consulting',
            'type' => 'online',
            'price' => $this->faker->numberBetween(1000000, 10000000),
            'description' => $this->faker->paragraph(),
            'benefits' => ['Benefit 1', 'Benefit 2'],
            'activities' => ['Activity 1', 'Activity 2'],
        ];
    }
}