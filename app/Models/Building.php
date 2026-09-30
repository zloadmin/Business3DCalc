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
            'name'                      => 'House 1',
            'type'                      => BuildingType::Living,
            'price'                     => 100,
            'income'                    => -50,
            'upgrade_time_coefficient'  => 5,
            'upgrade_price_coefficient' => 1.2,
            'employees'                 => 0,
            'residents_capacity'        => 10,
            'happiness'                 => -3,
            'attractiveness'            => 2,
        ],
        [
            'name'                      => 'House 2',
            'type'                      => BuildingType::Living,
            'price'                     => 500,
            'income'                    => -180,
            'upgrade_time_coefficient'  => 5,
            'upgrade_price_coefficient' => 1.2,
            'employees'                 => 0,
            'residents_capacity'        => 25,
            'happiness'                 => -8,
            'attractiveness'            => 3,
        ],
        [
            'name'                      => 'House 3',
            'type'                      => BuildingType::Living,
            'price'                     => 1500,
            'income'                    => -600,
            'upgrade_time_coefficient'  => 5,
            'upgrade_price_coefficient' => 1.2,
            'employees'                 => 0,
            'residents_capacity'        => 50,
            'happiness'                 => -18,
            'attractiveness'            => 4,
        ],
        [
            'name'                      => 'House 4',
            'type'                      => BuildingType::Living,
            'price'                     => 4000,
            'income'                    => -1500,
            'upgrade_time_coefficient'  => 5,
            'upgrade_price_coefficient' => 1.2,
            'employees'                 => 0,
            'residents_capacity'        => 90,
            'happiness'                 => -35,
            'attractiveness'            => 8,
        ],

        // Production
        [
            'name'                      => 'Small Windmill',
            'type'                      => BuildingType::Production,
            'price'                     => 300,
            'income'                    => 350,
            'upgrade_time_coefficient'  => 5,
            'upgrade_price_coefficient' => 1.2,
            'employees'                 => 7,
            'happiness'                 => -4,
            'attractiveness'            => -5,
        ],
        [
            'name'                      => 'Windmill Farm',
            'type'                      => BuildingType::Production,
            'price'                     => 1500,
            'income'                    => 1400,
            'upgrade_time_coefficient'  => 5,
            'upgrade_price_coefficient' => 1.2,
            'employees'                 => 20,
            'happiness'                 => -8,
            'attractiveness'            => -10,
        ],
        [
            'name'                      => 'Industrial Windmill',
            'type'                      => BuildingType::Production,
            'price'                     => 4500,
            'income'                    => 4000,
            'upgrade_time_coefficient'  => 5,
            'upgrade_price_coefficient' => 1.2,
            'employees'                 => 80,
            'happiness'                 => -14,
            'attractiveness'            => -15,
        ],
        [
            'name'                      => 'Wind Power Plant',
            'type'                      => BuildingType::Production,
            'price'                     => 10000,
            'income'                    => 9000,
            'upgrade_time_coefficient'  => 5,
            'upgrade_price_coefficient' => 1.2,
            'employees'                 => 220,
            'happiness'                 => -20,
            'attractiveness'            => -22,
        ],

        // Service
        [
            'name'                      => 'Police Booth',
            'type'                      => BuildingType::Service,
            'price'                     => 200,
            'income'                    => -350,
            'upgrade_time_coefficient'  => 5,
            'upgrade_price_coefficient' => 1.2,
            'employees'                 => 7,
            'happiness'                 => 18,
            'attractiveness'            => 6,
        ],
        [
            'name'                      => 'Police Station',
            'type'                      => BuildingType::Service,
            'price'                     => 1000,
            'income'                    => -2200,
            'upgrade_time_coefficient'  => 5,
            'upgrade_price_coefficient' => 1.2,
            'employees'                 => 22,
            'happiness'                 => 14,
            'attractiveness'            => 22,
        ],
        [
            'name'                      => 'Clinic',
            'type'                      => BuildingType::Service,
            'price'                     => 300,
            'income'                    => -700,
            'upgrade_time_coefficient'  => 5,
            'upgrade_price_coefficient' => 1.2,
            'employees'                 => 9,
            'happiness'                 => 16,
            'attractiveness'            => 18,
        ],
        [
            'name'                      => 'Medical Center',
            'type'                      => BuildingType::Service,
            'price'                     => 3000,
            'income'                    => -6500,
            'upgrade_time_coefficient'  => 5,
            'upgrade_price_coefficient' => 1.2,
            'employees'                 => 45,
            'happiness'                 => 38,
            'attractiveness'            => 45,
        ],

        // Shopping
        [
            'name'                      => 'Corner Shop',
            'type'                      => BuildingType::Shopping,
            'price'                     => 1000,
            'income'                    => 800,
            'upgrade_time_coefficient'  => 5,
            'upgrade_price_coefficient' => 1.5,
            'employees'                 => 10,
            'happiness'                 => -8,
            'attractiveness'            => 7,
        ],
        [
            'name'                      => 'Supermarket',
            'type'                      => BuildingType::Shopping,
            'price'                     => 10000,
            'income'                    => 5000,
            'upgrade_time_coefficient'  => 6,
            'upgrade_price_coefficient' => 2,
            'employees'                 => 30,
            'happiness'                 => -22,
            'attractiveness'            => 10,
        ],
        [
            'name'                      => 'Shopping Center',
            'type'                      => BuildingType::Shopping,
            'price'                     => 100000,
            'income'                    => 22000,
            'upgrade_time_coefficient'  => 8,
            'upgrade_price_coefficient' => 3,
            'employees'                 => 90,
            'happiness'                 => -35,
            'attractiveness'            => 18,
        ],
        [
            'name'                      => 'Grand Mall',
            'type'                      => BuildingType::Shopping,
            'price'                     => 1000000,
            'income'                    => 280000,
            'upgrade_time_coefficient'  => 10,
            'upgrade_price_coefficient' => 5,
            'employees'                 => 280,
            'happiness'                 => -60,
            'attractiveness'            => 28,
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
            'type'                     => BuildingType::class,
            'price'                    => 'integer',
            'income'                   => 'integer',
            'upgrade_time_coefficient' => 'decimal:3',
            'employees_count'          => 'integer',
            'residents_count'          => 'integer',
            'happiness'                => 'integer',
            'attractiveness'           => 'integer',
        ];
    }

    public function getIncome(int $level = 1): int
    {
        return (int)floor(floor($this->income * $level) * 5);
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
        if (!$this->type->providesResidents()) return 0;
        return (int) floor(
            $this->residentsCapacity * sqrt($level)
        );
    }

    public function getHappiness(int $level = 1): float
    {
        $multiplier = 1 + (log($level, 2) * 1.35); // быстрее растёт

        // Если значение отрицательное — усиливаем штраф сильнее
        if ($this->happiness < 0) {
            $multiplier *= 1.25;
        }

        return round(($this->happiness * $multiplier) / 0.02) * 0.02;
    }

    public function getAttractiveness(int $level = 1): float
    {
        $multiplier = 1 + (log($level, 2) * 1.25);

        // Штрафы привлекательности тоже усиливаем
        if ($this->attractiveness < 0) {
            $multiplier *= 1.2;
        }

        return round(($this->attractiveness * $multiplier) / 0.04) * 0.04;
    }

    public function getEmployees(int $level): int
    {

        return floor(round($this->employees * pow($level, 0.4)));
    }
}
