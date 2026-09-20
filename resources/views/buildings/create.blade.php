@extends('layouts.app')

@section('title', __('New building'))

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight">{{ __('New building') }}</h1>
        <p class="mt-1 text-sm text-slate-600">{{ __('Fill in the building stats.') }}</p>
    </div>

    <div class="max-w-4xl rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('buildings.store') }}">
            @include('buildings._form')
        </form>
    </div>
@endsection
