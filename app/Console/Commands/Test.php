<?php

namespace App\Console\Commands;

use App\Models\Building;
use App\Models\GameBuilding;
use App\Models\GameSession;
use App\Services\GameTickService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('t')]
#[Description('Command description')]
class Test extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {

//        $gameSession = GameSession::orderByDesc('id')->first();

        $gameTickService = new GameTickService();
        while (true) {
            $gameSession = GameSession::orderByDesc('id')->first();
            $gameTickService->tick($gameSession);
            sleep(1);
        }

        return 0;

        $gameTickService = new GameTickService();
        $index = 1;
        while (true) {
            $gameSession = GameSession::orderByDesc('id')->first();
            $bestStep = $gameSession->getBestBuildingAction();
            if (!$bestStep) {
                sleep(1);
                $gameTickService->tick($gameSession);
                continue;
            }
            switch ($bestStep['action']) {
                case 'build':
                    $this->info($index . ' Build new building ' . $bestStep['type'] . ' ' . json_encode($bestStep));
                    $building = Building::findOrFail($bestStep['building_id']);
                    $gameSession->buyBuilding($building);
                    $gameTickService->tick($gameSession);
                    $index++;
                    break;
                case 'upgrade':
                    $this->info($index . ' Upgrade building ' . $bestStep['type'] . ' ' . json_encode($bestStep));
                    $gameBuilding = GameBuilding::findOrFail($bestStep['game_building_id']);
                    $gameSession->upgradeBuilding($gameBuilding);
                    $gameTickService->tick($gameSession);
                    $index++;
                    break;
            }


        }
    }
}
