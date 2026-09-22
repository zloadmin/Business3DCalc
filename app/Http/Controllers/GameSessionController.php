<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\GameBuilding;
use App\Models\GameSession;
use App\Services\GameTickService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GameSessionController extends Controller
{
    public function create()
    {
        return view('game-session.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'money'      => [
                'required',
                'numeric',
                'min:100',
                'max:100000',
            ],
            'time_speed' => [
                'required',
                'numeric',
                'min:1',
                'max:100',
            ],
        ]);
        $gameSession = GameSession::create([
            'money'      => $request->money,
            'time_speed' => $request->time_speed,
        ]);
        return redirect()->route('game-session.show', $gameSession);
    }

    public function show(GameTickService $gameTickService, GameSession $gameSession)
    {
        $buildingCollection = Building::query()->orderBy('id')->get();
        $gameBuildings = $gameSession->gameBuildings()->with('building')->get();
        $gameTickService->tick($gameSession);
        $trainItems = $gameSession->trainItems()->latest()->get();
        return view('game-session.show', compact('gameSession', 'gameBuildings', 'buildingCollection', 'trainItems'));
    }

    public function buyBuilding(GameSession $gameSession, Request $request)
    {
        $request->validate([
            'building_id' => [
                'required',
                'exists:buildings,id',
            ],
        ]);

        $building = Building::findOrFail($request->building_id);
        $gameSession->buyBuilding($building);
        return redirect()->route('game-session.show', $gameSession);
    }
    public function upgrade(GameSession $gameSession, GameBuilding $gameBuilding)
    {

        $gameSession->upgradeBuilding($gameBuilding);

        return redirect()->route('game-session.show', $gameSession);
    }

}
