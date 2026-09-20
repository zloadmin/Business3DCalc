<?php

namespace App\Models;

use App\Enums\BuildingType;
use Database\Factories\BuildingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'type',
    'price',
    'income',
    'upgrade_coefficient',
    'employees_count',
    'residents_count',
    'happiness',
    'attractiveness',
])]
class Building extends Model
{
    /** @use HasFactory<BuildingFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => BuildingType::class,
            'price' => 'integer',
            'income' => 'integer',
            'upgrade_coefficient' => 'decimal:3',
            'employees_count' => 'integer',
            'residents_count' => 'integer',
            'happiness' => 'integer',
            'attractiveness' => 'integer',
        ];
    }

    /**
     * Income the building generates at the given upgrade level.
     *
     * Level 1 is the base building; each further level multiplies
     * income by the upgrade coefficient.
     */
    public function incomeAtLevel(int $level): float
    {
        if ($level < 1) {
            return 0.0;
        }

        return (float) $this->income * ((float) $this->upgrade_coefficient ** ($level - 1));
    }

    /**
     * Scope a query to only include buildings of the given type.
     *
     * @param  Builder<Building>  $query
     * @return Builder<Building>
     */
    public function scopeOfType(Builder $query, BuildingType $type): Builder
    {
        return $query->where('type', $type->value);
    }
}
