<?php

namespace App\Models;

use App\Enums\BuildingType;
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

    public function trainItems(): \Illuminate\Database\Eloquent\Relations\HasMany|GameSession
    {
        return $this->hasMany(TrainItem::class);
    }

    public function getPopulation(): int
    {
        return $this->trainItems()->sum('result');
    }

    /**
     * @param Building $building
     * @return void
     */
    public function buyBuilding(Building $building): void
    {
        if ($building->price > $this->money) throw new \LogicException('Not enough money');
        if ($building->employees > $this->getUnemployed()) throw new \LogicException('Not enough unemployed');

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
        // get next employee amount, get current, and get required amount

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
        return (int)floor(
            $this->getRealSeconds() * $this->time_speed
        );
    }

    public function getHappiness(): float
    {
        $base = self::BASE_HAPPINESS; // 50.0

        $weightedHappiness = 0.0;
        $totalEmployees = 0;

        foreach ($this->gameBuildings()->with('building')->get() as $gameBuilding) {
            $happiness = $gameBuilding->building->getHappiness($gameBuilding->level);

//            if($gameBuilding->building->type->providesResidents()) {
//                $employees = 1; // TODO: default weight of living houses
//            } else {
//                $employees = $gameBuilding->building->getEmployees($gameBuilding->level);
//            }
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

    public function getAttractiveness(): float
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

    public function getPopulationCapacity(): int
    {
        $result = 0;
        foreach ($this->gameBuildings()->with('building')->get() as $gameBuilding) {
            $result += $gameBuilding->building->getResidentsCapacity($gameBuilding->level);
        }
        return $result;
    }

    public function getEmployees(): int
    {
        $result = 0;
        foreach ($this->gameBuildings()->with('building')->get() as $gameBuilding) {
            $result += $gameBuilding->building->getEmployees($gameBuilding->level);
        }
        return $result;
    }

    public function getTotalIncome(): int
    {
        $result = 0;
        foreach ($this->gameBuildings()->with('building')->get() as $gameBuilding) {
            $result += $gameBuilding->building->getIncome($gameBuilding->level);
        }
        return $result;
    }

    public function getUnemployed(): int
    {

        return $this->getPopulation() - $this->getEmployees();
    }

//    function getBestBuildingAction(): ?array
//    {
//
//
//        $totalBuildings = $this->gameBuildings()->count();
//        $candidates = [];
//        $buildingLimit = 100;
//
//        // ---------- 1. Evaluate upgrades of existing buildings ----------
//        foreach ($this->gameBuildings()->with('building')->get() as $idx => $gameBuilding) {
//            $name = $gameBuilding->building->name;
//            $level = $gameBuilding->level;
//            $cost = $gameBuilding->building->getUpgradePrice($gameBuilding->level + 1);
//
//            if ($cost > $this->money) continue; // can't afford
//            if ($gameBuilding->upgrade_finish_at !== null) continue; // upgrading right now
//
//            $deltaI = $gameBuilding->building->getIncome($gameBuilding->level + 1);
//            $deltaH = $gameBuilding->building->getHappiness($gameBuilding->level + 1);
//            $deltaA = $gameBuilding->building->getAttractiveness($gameBuilding->level + 1);
//            $time = $gameBuilding->building->getUpgradeTime($gameBuilding->level + 1);
//
//            $candidates[] = [
//                'action'           => 'upgrade',
//                'type'             => $name,
//                'index'            => $idx,
//                'level'            => $level,
//                'cost'             => $cost,
//                'time'             => $time,
//                'delta_i'          => $deltaI,
//                'delta_h'          => $deltaH,
//                'delta_a'          => $deltaA,
//                'game_building_id' => $gameBuilding->id,
//            ];
//        }
//
//        // ---------- 2. Evaluate building a new one of each type ----------
//        if ($totalBuildings < $buildingLimit) {
//            foreach (Building::query()->get() as $building) {
//                $cost = $building->price;
//                if ($cost > $this->money) continue;
//
//                $deltaI = $building->getIncome(1);
//                $deltaH = $building->getHappiness(1);
//                $deltaA = $building->getAttractiveness(1);
//                $time = $building->getUpgradeTime(2);
//
//                $candidates[] = [
//                    'action'      => 'build',
//                    'type'        => $building->name,
//                    'index'       => null,
//                    'level'       => 1,
//                    // will become 2
//                    'cost'        => $cost,
//                    'time'        => $time,
//                    'delta_i'     => $deltaI,
//                    'delta_h'     => $deltaH,
//                    'delta_a'     => $deltaA,
//                    'building_id' => $building->id,
//                ];
//            }
//        }
//
//        // ---------- 3. Score every candidate ----------
//        $best = null;
//        $bestScore = -INF;
//        $happiness = $this->getHappiness();
//        $attractiveness = $this->getAttractiveness();
//
//        // Weights – tune these
//        $wH = ($happiness < 50) ? 0.9 : 0.35; // stronger early game
//        $wA = ($attractiveness < 50) ? 0.9 : 0.35;
//
//        foreach ($candidates as $c) {
//            $hAfter = $happiness + $c['delta_h'];
//            $aAfter = $attractiveness + $c['delta_a'];
//
//            // Hard constraint: never go ≤ 0
//            if ($hAfter <= 0 || $aAfter <= 0) {
//                continue;
//            }
//
//            // Soft bonuses / penalties
//            $pH = $c['delta_h'] / max(1.0, abs($happiness));
//            $pA = $c['delta_a'] / max(1.0, abs($attractiveness));
//
//            // ROI
//            $roi = $c['delta_i'] / max(1, $c['cost']);
//
//            // Time discount (prefer faster actions)
//            $timeFactor = 1.0 / (1.0 + $c['time'] / 3600.0);
//
//            // Building limit pressure
//            $limitFactor = 1.0 - ($totalBuildings / $buildingLimit);
//
//            $score = $roi * $timeFactor * (1.0 + $wH * $pH + $wA * $pA) * $limitFactor;
//
//            if ($score > $bestScore) {
//                $bestScore = $score;
//                $best = $c;
//                $best['score'] = $score;
//            }
//        }
//
//        return $best;
//    }


//    private function findBestBuildingAction(string $need): ?array
//    {
//        $money = $this->money;
//
//        $happiness = $this->getHappiness();
//        $attractiveness = $this->getAttractiveness();
//
//        $freeResidents = $this->getUnemployed();
//        $buildingCount = $this->gameBuildings()->count();
//
//        $remainingTime = Building::INCOME_PERIOD;
//
//        /*
//         * Минимальный безопасный запас.
//         *
//         * Не позволяем действию увести показатели
//         * слишком близко к нулю.
//         */
//        $minimumHappiness = 5;
//        $minimumAttractiveness = 5;
//
//        /*
//         * Загружаем существующие здания.
//         */
//        $existingBuildings = $this->gameBuildings()
//            ->with('building')
//            ->get();
//
//        $bestAction = null;
//        $bestScore = -INF;
//
//        /*
//         * ==========================================================
//         * 1. BUILD NEW BUILDING
//         * ==========================================================
//         */
//
//        if ($buildingCount < 100) {
//
//            foreach (Building::get() as $building) {
//
//                $price = $building->price;
//
//                /*
//                 * Не можем купить.
//                 */
//                if ($money < $price) {
//                    continue;
//                }
//
//                /*
//                 * ==================================================
//                 * Employees
//                 * ==================================================
//                 */
//
//                $employees = $building->employees;
//
//                if ($employees > $freeResidents) {
//                    continue;
//                }
//
//                /*
//                 * ==================================================
//                 * New happiness / attractiveness
//                 * ==================================================
//                 */
//
//                $newHappiness =
//                    $happiness + $building->happiness;
//
//                $newAttractiveness =
//                    $attractiveness + $building->attractiveness;
//
//                /*
//                 * Не позволяем показателям стать <= 0.
//                 */
//                if ($newHappiness <= 0) {
//                    continue;
//                }
//
//                if ($newAttractiveness <= 0) {
//                    continue;
//                }
//
//                /*
//                 * ==================================================
//                 * New free residents
//                 * ==================================================
//                 *
//                 * Living добавляет capacity.
//                 * Остальные здания сразу забирают employees.
//                 */
//
//                $newFreeResidents = $freeResidents - $employees;
//
//                /*
//                 * ==================================================
//                 * Income
//                 * ==================================================
//                 */
//
//                $income = $building->getIncome(1);
//
//                /*
//                 * Сколько денег здание принесёт
//                 * до конца текущего периода.
//                 */
//                $periodIncome =
//                    $income * $remainingTime;
//
//                /*
//                 * ==================================================
//                 * Base financial result
//                 * ==================================================
//                 */
//
//                $profit =
//                    $periodIncome - $price;
//
//                /*
//                 * ==================================================
//                 * Score depending on current need
//                 * ==================================================
//                 */
//
//                $score = $profit;
//
//                /*
//                 * ------------------------------
//                 * Need: income
//                 * ------------------------------
//                 */
//
//                if ($need === 'income') {
//                    $score = $profit + ($income * 100);
//                } /*
//             * ------------------------------
//             * Need: residents
//             * ------------------------------
//             */
//
//                elseif ($need === 'residents') {
//
//                    /*
//                     * Living buildings are what we want.
//                     */
//                    if ($building->type->isLiving()) {
//
//                        $capacity = $building->residents_capacity;
//
//                        /*
//                         * Чем больше capacity за ту же цену,
//                         * тем лучше.
//                         */
//                        $score = ($capacity * 1000) - $price;
//
//                        /*
//                         * При этом не забываем про прибыль.
//                         */
//                        $score += $profit * 0.1;
//
//                    } else {
//
//                        /*
//                         * Остальные здания сейчас не решают
//                         * проблему жителей.
//                         */
//                        continue;
//                    }
//                } /*
//             * ------------------------------
//             * Need: happiness
//             * ------------------------------
//             */
//
//                elseif ($need === 'happiness') {
//
//                    /*
//                     * Service обычно главный источник happiness.
//                     */
//                    $happinessGain = $building->happiness;
//
//                    if ($happinessGain <= 0) {
//                        continue;
//                    }
//
//                    $score =
//                        ($happinessGain * 1000)
//                        - $price;
//
//                    /*
//                     * Учитываем attractiveness.
//                     */
//                    $score += $building->attractiveness * 100;
//
//                    /*
//                     * И немного учитываем прибыль.
//                     */
//                    $score += $profit * 0.1;
//                } /*
//             * ------------------------------
//             * Need: attractiveness
//             * ------------------------------
//             */
//
//                elseif ($need === 'attractiveness') {
//
//                    $attractivenessGain = $building->attractiveness;
//
//                    if ($attractivenessGain <= 0) {
//                        continue;
//                    }
//
//                    $score =
//                        ($attractivenessGain * 1000)
//                        - $price;
//
//                    /*
//                     * Учитываем happiness.
//                     */
//                    $score +=
//                        $building->happiness * 100;
//
//                    /*
//                     * И прибыль.
//                     */
//                    $score +=
//                        $profit * 0.1;
//                }
//
//                /*
//                 * ==================================================
//                 * Safety penalty
//                 * ==================================================
//                 */
//
//                if ($newHappiness < $minimumHappiness) {
//                    $score -= 100000;
//                }
//
//                if ($newAttractiveness < $minimumAttractiveness) {
//                    $score -= 100000;
//                }
//
//                /*
//                 * ==================================================
//                 * Compare
//                 * ==================================================
//                 */
//
//                if ($score > $bestScore) {
//
//                    $bestScore = $score;
//
//                    $bestAction = [
//                        'action'   => 'build',
//                        'building' => $building,
//                        'level'    => 1,
//
//                        'cost' => $price,
//
//                        'income'        => $income,
//                        'period_income' => $periodIncome,
//                        'profit'        => $profit,
//
//                        'employees'          => $employees,
//                        'residents_capacity' =>
//                            $building->residents_capacity,
//
//                        'happiness' =>
//                            $building->happiness,
//
//                        'attractiveness' =>
//                            $building->attractiveness,
//
//                        'new_happiness' =>
//                            $newHappiness,
//
//                        'new_attractiveness' =>
//                            $newAttractiveness,
//
//                        'new_free_residents' =>
//                            $newFreeResidents,
//
//                        'score' => $score,
//                    ];
//                }
//            }
//        }
//
//        /*
//         * ==========================================================
//         * 2. UPGRADE EXISTING BUILDINGS
//         * ==========================================================
//         */
//
//        foreach ($this->gameBuildings()->with('building')->get() as $gameBuilding) {
//
//            $building = $gameBuilding->building;
//
//            $level = $gameBuilding->level;
//
//            /*
//             * ==================================================
//             * Upgrade price
//             * ==================================================
//             */
//
//            $upgradePrice = $building->getUpgradePrice($level);
//
//            /*
//             * Не хватает денег.
//             */
//            if ($money < $upgradePrice) {
//                continue;
//            }
//
//            // TODO: need people
//            /*
//             * ==================================================
//             * Upgrade time
//             * ==================================================
//             */
//
//            $upgradeTime = $building->getUpgradeTime($level);
//
//            /*
//             * ==================================================
//             * Income difference
//             * ==================================================
//             */
//
//            $oldIncome = $building->getIncome($level);
//
//            $newIncome = $building->getIncome($level + 1);
//
//            $additionalIncome = $newIncome - $oldIncome;
//
//            /*
//             * ==================================================
//             * Income until end of period
//             * ==================================================
//             */
//
//            $incomeTime =
//                max(
//                    0,
//                    $remainingTime - $upgradeTime
//                );
//
//            $additionalPeriodIncome =
//                $additionalIncome * $incomeTime;
//
//            /*
//             * ==================================================
//             * Profit
//             * ==================================================
//             */
//
//            $profit =
//                $additionalPeriodIncome - $upgradePrice;
//
//            /*
//             * ==================================================
//             * Building effects
//             * ==================================================
//             *
//             * Assuming happiness / attractiveness scale
//             * with building level.
//             */
//
//            $oldHappiness =
//                $building->getHappiness($level);
//
//            $newHappinessValue =
//                $building->getHappiness($level + 1);
//
//            $deltaHappiness =
//                $newHappinessValue - $oldHappiness;
//
//            $oldAttractiveness =
//                $building->getAttractiveness($level);
//
//            $newAttractivenessValue =
//                $building->getAttractiveness($level + 1);
//
//            $deltaAttractiveness =
//                $newAttractivenessValue - $oldAttractiveness;
//
//            /*
//             * ==================================================
//             * Employee difference
//             * ==================================================
//             */
//
//            $oldEmployees =
//                $building->getEmployees($level);
//
//            $newEmployees =
//                $building->getEmployees($level + 1);
//
//            $additionalEmployees =
//                $newEmployees - $oldEmployees;
//
//            /*
//             * Upgrade may require additional free residents.
//             */
//            if ($additionalEmployees > $freeResidents) {
//                continue;
//            }
//
//            /*
//             * ==================================================
//             * New indicators
//             * ==================================================
//             */
//
//            $resultHappiness =
//                $happiness + $deltaHappiness;
//
//            $resultAttractiveness =
//                $attractiveness + $deltaAttractiveness;
//
//            if ($resultHappiness <= 0) {
//                continue;
//            }
//
//            if ($resultAttractiveness <= 0) {
//                continue;
//            }
//
//            /*
//             * ==================================================
//             * Score
//             * ==================================================
//             */
//
//            $score = $profit;
//
//            /*
//             * ------------------------------
//             * Income
//             * ------------------------------
//             */
//
//            if ($need === 'income') {
//
//                $score =
//                    $profit
//                    + ($additionalIncome * 100);
//            } /*
//         * ------------------------------
//         * Residents
//         * ------------------------------
//         *
//         * Upgrade Living increases capacity.
//         */
//
//            elseif ($need === 'residents') {
//
//                if ($building->type->isLiving()) {
//                    continue;
//                }
//
//                $oldCapacity =
//                    $building->residents_capacity * $level;
//
//                $newCapacity =
//                    $building->residents_capacity * ($level + 1);
//
//                $additionalCapacity =
//                    $newCapacity - $oldCapacity;
//
//                if ($additionalCapacity <= 0) {
//                    continue;
//                }
//
//                $score =
//                    ($additionalCapacity * 1000)
//                    - $upgradePrice;
//
//                $score +=
//                    $profit * 0.1;
//            } /*
//         * ------------------------------
//         * Happiness
//         * ------------------------------
//         */
//
//            elseif ($need === 'happiness') {
//
//                if ($deltaHappiness <= 0) {
//                    continue;
//                }
//
//                $score =
//                    ($deltaHappiness * 1000)
//                    - $upgradePrice;
//
//                $score +=
//                    $deltaAttractiveness * 100;
//
//                $score +=
//                    $profit * 0.1;
//            } /*
//         * ------------------------------
//         * Attractiveness
//         * ------------------------------
//         */
//
//            elseif ($need === 'attractiveness') {
//
//                if ($deltaAttractiveness <= 0) {
//                    continue;
//                }
//
//                $score =
//                    ($deltaAttractiveness * 1000)
//                    - $upgradePrice;
//
//                $score +=
//                    $deltaHappiness * 100;
//
//                $score +=
//                    $profit * 0.1;
//            }
//
//            /*
//             * ==================================================
//             * Safety
//             * ==================================================
//             */
//
//            if ($resultHappiness < $minimumHappiness) {
//                $score -= 100000;
//            }
//
//            if ($resultAttractiveness < $minimumAttractiveness) {
//                $score -= 100000;
//            }
//
//            /*
//             * ==================================================
//             * Compare
//             * ==================================================
//             */
//
//            if ($score > $bestScore) {
//
//                $bestScore = $score;
//
//                $bestAction = [
//                    'action' => 'upgrade',
//
//                    'game_building' =>
//                        $gameBuilding,
//
//                    'building' =>
//                        $building,
//
//                    'level' =>
//                        $level,
//
//                    'new_level' =>
//                        $level + 1,
//
//                    'cost' =>
//                        $upgradePrice,
//
//                    'time' =>
//                        $upgradeTime,
//
//                    'income' =>
//                        $additionalIncome,
//
//                    'period_income' =>
//                        $additionalPeriodIncome,
//
//                    'profit' =>
//                        $profit,
//
//                    'employees' =>
//                        $additionalEmployees,
//
//                    'happiness' =>
//                        $deltaHappiness,
//
//                    'attractiveness' =>
//                        $deltaAttractiveness,
//
//                    'new_happiness' =>
//                        $resultHappiness,
//
//                    'new_attractiveness' =>
//                        $resultAttractiveness,
//
//                    'score' =>
//                        $score,
//                ];
//            }
//        }
//
//        return $bestAction;
//    }


//    private function getCurrentNeed(): array
//    {
//        $happiness = $this->getHappiness();
//        $attractiveness = $this->getAttractiveness();
//        $freeResidents = $this->getUnemployed();
//
//        if ($happiness <= 10) {
//            return [
//                'need'     => 'happiness',
//                'priority' => 100,
//            ];
//        }
//
//        if ($attractiveness <= 10) {
//            return [
//                'need'     => 'attractiveness',
//                'priority' => 100,
//            ];
//        }
//
//
//        if ($freeResidents <= 0) {
//            return [
//                'need'     => 'residents',
//                'priority' => 90,
//            ];
//        }
//
//        if ($freeResidents <= 30) { // TODO: calc how many people we need
//            return [
//                'need'     => 'residents',
//                'priority' => 70,
//            ];
//        }
//
//        return [
//            'need'     => 'income',
//            'priority' => 50,
//        ];
//    }

//    public function getBestBuildingAction()
//    {
//        $need = $this->getCurrentNeed();
//
//        switch ($need['need']) {
//
//            case 'happiness':
//                return $this->findBestBuildingAction('happiness');
//
//            case 'attractiveness':
//                return $this->findBestBuildingAction('attractiveness');
//
//            case 'residents':
//                return $this->findBestBuildingAction('residents');
//
//            case 'income':
//                return $this->findBestBuildingAction('income');
//        }
//    }


    /**
     * Возвращает следующее лучшее действие для сессии.
     *
     * Возможные результаты:
     * [
     *   'action' => 'wait_train',
     *   'reason' => '...'
     * ]
     *
     * [
     *   'action' => 'wait_profit',
     *   'reason' => '...',
     *   'expected_income' => float
     * ]
     *
     * [
     *   'action' => 'build',
     *   'building' => Building,
     *   'score' => float,
     *   ...
     * ]
     *
     * [
     *   'action' => 'upgrade',
     *   'game_building' => GameBuilding,
     *   'score' => float,
     *   ...
     * ]
     */
    public function getNextAction(): ?array
    {
//        $money            = $this->money;
//        $happiness        = $this->getHappiness();
//        $attractiveness   = $this->getAttractiveness();
//        $totalIncome      = $this->getTotalIncome();
//        $unemployed     = $this->getUnemployed();


        // 1. Next type building we need update/create
        $need = $this->detectNeed();
        // 2. Calc how money people we need
        $building = $this->findBestBuildingCandidate($need);
        if ($building) {
            $needPeople = $this->getAmountPeopleToBuildOrUpgrade($building);
            // If we have unemployed

            if ($this->getUnemployed() >= $needPeople) {
                return $building;
            }
            // If we have capacity and we need wait train
            if ($this->getPopulationCapacity() - $this->getPopulation() >= $needPeople) {
                return [
                    'action' => 'wait_train',
                ];
            }
            // Any way we need living building
            $livingBuilding = $this->findBestBuildingCandidate('residents');
            if ($livingBuilding) {
                return $livingBuilding;
            }
        }
        return null;
    }

    public function getAmountPeopleToBuildOrUpgrade(array $building): int
    {
        if ($building['action'] == 'build') {
            return $this->getAmountPeopleToBuild($building);
        }
        return $this->getAmountPeopleToUpgrade($building);
    }

    public function getAmountPeopleToBuild(array $building): int
    {
        return $building['building']->employees;
    }

    public function getAmountPeopleToUpgrade(array $building): int
    {
        $gameBuilding = $building['game_building'];
        return $gameBuilding->getEmployeeRequiredToNextLevel();
    }

    private function detectNeed(): string
    {
        // If balance negative only income
        if ($this->getTotalIncome() <= 0) {
            return 'income';
        }
        // If unhappy
        if ($this->getHappiness() <= 18) {
            return 'happiness';
        }

        // If bad attractiveness
        if ($this->getAttractiveness() <= 18) {
            return 'attractiveness';
        }
        // Default income
        return 'income';
    }

    /**
     * Находит лучшее здание под текущую нужду.
     * Пока НЕ проверяет жителей и вместимость.
     */
    public function findBestBuildingCandidate(string $need): ?array
    {
        $money = $this->money;
        $happiness = $this->getHappiness();
        $attractiveness = $this->getAttractiveness();
        $totalIncome = $this->getTotalIncome();

        $best = null;
        $bestScore = -INF;

        // ==========================================
        // 1. Новые здания
        // ==========================================
        if ($this->gameBuildings()->count() < 100) {
            $newBuildings = Building::query();
//            if($need == 'living') {
//                $newBuildings->where('type', BuildingType::Living->value);
//            } else {
//                $newBuildings->where('type', '!=', BuildingType::Living->value);
//            }

            foreach ($newBuildings->get() as $building) {
                $price = $building->price;

                if ($price > ($money * 0.75)) {  // TODO: calc building and living price
                    continue;
                }

                $deltaH = $building->getHappiness(1);
                $deltaA = $building->getAttractiveness(1);
                $income = $building->getIncome(1);
                $capacity = $building->getResidentsCapacity(1);
                $employees = $building->getEmployees(1);
                $isLiving = $building->type->isLiving();

                $score = $this->calculateBuildingScore(
                    need: $need,
                    cost: $price,
                    income: $income,
                    deltaH: $deltaH,
                    deltaA: $deltaA,
                    capacity: $capacity,
                    isLiving: $isLiving,
                    isNew: true,
                    currentHappiness: $happiness,
                    currentAttractiveness: $attractiveness,
                    currentIncome: $totalIncome
                );


                if ($score > $bestScore) {
                    $bestScore = $score;
                    $best = [
                        'action'             => 'build',
                        'building'           => $building,
                        'cost'               => $price,
                        'income'             => $income,
                        'employees'          => $employees,
                        'residents_capacity' => $capacity,
                        'happiness'          => $deltaH,
                        'attractiveness'     => $deltaA,
                        'score'              => $score,
                    ];
                }
            }
        }


        // ==========================================
        // 2. Улучшения существующих
        // ==========================================
        foreach ($this->gameBuildings()->with('building')->get() as $gameBuilding) {
//            if($need == 'living' && !$gameBuilding->type->isLiving()) {
//                continue;
//            }
//            if($need != 'living' && $gameBuilding->type->isLiving()) {
//                continue;
//            }

            if ($gameBuilding->upgrade_finish_at !== null) {
                continue;
            }

            $building = $gameBuilding->building;
            $level = $gameBuilding->level;
            $nextLevel = $level + 1;

            $price = $building->getUpgradePrice($nextLevel);

            if ($price > ($money * 0.75)) { // TODO: calc building and living price
                continue;
            }

            $deltaIncome = $building->getIncome($nextLevel) - $building->getIncome($level);
            $deltaH = $building->getHappiness($nextLevel) - $building->getHappiness($level);
            $deltaA = $building->getAttractiveness($nextLevel) - $building->getAttractiveness($level);
            $deltaCapacity = $building->getResidentsCapacity($nextLevel) - $building->getResidentsCapacity($level);
            $deltaEmployees = $building->getEmployees($nextLevel) - $building->getEmployees($level);
            $isLiving = $building->type->isLiving();

            $score = $this->calculateBuildingScore(
                need: $need,
                cost: $price,
                income: $deltaIncome,
                deltaH: $deltaH,
                deltaA: $deltaA,
                capacity: $deltaCapacity,
                isLiving: $isLiving,
                isNew: false,
                currentHappiness: $happiness,
                currentAttractiveness: $attractiveness,
                currentIncome: $totalIncome
            );

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = [
                    'action'             => 'upgrade',
                    'game_building'      => $gameBuilding,
                    'building'           => $building,
                    'level'              => $level,
                    'new_level'          => $nextLevel,
                    'cost'               => $price,
                    'income'             => $deltaIncome,
                    'employees'          => $deltaEmployees,
                    'residents_capacity' => $deltaCapacity,
                    'happiness'          => $deltaH,
                    'attractiveness'     => $deltaA,
                    'score'              => $score,
                ];
            }
        }

        return $best;
    }

    /**
     * Считает score здания в зависимости от текущей нужды.
     */
    private function calculateBuildingScore(
        string $need,
        float  $cost,
        float  $income,
        float  $deltaH,
        float  $deltaA,
        int    $capacity,
        bool   $isLiving,
        bool   $isNew,
        float  $currentHappiness,
        float  $currentAttractiveness,
        float  $currentIncome
    ): float
    {
        // Защита от деления на ноль и слишком маленьких значений
        $cost = max(1.0, $cost);

        $score = 0.0;

        switch ($need) {
            // ==========================================
            // Нужны деньги
            // ==========================================
            case 'income':
                // Основной упор на прирост дохода
                $score = ($income * 180) - ($cost * 0.25);

                // Небольшой бонус за то, что не портит показатели
                $score += $deltaH * 8;
                $score += $deltaA * 8;

                // Living почти не интересны, когда нужны деньги
                if ($isLiving) {
                    $score *= 0.25;
                }
                break;

            // ==========================================
            // Нужны жители (места)
            // ==========================================
            case 'residents':
                if (!$isLiving || $capacity <= 0) {
                    return -INF; // нам нужны только Living
                }

                // Чем больше мест за деньги — тем лучше
                $score = ($capacity * 2200) - $cost;

                // Небольшой бонус за доход (чтобы не брать совсем бесполезные дома)
                $score += $income * 15;

                // Штраф, если дом сильно портит счастье/привлекательность
                if ($deltaH < 0) {
                    $score += $deltaH * 40;
                }
                if ($deltaA < 0) {
                    $score += $deltaA * 30;
                }
                break;

            // ==========================================
            // Нужно счастье
            // ==========================================
            case 'happiness':
                if ($deltaH <= 0) {
                    return -INF;
                }

                $score = ($deltaH * 1600) - $cost;

                // Учитываем привлекательность
                $score += $deltaA * 120;

                // Учитываем доход
                $score += $income * 10;

                // Если счастье уже очень низкое — сильнее ценим прирост
                if ($currentHappiness < 30) {
                    $score *= 1.35;
                }
                break;

            // ==========================================
            // Нужна привлекательность
            // ==========================================
            case 'attractiveness':
                if ($deltaA <= 0) {
                    return -INF;
                }

                $score = ($deltaA * 1600) - $cost;

                // Учитываем счастье
                $score += $deltaH * 120;

                // Учитываем доход
                $score += $income * 10;

                if ($currentAttractiveness < 30) {
                    $score *= 1.35;
                }
                break;

            default:
                return -INF;
        }

        // Небольшой бонус новым зданиям (чтобы не зацикливаться только на апгрейдах)
        if ($isNew) {
            $score *= 1.06;
        }

        // Лёгкий штраф за очень дорогие действия, когда денег мало
        if ($cost > 400 && $currentIncome < 10) {
            $score *= 0.85;
        }

        return $score;
    }

    /**
     * Оценка действия в зависимости от нужды
     */
    private function scoreAction(
        string $need,
        float  $cost,
        float  $income,
        float  $deltaH,
        float  $deltaA,
        int    $capacity,
        bool   $isLiving,
        float  $moneyLeft
    ): float
    {
        $score = 0;

        switch ($need) {
            case 'income':
                $score = ($income * 140) - ($cost * 0.18);
                break;

            case 'residents':
                if (!$isLiving || $capacity <= 0) {
                    return -INF;
                }

                $score = ($capacity * 1700) - $cost;
                $score += $income * 10;

                // Важно: не оставлять город без денег
                if ($moneyLeft < 130) {
                    $score -= 12000;
                } elseif ($moneyLeft < 220) {
                    $score -= 5000;
                }
                break;

            case 'happiness':
                if ($deltaH <= 0) return -INF;
                $score = ($deltaH * 1500) - $cost;
                $score += $deltaA * 90;
                $score += $income * 5;
                break;

            case 'attractiveness':
                if ($deltaA <= 0) return -INF;
                $score = ($deltaA * 1500) - $cost;
                $score += $deltaH * 90;
                $score += $income * 5;
                break;
        }

        return $score;
    }


}
