<?php

namespace App\Console\Commands;

use App\Enums\BuildingType;
use App\Models\Building;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('buildings:create')]
#[Description('Create the starting buildings, four for every building type')]
class CreateBuildings extends Command
{
    /**
     * Buildings to create, four per type.
     *
     * @var list<array{name: string, type: BuildingType, price: int, income: int, upgrade_coefficient: float, employees_count: int, residents_count: int, happiness: int, attractiveness: int}>
     */
    private array $buildings = [
        // Living
        ['name' => 'Hotel 1', 'type' => BuildingType::Living, 'price' => 80_000, 'income' => 2_200, 'upgrade_coefficient' => 1.24, 'employees_count' => 12, 'residents_count' => 40, 'happiness' => 4, 'attractiveness' => 6],
        ['name' => 'Hotel 2', 'type' => BuildingType::Living, 'price' => 180_000, 'income' => 4_800, 'upgrade_coefficient' => 1.27, 'employees_count' => 24, 'residents_count' => 85, 'happiness' => 6, 'attractiveness' => 9],
        ['name' => 'Hotel 3', 'type' => BuildingType::Living, 'price' => 380_000, 'income' => 9_500, 'upgrade_coefficient' => 1.31, 'employees_count' => 45, 'residents_count' => 160, 'happiness' => 8, 'attractiveness' => 12],
        ['name' => 'Hotel 4', 'type' => BuildingType::Living, 'price' => 750_000, 'income' => 18_000, 'upgrade_coefficient' => 1.35, 'employees_count' => 80, 'residents_count' => 300, 'happiness' => 10, 'attractiveness' => 16],

        // Service
        ['name' => 'Police 1', 'type' => BuildingType::Service, 'price' => 120_000, 'income' => 800, 'upgrade_coefficient' => 1.12, 'employees_count' => 20, 'residents_count' => 0, 'happiness' => 14, 'attractiveness' => 2],
        ['name' => 'Police 2', 'type' => BuildingType::Service, 'price' => 260_000, 'income' => 1_800, 'upgrade_coefficient' => 1.15, 'employees_count' => 42, 'residents_count' => 0, 'happiness' => 19, 'attractiveness' => 3],
        ['name' => 'Hospital 1', 'type' => BuildingType::Service, 'price' => 300_000, 'income' => 2_600, 'upgrade_coefficient' => 1.16, 'employees_count' => 50, 'residents_count' => 0, 'happiness' => 22, 'attractiveness' => 1],
        ['name' => 'Hospital 2', 'type' => BuildingType::Service, 'price' => 620_000, 'income' => 5_800, 'upgrade_coefficient' => 1.19, 'employees_count' => 95, 'residents_count' => 0, 'happiness' => 28, 'attractiveness' => 2],

        // Shopping
        ['name' => 'Shop 1', 'type' => BuildingType::Shopping, 'price' => 30_000, 'income' => 1_500, 'upgrade_coefficient' => 1.30, 'employees_count' => 4, 'residents_count' => 0, 'happiness' => 3, 'attractiveness' => 6],
        ['name' => 'Shop 2', 'type' => BuildingType::Shopping, 'price' => 70_000, 'income' => 3_200, 'upgrade_coefficient' => 1.33, 'employees_count' => 9, 'residents_count' => 0, 'happiness' => 5, 'attractiveness' => 9],
        ['name' => 'Shop 3', 'type' => BuildingType::Shopping, 'price' => 150_000, 'income' => 6_800, 'upgrade_coefficient' => 1.36, 'employees_count' => 18, 'residents_count' => 0, 'happiness' => 6, 'attractiveness' => 12],
        ['name' => 'Shop 4', 'type' => BuildingType::Shopping, 'price' => 320_000, 'income' => 14_000, 'upgrade_coefficient' => 1.39, 'employees_count' => 35, 'residents_count' => 0, 'happiness' => 8, 'attractiveness' => 15],

        // Production
        ['name' => 'Windmill 1', 'type' => BuildingType::Production, 'price' => 45_000, 'income' => 2_600, 'upgrade_coefficient' => 1.34, 'employees_count' => 3, 'residents_count' => 0, 'happiness' => 1, 'attractiveness' => -2],
        ['name' => 'Windmill 2', 'type' => BuildingType::Production, 'price' => 95_000, 'income' => 5_500, 'upgrade_coefficient' => 1.37, 'employees_count' => 6, 'residents_count' => 0, 'happiness' => 2, 'attractiveness' => -3],
        ['name' => 'Windmill 3', 'type' => BuildingType::Production, 'price' => 200_000, 'income' => 11_000, 'upgrade_coefficient' => 1.40, 'employees_count' => 10, 'residents_count' => 0, 'happiness' => 3, 'attractiveness' => -4],
        ['name' => 'Windmill 4', 'type' => BuildingType::Production, 'price' => 420_000, 'income' => 23_000, 'upgrade_coefficient' => 1.43, 'employees_count' => 16, 'residents_count' => 0, 'happiness' => 4, 'attractiveness' => -5],
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $created = [];
        $skipped = [];

        foreach ($this->buildings as $building) {
            $model = Building::firstOrCreate(
                ['name' => $building['name']],
                $building,
            );

            if ($model->wasRecentlyCreated) {
                $created[] = $building['name'];
            } else {
                $skipped[] = $building['name'];
            }
        }

        foreach ($created as $name) {
            $this->components->twoColumnDetail($name, 'created');
        }

        $this->newLine();
        $this->components->info(sprintf(
            'Created %d building(s), skipped %d that already exist.',
            count($created),
            count($skipped),
        ));

        return self::SUCCESS;
    }
}
