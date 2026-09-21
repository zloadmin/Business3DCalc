@extends('layouts.app')

@section('title', __('Upgrade schedule'))

@section('content')

    <div class="rounded-lg border border-slate-200 bg-white shadow-sm p-4 mb-4">
        <h1>Building {{ $building->name }}</h1>
        <p>Price: <b>{{ $building->price }}</b></p>
        <p>Happiness: <b>{{ $building->happiness }}</b></p>
        <p>Attractiveness: <b>{{ $building->attractiveness }}</b></p>
        <p>Employees: <b>{{ $building->employees }}</b></p>
        <p>Residents capacity: <b>{{ $building->residents_capacity }}</b></p>
    </div>
    <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-600">
                <tr>
                    <th scope="col" class="px-3 py-3 font-medium">{{ __('Level') }}</th>
                    <th scope="col" class="px-3 py-3 text-left font-medium">{{ __('Upgrade price') }}</th>
                    <th scope="col" class="px-3 py-3 text-left font-medium">{{ __('Total spent') }}</th>
                    <th scope="col" class="px-3 py-3 text-left font-medium">{{ __('Income') }}</th>
                    <th scope="col" class="px-3 py-3 text-right font-medium">{{ __('Income before next level') }}</th>
                    <th scope="col" class="px-3 py-3 text-left font-medium">{{ __('Total Income') }}</th>
                    <th scope="col" class="px-3 py-3 text-left font-medium">{{ __('Upgrade time') }}</th>
                    <th scope="col" class="px-3 py-3 text-left font-medium">{{ __('Total time') }}</th>
                    <th scope="col" class="px-3 py-3 text-left font-medium">{{ __('Residents Capacity') }}</th>
                    <th scope="col" class="px-3 py-3 text-left font-medium">{{ __('Happiness') }}</th>
                    <th scope="col" class="px-3 py-3 text-left font-medium">{{ __('Attractiveness') }}</th>
                    <th scope="col" class="px-3 py-3 text-left font-medium">{{ __('Employees') }}</th>

                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @for($level = 1; $level <= 100; $level++)
                    <tr class="bg-slate-50 font-medium">
                        <td class="px-3 py-2 tabular-nums">{{ $level }}</td>
                        <td class="px-3 py-2 tabular-nums">{{ $building->getUpgradePrice($level) }}</td>
                        <td class="px-3 py-2 tabular-nums">{{ $building->getTotalSpend($level) }}</td>
                        <td class="px-3 py-2 tabular-nums">{{ $building->getIncome($level) }}</td>
                        <td class="px-3 py-2 tabular-nums">{{ $building->getIncomeBeforeNextLevel($level) }}</td>
                        <td class="px-3 py-2 tabular-nums">{{ $building->getTotalIncome($level) }}</td>
                        <td class="px-3 py-2 tabular-nums">{{ $building->getUpgradeTimeText($level) }}</td>
                        <td class="px-3 py-2 tabular-nums">{{ $building->getTotalTimeText($level) }}</td>
                        <td class="px-3 py-2 tabular-nums">{{ $building->getResidentsCapacity($level) }}</td>
                        <td class="px-3 py-2 tabular-nums">{{ $building->getHappiness($level) }}</td>
                        <td class="px-3 py-2 tabular-nums">{{ $building->getAttractiveness($level) }}</td>
                        <td class="px-3 py-2 tabular-nums">{{ $building->getEmployees($level) }}</td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>

@endsection
