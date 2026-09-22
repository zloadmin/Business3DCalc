<?php
declare(strict_types=1);


namespace App\Services;


use App\Models\Building;
use App\Models\GameBuilding;
use App\Models\GameSession;
use App\Models\TrainItem;

class GameTickService
{

    const int TRAIN_PERIOD = 120;

    public function tick(GameSession $session): void
    {
        $now = now();

        if (!$session->last_tick_at) {
            $session->last_tick_at = $now;
            $session->save();
            return;
        }

        $realSeconds = $session->getRealSeconds();

        if ($realSeconds <= 0) {
            return;
        }

        $gameSeconds = (int)floor(
            $realSeconds * $session->time_speed
        );

        if ($gameSeconds <= 0) {
            return;
        }

        $oldGameTime = $session->game_time;
        $newGameTime = $oldGameTime + $gameSeconds;


        $this->processBuildings(
            $session,
            $oldGameTime,
            $newGameTime
        );


        if ($session->game_time % self::TRAIN_PERIOD == 0) {
            $train = $this->createTrainItem($session);
            var_dump($train);
        }

        $session->game_time = $newGameTime;
        $session->last_tick_at = $now;
        $session->save();
    }

    private function processBuildings(
        GameSession $session,
        int         $from,
        int         $to
    ): void
    {
        $buildings = $session->gameBuildings()
            ->with('building')
            ->get();

        foreach ($buildings as $gameBuilding) {

            if (
                $gameBuilding->next_income_at !== null &&
                $gameBuilding->next_income_at <= $to
            ) {
                $this->processIncome(
                    $session,
                    $gameBuilding,
                    $from,
                    $to
                );
            }

            if (
                $gameBuilding->upgrade_finish_at !== null &&
                $gameBuilding->upgrade_finish_at <= $to
            ) {
                $this->finishUpgrade(
                    $gameBuilding
                );
            }
        }
    }

    private function processIncome(
        GameSession  $session,
        GameBuilding $gameBuilding,
        int          $from,
        int          $to
    ): void
    {
        $building = $gameBuilding->building;

        while (
            $gameBuilding->next_income_at !== null &&
            $gameBuilding->next_income_at <= $to
        ) {
            $income = $building->getIncome($gameBuilding->level);
            $session->money += $income;
            $gameBuilding->next_income_at += Building::INCOME_PERIOD;
            $gameBuilding->full_income += $income;
        }

        $gameBuilding->save();
    }

    private function finishUpgrade(GameBuilding $gameBuilding)
    {
        $gameBuilding->upgrade_finish_at = null;
        $gameBuilding->level = $gameBuilding->level + 1;
        $gameBuilding->save();
    }

    public function createTrainItem(GameSession $gameSession): TrainItem
    {
        $population = $gameSession->getPopulation();
        $populationCapacity = $gameSession->getPopulationCapacity();
        $employees = $gameSession->getEmployees();
        $unemployed = $gameSession->getUnemployed();
        $happiness = $gameSession->getHappiness();
        $attractiveness = $gameSession->getAttractiveness();



        // Сначала определяем потенциальный отток.
        $leave = $this->calculateLeave(
            $population,
            $populationCapacity,
            $employees,
            $happiness,
            $attractiveness
        );

        // После отъезда освободятся дополнительные места.
        $freeCapacity = max(
            0,
            $populationCapacity - $population + $leave
        );



        // Приток не может превышать свободную вместимость.
        $coming = $this->calculateComing(
            $population,
            $populationCapacity,
            $employees,
            $happiness,
            $attractiveness,
            $freeCapacity
        );

        $result = $coming - $leave;

        return $gameSession->trainItems()->create([
            'money' => $gameSession->money,

            'population_capacity' => $populationCapacity,
            'population'          => $population,
            'employees'           => $employees,
            'unemployed'          => $unemployed,

            'happiness'      => $happiness,
            'attractiveness' => $attractiveness,

            'coming' => $coming,
            'leave'  => $leave,
            'result' => $result,

            'game_time' => $gameSession->game_time,
        ]);
    }

    private function calculateComing(
        int   $population,
        int   $populationCapacity,
        int   $employees,
        float $happiness,
        float $attractiveness,
        int   $freeCapacity
    ): int {

        if ($freeCapacity <= 0) {
            return 0;
        }

        $attractivenessFactor = max(0, $attractiveness / 100);
        $happinessFactor = max(0, $happiness / 100);

        $factor =
            $attractivenessFactor * 0.6 +
            $happinessFactor * 0.4;

        // 20% свободной вместимости является
        // базовым потенциальным притоком.
        $baseComing = $freeCapacity * 0.20;

        $potentialComing = (int) round(
            $baseComing * (0.5 + $factor)
        );

        return min(
            $freeCapacity,
            max(0, $potentialComing)
        );
    }

    private function calculateLeave(
        int   $population,
        int   $populationCapacity,
        int   $employees,
        float $happiness,
        float $attractiveness
    ): int {
        if ($population <= 0) {
            return 0;
        }

        // Уезжать могут только безработные.
        $unemployed = max(
            0,
            $population - $employees
        );

        if ($unemployed === 0) {
            return 0;
        }

        $unemploymentRate = $unemployed / $population;

        $unhappiness = max(
            0,
            (100 - $happiness) / 100
        );

        $unattractiveness = max(
            0,
            (100 - $attractiveness) / 100
        );

        $factor =
            $unemploymentRate * 0.5 +
            $unhappiness * 0.3 +
            $unattractiveness * 0.2;

        $baseLeave = $population * 0.02 * $factor;

        // Уехать может не больше безработных.
        return min(
            $unemployed,
            max(0, (int) round($baseLeave))
        );
    }
}
