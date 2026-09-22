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


        $gameTickService = new GameTickService();
        $index = 1;
        while (true) {
            $gameSession = GameSession::orderByDesc('id')->first();
            $bestStep = $gameSession->getNextAction();
            $this->info(json_encode($bestStep));
            sleep(1);
//            if (!$bestStep) {
//                sleep(1);
//                $gameTickService->tick($gameSession);
//                continue;
//            }

//            switch ($bestStep['action']) {
//                case 'build':
//                    $building = $bestStep['building'];
//                    $this->info($index . ' Build new building ' . $building->name . ' ' . json_encode($bestStep));
//                    $gameSession->buyBuilding($building);
//                    $gameTickService->tick($gameSession);
//                    $index++;
//                    break;
//                case 'upgrade':
//                    $gameBuilding = $bestStep['game_building'];
//                    $building = $bestStep['building'];
//                    $this->info($index . ' Upgrade building ' . $building->name . ' ' . json_encode($bestStep));
//                    $gameSession->upgradeBuilding($gameBuilding);
//                    $gameTickService->tick($gameSession);
//                    $index++;
//                    break;
//            }

        }
    }
}
