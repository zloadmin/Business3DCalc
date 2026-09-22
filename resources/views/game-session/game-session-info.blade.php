<div class="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm p-4 mb-4">
    <h2>Show Game Session</h2>
    <p>Money: <b>{{ $gameSession->money }}</b></p>
    <p>Population: <b>{{ $gameSession->population }}</b></p>
    <p>Population capacity: <b>{{ $gameSession->getPopulationCapacity() }}</b></p>
    <p>Employees: <b>{{ $gameSession->getEmployees() }}</b></p>
    <p>Unemployed: <b>{{ $gameSession->getUnemployed() }}</b></p>
    <p>Game time: <b>{{ $gameSession->game_time }}</b></p>
    <p>Game time text: <b>{{ \Carbon\CarbonInterval::seconds($gameSession->game_time)->cascade()->forHumans() }}</b></p>
    <p>Last tick at: <b>{{ $gameSession->last_tick_at }}</b></p>
    <p>Time speed: <b>{{ $gameSession->time_speed }}</b></p>
    <p>Happiness: <b>{{ $gameSession->getHappiness() }}</b></p>
    <p>Attractiveness: <b>{{ $gameSession->getAttractiveness() }}</b></p>
</div>
