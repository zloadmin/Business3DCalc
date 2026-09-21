<?php

namespace App\Models;

use App\Enums\BuildingType;
use Carbon\CarbonInterval;
use Database\Factories\BuildingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Building extends Model
{
    /** @use HasFactory<BuildingFactory> */
    use HasFactory;

    protected $guarded = [];
    const int INCOME_PERIOD = 60;
    static array $buildings = [
        // Living
        [
            'name'                      => 'Budget Hotel',
            'type'                      => BuildingType::Living,
            'price'                     => 100,
            'income'                    => 10,
            'upgrade_time_coefficient'  => 5,
            'upgrade_price_coefficient' => 1.2,
            'employees'                 => 2,
            'residents_capacity'        => 10,
            'happiness'                 => 2,
            'attractiveness'            => 2
        ],
        [
            'name'                      => 'City Hotel',
            'type'                      => BuildingType::Living,
            'price'                     => 500,
            'income'                    => 30,
            'upgrade_time_coefficient'  => 5,
            'upgrade_price_coefficient' => 1.2,
            'employees'                 => 4,
            'residents_capacity'        => 25,
            'happiness'                 => 4,
            'attractiveness'            => 5
        ],
        [
            'name'                      => 'Grand Hotel',
            'type'                      => BuildingType::Living,
            'price'                     => 1500,
            'income'                    => 80,
            'upgrade_time_coefficient'  => 5,
            'upgrade_price_coefficient' => 1.2,
            'employees'                 => 8,
            'residents_capacity'        => 50,
            'happiness'                 => 7,
            'attractiveness'            => 9
        ],
        [
            'name'                      => 'Luxury Hotel',
            'type'                      => BuildingType::Living,
            'price'                     => 4000,
            'income'                    => 180,
            'upgrade_time_coefficient'  => 5,
            'upgrade_price_coefficient' => 1.2,
            'employees'                 => 15,
            'residents_capacity'        => 90,
            'happiness'                 => 11,
            'attractiveness'            => 15
        ],

        // Production
        [
            'name'                      => 'Small Windmill',
            'type'                      => BuildingType::Production,
            'price'                     => 300,
            'income'                    => 100,
            'upgrade_time_coefficient'  => 5,
            'upgrade_price_coefficient' => 1.2,
            'employees'                 => 4,
            'happiness'                 => -2,
            'attractiveness'            => -3
        ],
        [
            'name'                      => 'Windmill Farm',
            'type'                      => BuildingType::Production,
            'price'                     => 1500,
            'income'                    => 500,
            'upgrade_time_coefficient'  => 5,
            'upgrade_price_coefficient' => 1.2,
            'employees'                 => 10,
            'happiness'                 => -4,
            'attractiveness'            => -6
        ],
        [
            'name'                      => 'Industrial Windmill',
            'type'                      => BuildingType::Production,
            'price'                     => 4500,
            'income'                    => 1500,
            'upgrade_time_coefficient'  => 5,
            'upgrade_price_coefficient' => 1.2,
            'employees'                 => 50,
            'happiness'                 => -7,
            'attractiveness'            => -9
        ],
        [
            'name'                      => 'Wind Power Plant',
            'type'                      => BuildingType::Production,
            'price'                     => 10000,
            'income'                    => 3500,
            'upgrade_time_coefficient'  => 5,
            'upgrade_price_coefficient' => 1.2,
            'employees'                 => 150,
            'happiness'                 => -10,
            'attractiveness'            => -12
        ],

        // Service
        [
            'name'                      => 'Police Booth',
            'type'                      => BuildingType::Service,
            'price'                     => 200,
            'income'                    => -50,
            'upgrade_time_coefficient'  => 5,
            'upgrade_price_coefficient' => 1.2,
            'employees'                 => 5,
            'happiness'                 => 15,
            'attractiveness'            => 5
        ],
        [
            'name'                      => 'Police Station',
            'type'                      => BuildingType::Service,
            'price'                     => 1000,
            'income'                    => -300,
            'upgrade_time_coefficient'  => 5,
            'upgrade_price_coefficient' => 1.2,
            'employees'                 => 15,
            'happiness'                 => 5,
            'attractiveness'            => 20
        ],
        [
            'name'                      => 'Clinic',
            'type'                      => BuildingType::Service,
            'price'                     => 300,
            'income'                    => -100,
            'upgrade_time_coefficient'  => 5,
            'upgrade_price_coefficient' => 1.2,
            'employees'                 => 7,
            'happiness'                 => 15,
            'attractiveness'            => 20
        ],
        [
            'name'                      => 'Medical Center',
            'type'                      => BuildingType::Service,
            'price'                     => 3000,
            'income'                    => -1000,
            'upgrade_time_coefficient'  => 5,
            'upgrade_price_coefficient' => 1.2,
            'employees'                 => 30,
            'happiness'                 => 30,
            'attractiveness'            => 50
        ],

        // Shopping
        [
            'name'                      => 'Corner Shop',
            'type'                      => BuildingType::Shopping,
            'price'                     => 1000,
            'income'                    => 500,
            'upgrade_time_coefficient'  => 5,
            'upgrade_price_coefficient' => 1.5,
            'employees'                 => 5,
            'happiness'                 => 4,
            'attractiveness'            => 10
        ],
        [
            'name'                      => 'Supermarket',
            'type'                      => BuildingType::Shopping,
            'price'                     => 10000,
            'income'                    => 3000,
            'upgrade_time_coefficient'  => 6,
            'upgrade_price_coefficient' => 2,
            'employees'                 => 10,
            'happiness'                 => 20,
            'attractiveness'            => 15
        ],
        [
            'name'                      => 'Shopping Center',
            'type'                      => BuildingType::Shopping,
            'price'                     => 100000,
            'income'                    => 15000,
            'upgrade_time_coefficient'  => 8,
            'upgrade_price_coefficient' => 3,
            'employees'                 => 30,
            'happiness'                 => 15,
            'attractiveness'            => 30
        ],
        [
            'name'                      => 'Grand Mall',
            'type'                      => BuildingType::Shopping,
            'price'                     => 1000000,
            'income'                    => 300000,
            'upgrade_time_coefficient'  => 10,
            'upgrade_price_coefficient' => 5,
            'employees'                 => 100,
            'happiness'                 => 30,
            'attractiveness'            => 50
        ],
    ];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type'                => BuildingType::class,
            'price'               => 'integer',
            'income'              => 'integer',
            'upgrade_time_coefficient' => 'decimal:3',
            'employees_count'     => 'integer',
            'residents_count'     => 'integer',
            'happiness'           => 'integer',
            'attractiveness'      => 'integer',
        ];
    }

    public function getIncome(int $level = 1): float
    {
        return round($this->income * $level) * 5;
    }
    public function getIncomeBeforeNextLevel(int $level): float
    {
        $time = $this->getUpgradeTime($level);
        $income = $this->getIncome($level);
        $times = $time / self::INCOME_PERIOD;


        return round($income * $times, 2);
    }

    /**
     * Scope a query to only include buildings of the given type.
     *
     * @param Builder<Building> $query
     * @return Builder<Building>
     */
    public function scopeOfType(Builder $query, BuildingType $type): Builder
    {
        return $query->where('type', $type->value);
    }

    public function getUpgradePrice(int $level): int
    {
        return ($this->price * $this->upgrade_price_coefficient) * ($level * $this->upgrade_price_coefficient);
    }

    public function getUpgradeTime(int $level = 2): int
    {
        return $this->roundTime(60 * ($level * $this->upgrade_time_coefficient));
    }

    public function roundTime($seconds): int
    {
        return (int)floor($seconds / 30) * 30;
    }

    /**
     * @throws \Exception
     */
    public function getUpgradeTimeText(int $level): string
    {

        return $this->secondsToString($this->getUpgradeTime($level));
    }

    public function secondsToString($seconds): string
    {
        return CarbonInterval::seconds($seconds)->cascade()->forHumans();
    }

    public function getTotalSpend(int $to_level): float
    {
        $total = $this->price;
        for ($level = 1; $level <= $to_level; $level++) {
            $total += $this->getUpgradePrice($level);
        }
        return $total;
    }
    public function getTotalIncome(int $to_level): float
    {
        $total = 0;
        for ($level = 1; $level <= $to_level; $level++) {
            $total += $this->getIncomeBeforeNextLevel($level);
        }
        return $total;
    }

    public function getTotalTime(int $to_level): float
    {
        $total = $this->building_time;
        for ($level = 1; $level <= $to_level; $level++) {
            $total += $this->getUpgradeTime($level);
        }
        return $total;
    }

    public function getTotalTimeText(int $level): string
    {
        return $this->secondsToString($this->getTotalTime($level));
    }

    public function getResidentsCapacity(int $level): int
    {
        if ($this->type->providesResidents()) return $this->residents_capacity * pow($level, 0.5);
        return 0;
    }
    public function getHappiness(int $level = 1): float
    {
        return round(
                ($this->happiness * (1 + log($level, 2))) / 0.02
            ) * 0.02;
    }
    public function getAttractiveness(int $level = 1): float
    {
        return round(
                ($this->attractiveness * (1 + log($level, 2))) / 0.04
            ) * 0.04;

//        $baseAttractiveness = 4 * (1 + log($level, 2));
//
//        $load = $this->city_population / $this->city_capacity;
//
//        if ($load <= 0.8) {
//            $multiplier = 1;
//        } else {
//            $penalty = ($load - 0.8) / 0.4;
//            $multiplier = max(0, 1 - $penalty);
//        }
//
//        $attractiveness = $baseAttractiveness * $multiplier;
//
//        return round($attractiveness / 0.05) * 0.05;
    }
    public function getEmployees(int $level) : int
    {

        return floor(round($this->employees * pow($level, 0.4)));
    }
}
