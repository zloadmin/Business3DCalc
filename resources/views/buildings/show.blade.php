@extends('layouts.app')

@section('title', $building->name)

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-semibold tracking-tight">{{ $building->name }}</h1>
                <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">
                    {{ $building->type->label() }}
                </span>
            </div>
            <p class="mt-1 text-sm text-slate-600">{{ __('Building stats.') }}</p>
        </div>

        <div class="flex items-center gap-4">
            <a href="{{ route('buildings.edit', $building) }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                {{ __('Edit') }}
            </a>
            <a href="{{ route('buildings.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">
                {{ __('Back to list') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold">{{ __('Main parameters') }}</h2>

            <dl class="mt-4 divide-y divide-slate-100 text-sm">
                <div class="flex justify-between py-2">
                    <dt class="text-slate-600">{{ __('Type') }}</dt>
                    <dd class="font-medium">{{ $building->type->label() }}</dd>
                </div>
                <div class="flex justify-between py-2">
                    <dt class="text-slate-600">{{ __('Price') }}</dt>
                    <dd class="font-medium tabular-nums">{{ number_format($building->price, 0, '.', ' ') }}</dd>
                </div>
                <div class="flex justify-between py-2">
                    <dt class="text-slate-600">{{ __('Income') }}</dt>
                    <dd class="font-medium tabular-nums">{{ number_format($building->income, 0, '.', ' ') }}</dd>
                </div>
                <div class="flex justify-between py-2">
                    <dt class="text-slate-600">{{ __('Upgrade coefficient') }}</dt>
                    <dd class="font-medium tabular-nums">{{ number_format((float) $building->upgrade_coefficient, 3, '.', '') }}</dd>
                </div>
                <div class="flex justify-between py-2">
                    <dt class="text-slate-600">{{ __('Employees') }}</dt>
                    <dd class="font-medium tabular-nums">{{ number_format($building->employees_count, 0, '.', ' ') }}</dd>
                </div>
                <div class="flex justify-between py-2">
                    <dt class="text-slate-600">{{ __('Residents') }}</dt>
                    <dd class="font-medium tabular-nums">
                        @if ($building->type->providesResidents())
                            {{ number_format($building->residents_count, 0, '.', ' ') }}
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </dd>
                </div>
                <div class="flex justify-between py-2">
                    <dt class="text-slate-600">{{ __('Happiness') }}</dt>
                    <dd class="font-medium tabular-nums">{{ $building->happiness > 0 ? '+' : '' }}{{ $building->happiness }}</dd>
                </div>
                <div class="flex justify-between py-2">
                    <dt class="text-slate-600">{{ __('Attractiveness') }}</dt>
                    <dd class="font-medium tabular-nums">{{ $building->attractiveness > 0 ? '+' : '' }}{{ $building->attractiveness }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold">{{ __('Income by upgrade level') }}</h2>

            <table class="mt-4 min-w-full text-sm">
                <thead class="text-left text-xs uppercase tracking-wide text-slate-600">
                    <tr>
                        <th scope="col" class="py-2 font-medium">{{ __('Level') }}</th>
                        <th scope="col" class="py-2 text-right font-medium">{{ __('Income') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($incomeByLevel as $level => $income)
                        <tr>
                            <td class="py-2">{{ $level }}</td>
                            <td class="py-2 text-right tabular-nums">{{ number_format($income, 2, '.', ' ') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @php
                $payback = $building->income > 0 ? $building->price / $building->income : null;
            @endphp

            <p class="mt-4 text-sm text-slate-600">
                {{ __('Payback') }}:
                @if ($payback === null)
                    <span class="font-medium">{{ __('never') }}</span>
                @else
                    <span class="font-medium tabular-nums">{{ number_format($payback, 1, '.', ' ') }}</span> {{ __('periods') }}
                @endif
            </p>
        </div>
    </div>
@endsection
