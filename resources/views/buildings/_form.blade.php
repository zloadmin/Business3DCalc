@csrf

@if ($building->exists)
    @method('PUT')
@endif

<div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label for="name" class="block text-sm font-medium text-slate-700">{{ __('Name') }}</label>
        <input type="text" name="name" id="name" value="{{ old('name', $building->name) }}" required maxlength="255"
            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm">
        @error('name')
            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="type" class="block text-sm font-medium text-slate-700">{{ __('Type') }}</label>
        <select name="type" id="type" required class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm">
            @foreach ($types as $value => $label)
                <option value="{{ $value }}" @selected(old('type', $building->exists ? $building->type->value : null) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('type')
            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="price" class="block text-sm font-medium text-slate-700">{{ __('Price') }}</label>
        <input type="number" name="price" id="price" value="{{ old('price', $building->price ?? 0) }}" required min="0" step="1"
            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm">
        @error('price')
            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="income" class="block text-sm font-medium text-slate-700">{{ __('Income generated') }}</label>
        <input type="number" name="income" id="income" value="{{ old('income', $building->income ?? 0) }}" required min="0" step="1"
            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm">
        @error('income')
            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="upgrade_coefficient" class="block text-sm font-medium text-slate-700">{{ __('Upgrade coefficient') }}</label>
        <input type="number" name="upgrade_coefficient" id="upgrade_coefficient"
            value="{{ old('upgrade_coefficient', $building->exists ? number_format((float) $building->upgrade_coefficient, 3, '.', '') : '1.000') }}"
            required min="1" max="10" step="0.001"
            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm">
        <p class="mt-1 text-xs text-slate-500">{{ __('Income multiplier applied per upgrade level.') }}</p>
        @error('upgrade_coefficient')
            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="employees_count" class="block text-sm font-medium text-slate-700">{{ __('Employees') }}</label>
        <input type="number" name="employees_count" id="employees_count" value="{{ old('employees_count', $building->employees_count ?? 0) }}" required min="0" step="1"
            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm">
        @error('employees_count')
            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="residents_count" class="block text-sm font-medium text-slate-700">{{ __('Residents') }}</label>
        <input type="number" name="residents_count" id="residents_count" value="{{ old('residents_count', $building->residents_count ?? 0) }}" required min="0" step="1"
            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm">
        <p class="mt-1 text-xs text-slate-500">{{ __('Residential buildings only. Other types are reset to 0.') }}</p>
        @error('residents_count')
            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="happiness" class="block text-sm font-medium text-slate-700">{{ __('Happiness provided') }}</label>
        <input type="number" name="happiness" id="happiness" value="{{ old('happiness', $building->happiness ?? 0) }}" required min="-100" max="100" step="1"
            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm">
        @error('happiness')
            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="attractiveness" class="block text-sm font-medium text-slate-700">{{ __('Attractiveness provided') }}</label>
        <input type="number" name="attractiveness" id="attractiveness" value="{{ old('attractiveness', $building->attractiveness ?? 0) }}" required min="-100" max="100" step="1"
            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm">
        @error('attractiveness')
            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mt-8 flex items-center gap-4">
    <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
        {{ $building->exists ? __('Save') : __('Create') }}
    </button>

    <a href="{{ route('buildings.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">
        {{ __('Cancel') }}
    </a>
</div>
