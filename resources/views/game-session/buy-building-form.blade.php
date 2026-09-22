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
                        {{ $item->name }} ({{ $item->price }} $) - {{ $item->employees }}
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
