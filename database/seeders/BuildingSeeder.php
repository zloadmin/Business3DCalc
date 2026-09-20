<?php

namespace Database\Seeders;

use App\Enums\BuildingType;
use App\Models\Building;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
    /**
     * Starting buildings across all four types.
     *
     * @var list<array<string, mixed>>
     */
    private array $buildings = [
        // Residential
        ['name' => 'Apartment Building', 'type' => BuildingType::Living, 'price' => 50000, 'income' => 1200, 'upgrade_coefficient' => 1.25, 'employees_count' => 2, 'residents_count' => 24, 'happiness' => 8, 'attractiveness' => 3],
        ['name' => 'Multi-Family Complex', 'type' => BuildingType::Living, 'price' => 250000, 'income' => 6000, 'upgrade_coefficient' => 1.30, 'employees_count' => 8, 'residents_count' => 120, 'happiness' => 5, 'attractiveness' => -2],
        ['name' => 'Cottage', 'type' => BuildingType::Living, 'price' => 120000, 'income' => 2500, 'upgrade_coefficient' => 1.20, 'employees_count' => 1, 'residents_count' => 6, 'happiness' => 12, 'attractiveness' => 6],

        // Shopping
        ['name' => 'Shop', 'type' => BuildingType::Shopping, 'price' => 40000, 'income' => 2000, 'upgrade_coefficient' => 1.35, 'employees_count' => 5, 'residents_count' => 0, 'happiness' => 4, 'attractiveness' => 7],
        ['name' => 'Shopping Mall', 'type' => BuildingType::Shopping, 'price' => 300000, 'income' => 12000, 'upgrade_coefficient' => 1.40, 'employees_count' => 40, 'residents_count' => 0, 'happiness' => 6, 'attractiveness' => 15],
        ['name' => 'Market', 'type' => BuildingType::Shopping, 'price' => 80000, 'income' => 3500, 'upgrade_coefficient' => 1.28, 'employees_count' => 12, 'residents_count' => 0, 'happiness' => 3, 'attractiveness' => 5],

        // Services
        ['name' => 'Hair Salon', 'type' => BuildingType::Service, 'price' => 25000, 'income' => 900, 'upgrade_coefficient' => 1.22, 'employees_count' => 3, 'residents_count' => 0, 'happiness' => 5, 'attractiveness' => 4],
        ['name' => 'Hospital', 'type' => BuildingType::Service, 'price' => 400000, 'income' => 5000, 'upgrade_coefficient' => 1.18, 'employees_count' => 60, 'residents_count' => 0, 'happiness' => 20, 'attractiveness' => 2],
        ['name' => 'School', 'type' => BuildingType::Service, 'price' => 220000, 'income' => 2000, 'upgrade_coefficient' => 1.15, 'employees_count' => 30, 'residents_count' => 0, 'happiness' => 15, 'attractiveness' => 1],
        ['name' => 'Cafe', 'type' => BuildingType::Service, 'price' => 60000, 'income' => 2200, 'upgrade_coefficient' => 1.32, 'employees_count' => 8, 'residents_count' => 0, 'happiness' => 7, 'attractiveness' => 9],

        // Production
        ['name' => 'Factory', 'type' => BuildingType::Production, 'price' => 500000, 'income' => 20000, 'upgrade_coefficient' => 1.45, 'employees_count' => 120, 'residents_count' => 0, 'happiness' => -10, 'attractiveness' => -12],
        ['name' => 'Sawmill', 'type' => BuildingType::Production, 'price' => 90000, 'income' => 4000, 'upgrade_coefficient' => 1.38, 'employees_count' => 25, 'residents_count' => 0, 'happiness' => -5, 'attractiveness' => -8],
        ['name' => 'Bakery', 'type' => BuildingType::Production, 'price' => 35000, 'income' => 1500, 'upgrade_coefficient' => 1.30, 'employees_count' => 10, 'residents_count' => 0, 'happiness' => 3, 'attractiveness' => 4],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->buildings as $building) {
            Building::updateOrCreate(
                ['name' => $building['name']],
                $building,
            );
        }
    }
}
