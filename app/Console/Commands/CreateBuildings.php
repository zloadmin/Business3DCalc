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
     * @var list<array{name: string, type: BuildingType, price: int, income: int, upgrade_time_coefficient: float, employees: int, residents_capacity: int, happiness: float, attractiveness: float}>
     */


    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $created = [];
        $skipped = [];

        foreach (Building::$buildings as $id => $building) {
            $building['id'] = ++$id;
            Building::create(
                $building
            );

            $created[] = $building['name'];
        }

        foreach ($created as $name) {
            $this->components->twoColumnDetail($name, 'created');
        }

        $this->newLine();
        $this->components->info(sprintf(
            'Created %d building(s)',
            count($created),

        ));

        return self::SUCCESS;
    }
}
