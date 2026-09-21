<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', __('Buildings')) — {{ config('app.name', 'Laravel') }}</title>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-slate-100 text-slate-900 antialiased">
        <div class="min-h-screen">
            <header class="border-b border-slate-200 bg-white">
                <nav class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                    <a href="{{ route('buildings.index') }}" class="text-lg font-semibold tracking-tight">
                        {{ config('app.name', 'Laravel') }}
                    </a>

                    <div class="flex items-center gap-4">
                        <a href="{{ route('buildings.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">
                            {{ __('Buildings') }}
                        </a>
                        <a href="{{ route('game-session.create') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">
                            {{ __('Create Game Session') }}
                        </a>
                        <a href="{{ route('buildings.create') }}" class="rounded-md bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                            {{ __('Add building') }}
                        </a>
                    </div>
                </nav>
            </header>

            <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                @if (session('status'))
                    <div class="mb-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                        {{ session('status') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </body>
</html>
