<?php

namespace Tests\Unit;

use App\Enums\BuildingType;
use App\Models\Building;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class BuildingTest extends TestCase
{
    public function test_type_enum_has_all_four_types(): void
    {
        $values = array_map(fn ($case) => $case->value, BuildingType::cases());

        $this->assertSame(['shopping', 'living', 'service', 'production'], $values);
    }

    public function test_only_living_buildings_provide_residents(): void
    {
        $this->assertTrue(BuildingType::Living->providesResidents());
        $this->assertFalse(BuildingType::Shopping->providesResidents());
        $this->assertFalse(BuildingType::Service->providesResidents());
        $this->assertFalse(BuildingType::Production->providesResidents());
    }

    public function test_income_at_level_applies_the_upgrade_coefficient(): void
    {
        $building = new Building([
            'income' => 1000,
            'upgrade_coefficient' => 1.5,
        ]);

        $this->assertSame(1000.0, $building->incomeAtLevel(1));
        $this->assertSame(1500.0, $building->incomeAtLevel(2));
        $this->assertSame(2250.0, $building->incomeAtLevel(3));
    }

    public function test_income_at_level_zero_is_free(): void
    {
        $building = new Building([
            'income' => 1000,
            'upgrade_coefficient' => 1.5,
        ]);

        $this->assertSame(0.0, $building->incomeAtLevel(0));
    }

    public function test_building_fillable_fields_match_the_requested_spec(): void
    {
        $attributes = (new ReflectionClass(Building::class))->getAttributes();

        $fillable = null;

        foreach ($attributes as $attribute) {
            if ($attribute->getName() === 'Illuminate\Database\Eloquent\Attributes\Fillable') {
                $fillable = $attribute->getArguments()[0];
            }
        }

        $this->assertNotNull($fillable, 'Building must declare a #[Fillable] attribute.');

        $this->assertSame([
            'name',
            'type',
            'price',
            'income',
            'upgrade_coefficient',
            'employees_count',
            'residents_count',
            'happiness',
            'attractiveness',
        ], $fillable);
    }
}
