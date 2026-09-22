@extends('layouts.app')

@section('title', __('Buildings'))

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">{{ __('Buildings') }}</h1>
            <p class="mt-1 text-sm text-slate-600">{{ __('Building catalog and their stats.') }}</p>
        </div>

        <a href="{{ route('buildings.create') }}" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
            {{ __('Add building') }}
        </a>
    </div>

    <form method="GET" action="{{ route('buildings.index') }}" class="mb-6 flex flex-wrap items-end gap-3 rounded-lg border border-slate-200 bg-white p-4">
        <div class="min-w-[200px]">
            <label for="type" class="block text-sm font-medium text-slate-700">{{ __('Type') }}</label>
            <select name="type" id="type" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm">
                <option value="">{{ __('All types') }}</option>
                @foreach ($types as $value => $label)
                    <option value="{{ $value }}" @selected($type?->value === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
            {{ __('Filter') }}
        </button>

        @if ($type)
            <a href="{{ route('buildings.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                {{ __('Reset') }}
            </a>
        @endif
    </form>

    <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-600">
                <tr>
                    <th scope="col" class="px-4 py-3 font-medium">{{ __('Name') }}</th>
                    <th scope="col" class="px-4 py-3 font-medium">{{ __('Type') }}</th>
                    <th scope="col" class="px-4 py-3 text-right font-medium">{{ __('Price') }}</th>
                    <th scope="col" class="px-4 py-3 text-right font-medium">{{ __('Income') }}</th>
                    <th scope="col" class="px-4 py-3 text-right font-medium">{{ __('Upgrade time coefficient') }}</th>
                    <th scope="col" class="px-4 py-3 text-right font-medium">{{ __('Upgrade price coefficient') }}</th>
                    <th scope="col" class="px-4 py-3 text-right font-medium">{{ __('Employees') }}</th>
                    <th scope="col" class="px-4 py-3 text-right font-medium">{{ __('Residents capacity') }}</th>
                    <th scope="col" class="px-4 py-3 text-right font-medium">{{ __('Happiness') }}</th>
                    <th scope="col" class="px-4 py-3 text-right font-medium">{{ __('Attractiveness') }}</th>
                    <th scope="col" class="px-4 py-3 text-right font-medium">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($buildings as $building)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-900">
                            <a href="{{ route('buildings.show', $building) }}" class="hover:underline">{{ $building->name }}</a>
                        </td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">
                                {{ $building->type->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right tabular-nums">{{ number_format($building->price, 0, '.', ' ') }}</td>
                        <td class="px-4 py-3 text-right tabular-nums">

                            <span class="{{ $building->getIncome(1) < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ $building->getIncome(1) > 0 ? '+' : '' }}{{ $building->getIncome(1) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right tabular-nums">{{ number_format((float) $building->upgrade_time_coefficient, 3, '.', '') }}</td>
                        <td class="px-4 py-3 text-right tabular-nums">{{ number_format((float) $building->upgrade_price_coefficient, 3, '.', '') }}</td>
                        <td class="px-4 py-3 text-right tabular-nums">{{ number_format($building->employees, 0, '.', ' ') }}</td>
                        <td class="px-4 py-3 text-right tabular-nums">
                            <span class="{{ $building->residents_capacity < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ $building->residents_capacity > 0 ? '+' : '' }}{{ $building->residents_capacity }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right tabular-nums">
                            <span class="{{ $building->happiness < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ $building->happiness > 0 ? '+' : '' }}{{ $building->happiness }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right tabular-nums">
                            <span class="{{ $building->attractiveness < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ $building->attractiveness > 0 ? '+' : '' }}{{ $building->attractiveness }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            <a href="{{ route('buildings.updates', $building) }}" target="_blank" class="font-medium  text-blue-900 hover:underline">{{ __('Updates') }}</a>
                            <a href="{{ route('buildings.edit', $building) }}" class="font-medium text-slate-700 hover:underline">{{ __('Edit') }}</a>
                            <form method="POST" action="{{ route('buildings.destroy', $building) }}" class="inline-block" onsubmit="return confirm('{{ __('Delete this building?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ml-3 font-medium text-rose-600 hover:underline">{{ __('Delete') }}</button>
                            </form>
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

    @if ($buildings->hasPages())
        <div class="mt-4">
            {{ $buildings->links() }}
        </div>
    @endif
@endsection
