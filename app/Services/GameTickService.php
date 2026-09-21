<?php
declare(strict_types=1);


namespace App\Services;


use App\Models\Building;
use App\Models\GameBuilding;
use App\Models\GameSession;

class GameTickService
{

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

        $gameSeconds = (int) floor(
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

        $session->game_time = $newGameTime;
        $session->last_tick_at = $now;
        $session->save();
    }

    private function processBuildings(
        GameSession $session,
        int $from,
        int $to
    ): void {
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
        GameSession $session,
        GameBuilding $gameBuilding,
        int $from,
        int $to
    ): void {
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
}
