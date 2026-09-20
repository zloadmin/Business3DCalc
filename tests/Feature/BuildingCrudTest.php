<?php

namespace Tests\Feature;

use App\Enums\BuildingType;
use App\Models\Building;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuildingCrudTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Shopping Mall',
            'type' => BuildingType::Shopping->value,
            'price' => 100_000,
            'income' => 5_000,
            'upgrade_coefficient' => 1.5,
            'employees_count' => 20,
            'residents_count' => 0,
            'happiness' => 5,
            'attractiveness' => 10,
        ], $overrides);
    }

    public function test_index_lists_buildings(): void
    {
        $building = Building::factory()->create(['name' => 'Apartment Building']);

        $response = $this->get(route('buildings.index'));

        $response->assertOk();
        $response->assertSee('Apartment Building');
    }

    public function test_index_can_be_filtered_by_type(): void
    {
        Building::factory()->create(['name' => 'Visible Factory', 'type' => BuildingType::Production]);
        Building::factory()->create(['name' => 'Hidden House', 'type' => BuildingType::Living]);

        $response = $this->get(route('buildings.index', ['type' => BuildingType::Production->value]));

        $response->assertOk();
        $response->assertSee('Visible Factory');
        $response->assertDontSee('Hidden House');
    }

    public function test_create_form_is_displayed(): void
    {
        $this->get(route('buildings.create'))->assertOk();
    }

    public function test_a_building_can_be_stored(): void
    {
        $response = $this->post(route('buildings.store'), $this->payload());

        $response->assertRedirect(route('buildings.index'));
        $response->assertSessionHas('status');

        $building = Building::first();

        $this->assertNotNull($building);
        $this->assertSame('Shopping Mall', $building->name);
        $this->assertSame(BuildingType::Shopping, $building->type);
        $this->assertSame(100_000, $building->price);
        $this->assertSame(5_000, $building->income);
        $this->assertSame(20, $building->employees_count);
        $this->assertSame(10, $building->attractiveness);
    }

    public function test_residents_are_reset_for_non_living_types(): void
    {
        $this->post(route('buildings.store'), $this->payload([
            'type' => BuildingType::Production->value,
            'residents_count' => 42,
        ]));

        $this->assertSame(0, Building::first()->residents_count);
    }

    public function test_residents_are_kept_for_living_buildings(): void
    {
        $this->post(route('buildings.store'), $this->payload([
            'type' => BuildingType::Living->value,
            'residents_count' => 42,
        ]));

        $this->assertSame(42, Building::first()->residents_count);
    }

    public function test_storing_requires_a_valid_type(): void
    {
        $response = $this->post(route('buildings.store'), $this->payload(['type' => 'unknown']));

        $response->assertSessionHasErrors('type');
        $this->assertDatabaseCount('buildings', 0);
    }

    public function test_storing_requires_a_name(): void
    {
        $response = $this->post(route('buildings.store'), $this->payload(['name' => '']));

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('buildings', 0);
    }

    public function test_upgrade_coefficient_must_be_at_least_one(): void
    {
        $response = $this->post(route('buildings.store'), $this->payload(['upgrade_coefficient' => 0.5]));

        $response->assertSessionHasErrors('upgrade_coefficient');
        $this->assertDatabaseCount('buildings', 0);
    }

    public function test_show_displays_a_building(): void
    {
        $building = Building::factory()->create(['name' => 'Bakery']);

        $response = $this->get(route('buildings.show', $building));

        $response->assertOk();
        $response->assertSee('Bakery');
    }

    public function test_edit_form_is_prefilled(): void
    {
        $building = Building::factory()->create(['name' => 'Sawmill']);

        $response = $this->get(route('buildings.edit', $building));

        $response->assertOk();
        $response->assertSee('Sawmill');
    }

    public function test_a_building_can_be_updated(): void
    {
        $building = Building::factory()->create();

        $response = $this->put(route('buildings.update', $building), $this->payload([
            'name' => 'Updated Building',
            'price' => 777,
        ]));

        $response->assertRedirect(route('buildings.index'));

        $this->assertSame('Updated Building', $building->fresh()->name);
        $this->assertSame(777, $building->fresh()->price);
    }

    public function test_a_building_can_be_deleted(): void
    {
        $building = Building::factory()->create();

        $response = $this->delete(route('buildings.destroy', $building));

        $response->assertRedirect(route('buildings.index'));
        $this->assertDatabaseCount('buildings', 0);
    }
}
