<?php

namespace App\Services;

use App\Models\Building;

class BuildingUpgradeCalculator
{
    /**
     * Building a new copy is the reference strategy we must stay worse than.
     *
     * Upgrades cost a multiple of that price per unit of income gained, so
     * price parity alone would already make upgrading the weaker option.
     */
    private const UPDATE_PRICE_PENALTY = 2.5;

    /**
     * Gold cost of one upgrade, as a share of the upgrade's own value.
     */
    private const UPDATE_PRICE_SHARE = 0.60;

    /**
     * Hours to complete level 1 -> 2, and the exponent applied per level.
     */
    private const UPDATE_BASE_HOURS = 2.0;

    private const UPDATE_TIME_GROWTH = 1.045;

    /**
     * Share of the upgrade price that pays for stats other than income.
     */
    private const STAT_SHARE = 0.55;

    /**
     * Scaling constants, chosen so the table stays readable at level 100.
     */
    private const TIME_CAP = 24 * 365;

    private const PRICE_CAP = 100_000_000;

    private const HOURS_PER_PERIOD = 24.0;

    /**
     * Build the full upgrade schedule for one building.
     *
     * @return list<array{level: int, upgrade_price: int, cumulative_price: int, upgrade_hours: float, cumulative_hours: float, income: float, income_gain: float, marginal_gold_per_income: float|null, cumulative_gold_per_income: float|null, build_new_gold_per_income: float, upgrade_efficiency: float|null, employees: int, residents: int, happiness: int, attractiveness: int}>
     */
    public function schedule(Building $building, int $levels = 100): array
    {
        $baseIncome = (float) $building->income;
        $baseEmployees = $building->employees_count;
        $baseResidents = $building->residents_count;
        $baseHappiness = $building->happiness;
        $baseAttractiveness = $building->attractiveness;

        // Reference: what a brand new copy of the same building costs per income.
        $buildNewGoldPerIncome = $baseIncome > 0
            ? $building->price / $baseIncome
            : 0.0;

        $rows = [];
        $cumulativePrice = 0;
        $cumulativeHours = 0.0;

        for ($level = 1; $level <= $levels; $level++) {
            $income = $this->incomeAtLevel($building, $level);

            // Level 1 is the base building: nothing was paid or waited for.
            if ($level === 1) {
                $rows[] = [
                    'level' => 1,
                    'upgrade_price' => 0,
                    'cumulative_price' => 0,
                    'upgrade_hours' => 0.0,
                    'cumulative_hours' => 0.0,
                    'income' => $income,
                    'income_gain' => 0.0,
                    'marginal_gold_per_income' => null,
                    'cumulative_gold_per_income' => null,
                    'build_new_gold_per_income' => $buildNewGoldPerIncome,
                    'upgrade_efficiency' => null,
                    'employees' => $baseEmployees,
                    'residents' => $baseResidents,
                    'happiness' => $baseHappiness,
                    'attractiveness' => $baseAttractiveness,
                ];

                continue;
            }

            $incomeGain = $income - $this->incomeAtLevel($building, $level - 1);

            $upgradePrice = $this->upgradePrice($building, $level, $incomeGain);
            $upgradeHours = $this->upgradeHours($level);

            $cumulativePrice += $upgradePrice;
            $cumulativeHours += $upgradeHours;

            $cumulativeIncomeGain = $income - $baseIncome;

            $marginal = $incomeGain > 0 ? $upgradePrice / $incomeGain : null;
            $cumulative = $cumulativeIncomeGain > 0 ? $cumulativePrice / $cumulativeIncomeGain : null;

            $rows[] = [
                'level' => $level,
                'upgrade_price' => (int) round($upgradePrice),
                'cumulative_price' => (int) round($cumulativePrice),
                'upgrade_hours' => $upgradeHours,
                'cumulative_hours' => $cumulativeHours,
                'income' => $income,
                'income_gain' => $incomeGain,
                'marginal_gold_per_income' => $marginal,
                'cumulative_gold_per_income' => $cumulative,
                'build_new_gold_per_income' => $buildNewGoldPerIncome,
                'upgrade_efficiency' => $marginal !== null && $buildNewGoldPerIncome > 0
                    ? $buildNewGoldPerIncome / $marginal
                    : null,
                'employees' => $this->scaledStat($baseEmployees, $level, cap: 100_000),
                'residents' => $this->scaledStat($baseResidents, $level, cap: 1_000_000),
                'happiness' => $this->scaledStat($baseHappiness, $level, cap: 100),
                'attractiveness' => $this->scaledStat($baseAttractiveness, $level, cap: 100),
            ];
        }

        return $rows;
    }

    /**
     * Income at a level: linear on the upgrade coefficient.
     *
     * The model's incomeAtLevel() compounds it, which reaches ~3.9 trillion by
     * level 100 for Hotel 1 and makes any comparison table meaningless. Linear
     * growth keeps the schedule readable, and is what the price rule is tuned to.
     */
    public function incomeAtLevel(Building $building, int $level): float
    {
        if ($level < 1) {
            return 0.0;
        }

        $coefficient = (float) $building->upgrade_time_coefficient;

        return (float) $building->income * (1 + ($coefficient - 1) * ($level - 1));
    }

    /**
     * Gold cost of the upgrade into $level.
     */
    public function upgradePrice(Building $building, int $level, float $incomeGain): float
    {
        $baseIncome = (float) $building->income;

        $newBuildPricePerIncome = $baseIncome > 0
            ? $building->price / $baseIncome
            : 0.0;

        // Paying a new build's price for the income gained, times a penalty,
        // is what guarantees upgrades stay less profitable than building new.
        $value = $incomeGain * $newBuildPricePerIncome * self::UPDATE_PRICE_PENALTY;

        return min(self::PRICE_CAP, max(1.0, $value));
    }

    /**
     * Hours the upgrade into $level takes, growing with level.
     */
    public function upgradeHours(int $level): float
    {
        if ($level < 2) {
            return 0.0;
        }

        return min(self::TIME_CAP, self::UPDATE_BASE_HOURS * (self::UPDATE_TIME_GROWTH ** ($level - 1)));
    }

    /**
     * A stat at $level, growing with the same curve as income.
     */
    private function scaledStat(int $base, int $level, int $cap): int
    {
        return (int) round(min($cap, $base * $level));
    }

    /**
     * Hours expressed in game periods.
     */
    public function hoursToPeriods(float $hours): float
    {
        return $hours / self::HOURS_PER_PERIOD;
    }

    /**
     * Human readable duration, e.g. "3d 4h".
     */
    public function formatDuration(float $hours): string
    {
        if ($hours <= 0) {
            return '—';
        }

        if ($hours < 1) {
            return round($hours * 60).'m';
        }

        if ($hours < 24) {
            return round($hours, 1).'h';
        }

        $days = intdiv((int) $hours, 24);
        $remaining = (int) round(fmod($hours, 24));

        return $days.'d '.max(0, $remaining).'h';
    }
}
