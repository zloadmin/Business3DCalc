@extends('layouts.app')

@section('title', __('Create Game Session'))

@section('content')
    <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm p-4">
        <h2>Create Game Session</h2>

        <form method="POST" action="{{ route('game-session.create') }}" class="max-w-md space-y-6">
            @csrf

            <!-- Money -->
            <div>
                <label for="money" class="mb-2 block text-sm font-medium text-gray-900">
                    Money
                </label>

                <input
                    type="text"
                    id="money"
                    name="money"
                    placeholder="Enter money"
                    value="{{ old('money') ?? 1000 }}"
                    required
                    class="block w-full rounded-lg border bg-white px-4 py-2.5 text-sm text-gray-900 shadow-sm outline-none transition
                   placeholder:text-gray-400
                   focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
                   {{ $errors->has('money')
                        ? 'border-red-500 focus:border-red-500 focus:ring-red-500/20'
                        : 'border-gray-300' }}"
                >

                @if ($errors->has('money'))
                    <p class="mt-2 text-sm text-red-600">
                        {{ $errors->first('money') }}
                    </p>
                @endif
            </div>

            <!-- Time Speed -->
            <div>
                <label for="time_speed" class="mb-2 block text-sm font-medium text-gray-900">
                    Time Speed
                </label>

                <input
                    type="text"
                    id="time_speed"
                    name="time_speed"
                    placeholder="Enter time speed"
                    value="{{ old('time_speed') ?? 5 }}"
                    required
                    class="block w-full rounded-lg border bg-white px-4 py-2.5 text-sm text-gray-900 shadow-sm outline-none transition
                   placeholder:text-gray-400
                   focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
                   {{ $errors->has('time_speed')
                        ? 'border-red-500 focus:border-red-500 focus:ring-red-500/20'
                        : 'border-gray-300' }}"
                >

                @if ($errors->has('time_speed'))
                    <p class="mt-2 text-sm text-red-600">
                        {{ $errors->first('time_speed') }}
                    </p>
                @endif
            </div>

            <!-- Create -->
            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white
               shadow-sm transition
               hover:bg-blue-700
               focus:outline-none focus:ring-4 focus:ring-blue-300
               disabled:cursor-not-allowed disabled:opacity-50"
            >
                Create
            </button>
        </form>

    </div>


@endsection
