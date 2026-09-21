@extends('layouts.app')

@section('title', __('Create Game Session'))

@section('content')
    <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm p-4 mb-4">
        <h2>Show Game Session</h2>

        <p>Money: <b>{{ $gameSession->money }}</b></p>
        <p>Population: <b>{{ $gameSession->population }}</b></p>
        <p>Game time: <b>{{ $gameSession->game_time }}</b></p>
        <p>Game time text: <b>{{ \Carbon\CarbonInterval::seconds($gameSession->game_time)->cascade()->forHumans() }}</b></p>
        <p>Last tick at: <b>{{ $gameSession->last_tick_at }}</b></p>
        <p>Time speed: <b>{{ $gameSession->time_speed }}</b></p>
        <p>Happiness: <b>{{ $gameSession->getHappiness() }}</b></p>
        <p>Attractiveness: <b>{{ $gameSession->getAttractiveness() }}</b></p>

    </div>
    <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm p-4 mb-4">
        <h2>Buy building</h2>

        <form method="POST" action="{{ route('game-session.buy-building', $gameSession) }}" class="space-y-6">
            @csrf

            <!-- Building Field -->
            <div>
                <label
                    for="building_id"
                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                    Building
                </label>

                <select
                    id="building_id"
                    name="building_id"
                    required
                    class="block w-full rounded-lg border px-4 py-2.5 text-sm
                   bg-white text-gray-900
                   border-gray-300
                   focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
                   dark:border-gray-600 dark:bg-gray-700 dark:text-white
                   {{ $errors->has('building_id') ? 'border-red-500 focus:border-red-500 focus:ring-red-500/20' : '' }}"
                >
                    <option value="">Select building</option>

                    @foreach($buildingCollection as $item)
                        <option value="{{ $item->id }}">
                            {{ $item->name }} ({{ $item->price }} $)
                        </option>
                    @endforeach
                </select>

                @if ($errors->has('building_id'))
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                        {{ $errors->first('building_id') }}
                    </p>
                @endif
            </div>

            <!-- Buy Button -->
            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-lg
               bg-blue-600 px-5 py-2.5 text-sm font-medium text-white
               transition-colors
               hover:bg-blue-700
               focus:outline-none focus:ring-4 focus:ring-blue-300
               dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-800"
            >
                Buy
            </button>
        </form>

    </div>


    <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-600">
            <tr>
                <th scope="col" class="px-4 py-3 font-medium">{{ __('#') }}</th>
                <th scope="col" class="px-4 py-3 font-medium">{{ __('Name') }}</th>
                <th scope="col" class="px-4 py-3 font-medium">{{ __('Type') }}</th>
                <th scope="col" class="px-4 py-3 text-right font-medium">{{ __('Price') }}</th>
                <th scope="col" class="px-4 py-3 text-right font-medium">{{ __('Income') }}</th>
                <th scope="col" class="px-4 py-3 text-right font-medium">{{ __('Level') }}</th>
                <th scope="col" class="px-4 py-3 text-right font-medium">{{ __('Build at') }}</th>
                <th scope="col" class="px-4 py-3 text-right font-medium">{{ __('Income with Level') }}</th>
                <th scope="col" class="px-4 py-3 text-right font-medium">{{ __('Next Income') }}</th>
                <th scope="col" class="px-4 py-3 text-right font-medium">{{ __('Upgrading') }}</th>
                <th scope="col" class="px-4 py-3 text-right font-medium">{{ __('Full Income') }}</th>
                <th scope="col" class="px-4 py-3 text-right font-medium">{{ __('Happiness') }}</th>
                <th scope="col" class="px-4 py-3 text-right font-medium">{{ __('Attractiveness') }}</th>

                <th scope="col" class="px-4 py-3 text-right font-medium">{{ __('Action') }}</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            @forelse ($gameBuildings as $gameBuilding)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 text-right tabular-nums">{{ $gameBuilding->id }}</td>
                    <td class="px-4 py-3 font-medium text-slate-900">
                        <a href="{{ route('buildings.show', $gameBuilding->building) }}" class="hover:underline">{{ $gameBuilding->building->name }}</a>
                    </td>
                    <td class="px-4 py-3">
                            <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">
                                {{ $gameBuilding->building->type->label() }}
                            </span>
                    </td>
                    <td class="px-4 py-3 text-right tabular-nums">{{ number_format($gameBuilding->building->price, 0, '.', ' ') }}</td>
                    <td class="px-4 py-3 text-right tabular-nums">{{ number_format($gameBuilding->building->getIncome(1), 0, '.', ' ') }}</td>
                    <td class="px-4 py-3 text-right tabular-nums">{{ $gameBuilding->level }}</td>
                    <td class="px-4 py-3 text-right tabular-nums">
                        {{ \Carbon\CarbonInterval::seconds($gameSession->game_time - $gameBuilding->built_at)->cascade()->forHumans() }}
                    </td>
                    <td class="px-4 py-3 text-right tabular-nums">{{ $gameBuilding->building->getIncome($gameBuilding->level) }}</td>
                    <td class="px-4 py-3 text-right tabular-nums">
                        @if ($gameBuilding->next_income_at)
                            {{ \Carbon\CarbonInterval::seconds($gameSession->game_time - $gameBuilding->next_income_at)->cascade()->forHumans() }}
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right tabular-nums">
                        @if ($gameBuilding->upgrade_finish_at)
                            {{ \Carbon\CarbonInterval::seconds($gameBuilding->upgrade_finish_at  - $gameSession->game_time)->cascade()->forHumans() }}

                        @else
                            Need:
                            {{ \Carbon\CarbonInterval::seconds($gameBuilding->building->getUpgradeTime($gameBuilding->level + 1))->cascade()->forHumans() }}
                            and
                            {{ $gameBuilding->building->getUpgradePrice($gameBuilding->level + 1) }} $
                        @endif
                    </td>


                    <td class="px-4 py-3 text-right tabular-nums">{{ $gameBuilding->full_income }}</td>

                    <td class="px-4 py-3 text-right tabular-nums">{{ $gameBuilding->building->getHappiness($gameBuilding->level) }}</td>
                    <td class="px-4 py-3 text-right tabular-nums">{{ $gameBuilding->building->getAttractiveness($gameBuilding->level) }}</td>

                    <td class="px-4 py-3 text-right tabular-nums">
                        @if (!$gameBuilding->upgrade_finish_at)
                        <form method="POST" action="{{ route('game-session.upgrade-game-building', [$gameSession, $gameBuilding]) }}">
                            @csrf
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-lg
                               bg-blue-600 px-5 py-2.5 text-sm font-medium text-white
                               transition-colors
                               hover:bg-blue-700
                               focus:outline-none focus:ring-4 focus:ring-blue-300
                               dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-800"
                            >
                                Upgrade
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="px-4 py-8 text-center text-slate-500">
                        {{ __('No buildings yet.') }}
                    </td>
                </tr>
            @endforelse
            </tbody>

        </table>
    </div>

@endsection
