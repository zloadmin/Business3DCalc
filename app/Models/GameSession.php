<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameSession extends Model
{
    protected $guarded = [];
    protected $casts = [
        'last_tick_at' => 'datetime',
    ];
    const float BASE_HAPPINESS = 50.0;
    const float BASE_ATTRACTIVENESS = 50.0;

    public function gameBuildings(): \Illuminate\Database\Eloquent\Relations\HasMany|GameSession
    {
        return $this->hasMany(GameBuilding::class);
    }

    /**
     * @param Building $building
     * @return void
     */
    public function buyBuilding(Building $building): void
    {
        if ($building->price > $this->money) throw new \LogicException('Not enough money');

        $this->gameBuildings()->create([
            'building_id'    => $building->id,
            'level'          => 1,
            'next_income_at' => $this->game_time + Building::INCOME_PERIOD,
            'built_at'       => $this->game_time
        ]);
        $this->decrement('money', $building->price);
    }

    public function upgradeBuilding(GameBuilding $building): void
    {
        $nextLevel = $building->level + 1;
        $upgradeMoney = $building->building->getUpgradePrice($nextLevel);

        // 0 check enough money
        if ($upgradeMoney > $this->money) throw new \LogicException('Not enough money');
        // 1 get seconds to next level
        $upgradeTime = $building->building->getUpgradeTime($nextLevel);

        // 2 move income money after update
        $building->update([
            'upgrade_finish_at' => $this->game_time + $upgradeTime,
            'next_income_at'    => $building->next_income_at ? $building->next_income_at + $upgradeTime : null,
        ]);

        $this->decrement('money', $upgradeMoney);
    }

    public function getRealSeconds(): int
    {

        return $this->last_tick_at->diffInSeconds(now());
    }
    public function getGameSeconds(): int
    {
        return (int) floor(
            $this->getRealSeconds() * $this->time_speed
        );
    }

    public function getHappiness() : float
    {
        $base = self::BASE_HAPPINESS; // 50.0

        $weightedHappiness = 0.0;
        $totalEmployees = 0;

        foreach ($this->gameBuildings()->with('building')->get() as $gameBuilding) {
            $happiness = $gameBuilding->building->getHappiness($gameBuilding->level);
            $employees = $gameBuilding->building->getEmployees($gameBuilding->level);

            if ($employees <= 0) {
                continue;
            }

            $weightedHappiness += $happiness * $employees;
            $totalEmployees += $employees;
        }

        // Нет зданий / сотрудников — остаёмся на базовом уровне
        if ($totalEmployees === 0) {
            return $base;
        }

        $averageHappiness = $weightedHappiness / $totalEmployees;

        return max(
            0,
            min(100, $base + $averageHappiness * 0.5)
        );
    }
    public function getAttractiveness() : float
    {
        $base = self::BASE_ATTRACTIVENESS; // 50.0

        $weightedAttractiveness = 0.0;
        $totalIncome = 0;

        foreach ($this->gameBuildings()->with('building')->get() as $gameBuilding) {
            $happiness = $gameBuilding->building->getAttractiveness($gameBuilding->level);
            $income = $gameBuilding->building->getIncome($gameBuilding->level);

            if ($income <= 0) {
                continue;
            }

            $weightedAttractiveness += $happiness * $income;
            $totalIncome += $income;
        }

        // Нет зданий / сотрудников — остаёмся на базовом уровне
        if ($totalIncome === 0) {
            return $base;
        }

        $averageAttractiveness = $weightedAttractiveness / $totalIncome;

        return max(
            0,
            min(100, $base + $averageAttractiveness * 0.5)
        );
    }

    function getBestBuildingAction(): ?array {


        $totalBuildings = $this->gameBuildings()->count();
        $candidates = [];
        $buildingLimit  = 100;

        // ---------- 1. Evaluate upgrades of existing buildings ----------
        foreach ($this->gameBuildings()->with('building')->get() as $idx => $gameBuilding) {
            $name  = $gameBuilding->building->name;
            $level = $gameBuilding->level;
            $cost = $gameBuilding->building->getUpgradePrice($gameBuilding->level + 1);

            if ($cost > $this->money) continue; // can't afford
            if($gameBuilding->upgrade_finish_at !== null) continue; // upgrading right now

            $deltaI = $gameBuilding->building->getIncome($gameBuilding->level + 1);
            $deltaH = $gameBuilding->building->getHappiness($gameBuilding->level + 1);
            $deltaA = $gameBuilding->building->getAttractiveness($gameBuilding->level + 1);
            $time   = $gameBuilding->building->getUpgradeTime($gameBuilding->level + 1);

            $candidates[] = [
                'action'  => 'upgrade',
                'type'    => $name,
                'index'   => $idx,
                'level'   => $level,
                'cost'    => $cost,
                'time'    => $time,
                'delta_i' => $deltaI,
                'delta_h' => $deltaH,
                'delta_a' => $deltaA,
                'game_building_id' => $gameBuilding->id,
            ];
        }

        // ---------- 2. Evaluate building a new one of each type ----------
        if ($totalBuildings < $buildingLimit) {
            foreach (Building::query()->get() as $building) {
                $cost = $building->price;
                if ($cost > $this->money) continue;

                $deltaI = $building->getIncome(1);
                $deltaH = $building->getHappiness(1);
                $deltaA = $building->getAttractiveness(1);
                $time   = $building->getUpgradeTime(2);

                $candidates[] = [
                    'action'  => 'build',
                    'type'    => $building->name,
                    'index'   => null,
                    'level'   => 1, // will become 2
                    'cost'    => $cost,
                    'time'    => $time,
                    'delta_i' => $deltaI,
                    'delta_h' => $deltaH,
                    'delta_a' => $deltaA,
                    'building_id' => $building->id,
                ];
            }
        }

        // ---------- 3. Score every candidate ----------
        $best = null;
        $bestScore = -INF;
        $happiness = $this->getHappiness();
        $attractiveness = $this->getAttractiveness();

        // Weights – tune these
        $wH = ($happiness < 50) ? 0.9 : 0.35; // stronger early game
        $wA = ($attractiveness < 50) ? 0.9 : 0.35;

        foreach ($candidates as $c) {
            $hAfter = $happiness + $c['delta_h'];
            $aAfter = $attractiveness + $c['delta_a'];

            // Hard constraint: never go ≤ 0
            if ($hAfter <= 0 || $aAfter <= 0) {
                continue;
            }

            // Soft bonuses / penalties
            $pH = $c['delta_h'] / max(1.0, abs($happiness));
            $pA = $c['delta_a'] / max(1.0, abs($attractiveness));

            // ROI
            $roi = $c['delta_i'] / max(1, $c['cost']);

            // Time discount (prefer faster actions)
            $timeFactor = 1.0 / (1.0 + $c['time'] / 3600.0);

            // Building limit pressure
            $limitFactor = 1.0 - ($totalBuildings / $buildingLimit);

            $score = $roi * $timeFactor * (1.0 + $wH * $pH + $wA * $pA) * $limitFactor;

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $c;
                $best['score'] = $score;
            }
        }

        return $best;
    }


}
