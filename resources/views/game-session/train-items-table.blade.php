<div class="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
    <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-600">
        <tr>
            <th scope="col" class="px-4 py-3 font-medium">{{ __('#') }}</th>
            <th scope="col" class="px-4 py-3 font-medium">{{ __('Money') }}</th>
            <th scope="col" class="px-4 py-3 font-medium">{{ __('Population') }}</th>
            <th scope="col" class="px-4 py-3 font-medium">{{ __('Population Capacity') }}</th>
            <th scope="col" class="px-4 py-3 font-medium">{{ __('Employees') }}</th>
            <th scope="col" class="px-4 py-3 font-medium">{{ __('Unemployed') }}</th>
            <th scope="col" class="px-4 py-3 font-medium">{{ __('Happiness') }}</th>
            <th scope="col" class="px-4 py-3 font-medium">{{ __('Attractiveness') }}</th>
            <th scope="col" class="px-4 py-3 font-medium">{{ __('Coming') }}</th>
            <th scope="col" class="px-4 py-3 font-medium">{{ __('Leave') }}</th>
            <th scope="col" class="px-4 py-3 font-medium">{{ __('Result') }}</th>
            <th scope="col" class="px-4 py-3 font-medium">{{ __('Date') }}</th>
            <th scope="col" class="px-4 py-3 font-medium">{{ __('Game time') }}</th>
        </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
        @forelse ($trainItems as $trainItem)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-right tabular-nums">
                    {{ $trainItem->id }}
                </td>

                <td class="px-4 py-3 text-right tabular-nums">
                    {{ number_format($trainItem->money) }}
                </td>

                <td class="px-4 py-3 text-right tabular-nums">
                    {{ number_format($trainItem->population) }}
                </td>
                <td class="px-4 py-3 text-right tabular-nums">
                    {{ number_format($trainItem->population_capacity) }}
                </td>

                <td class="px-4 py-3 text-right tabular-nums">
                    {{ number_format($trainItem->employees) }}
                </td>

                <td class="px-4 py-3 text-right tabular-nums">
                    {{ number_format($trainItem->unemployed) }}
                </td>

                <td class="px-4 py-3 text-right tabular-nums">
                    {{ number_format($trainItem->happiness, 2) }}
                </td>

                <td class="px-4 py-3 text-right tabular-nums">
                    {{ number_format($trainItem->attractiveness, 2) }}
                </td>

                <td class="px-4 py-3 text-right tabular-nums text-green-600">
                    +{{ number_format($trainItem->coming) }}
                </td>

                <td class="px-4 py-3 text-right tabular-nums text-red-600">
                    -{{ number_format($trainItem->leave) }}
                </td>

                <td class="px-4 py-3 text-right font-medium tabular-nums
                    {{ $trainItem->result > 0 ? 'text-green-600' : ($trainItem->result < 0 ? 'text-red-600' : 'text-slate-600') }}">
                    {{ $trainItem->result > 0 ? '+' : '' }}{{ number_format($trainItem->result) }}
                </td>

                <td class="px-4 py-3 whitespace-nowrap text-slate-500">
                    {{ $trainItem->created_at->format('d.m.Y H:i:s') }}
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-slate-500">
                    {{ $trainItem->game_time }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="11" class="px-4 py-8 text-center text-slate-500">
                    {{ __('No train items yet.') }}
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
