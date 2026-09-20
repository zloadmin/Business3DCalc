<?php

namespace Database\Factories;

use App\Enums\BuildingType;
use App\Models\Building;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Building>
 */
class BuildingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(BuildingType::cases());

        return [
            'name' => $this->faker->words(2, true),
            'type' => $type,
            'price' => $this->faker->numberBetween(1_000, 500_000),
            'income' => $this->faker->numberBetween(100, 20_000),
            'upgrade_coefficient' => $this->faker->randomFloat(3, 1.05, 3.0),
            'employees_count' => $this->faker->numberBetween(0, 200),
            'residents_count' => $type === BuildingType::Living
                ? $this->faker->numberBetween(1, 500)
                : 0,
            'happiness' => $this->faker->numberBetween(-20, 50),
            'attractiveness' => $this->faker->numberBetween(-20, 50),
        ];
    }

    /**
     * Indicate that the building is of the given type.
     */
    public function ofType(BuildingType $type): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $type,
            'residents_count' => $type === BuildingType::Living
                ? $this->faker->numberBetween(1, 500)
                : 0,
        ]);
    }
}
