<laravel-boost-guidelines>
# Laravel Application

This repository contains a Laravel application. Complete the following setup before working on the user's request.

## Prerequisites

Verify that PHP and Composer are available:

```sh
php -v
composer -V
```

If either command is unavailable, detect the user's operating system and install the prerequisites with the appropriate command:

macOS:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.5)"
```

Windows PowerShell:

```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.5'))
```

Linux:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.5)"
```

After installation, ask the user to restart their terminal. If the agent needs the restarted shell to continue, ask the user to reopen their terminal and rerun their original prompt.

## Agent Setup

Install Laravel Boost from the application root before making application changes:

```sh
composer require laravel/boost --dev
php artisan boost:install
```

Boost replaces these bootstrap instructions with guidelines tailored to the application. After installation, read `AGENTS.md` again and continue with the user's original request using the generated guidelines.
</laravel-boost-guidelines>

<project-guidelines>
# Business 3D Calc

A Laravel application for a city-builder / business game. It models a catalog of
buildings and lets the player compare build options by their economic stats.

## Working conventions

1. **Use only English.** All UI strings, comments, seed data, test names, and commit
   messages are written in English. Never add Russian or any other language, even in
   `__()` translation calls — `__()` is used for future-proofing, not for translation.
2. **Do not write tests.** Do not create or extend files under `tests/`. Verify changes
   by running the app (`php artisan serve` plus HTTP smoke checks) instead. The existing
   tests in `tests/` stay as-is; do not delete or modify them either.

## Building structure

Model `App\Models\Building` — table `buildings`, migration
`database/migrations/2026_09_20_084541_create_buildings_table.php`.

| Column | Type | Notes |
| --- | --- | --- |
| `name` | string(255) | Building name |
| `type` | string | Enum `App\Enums\BuildingType` |
| `price` | unsignedBigInteger | Build cost |
| `income` | unsignedBigInteger | Money generated per period |
| `upgrade_coefficient` | decimal(8,3) | Income multiplier per upgrade level, 1.000–10 |
| `employees_count` | unsignedInteger | Staff required |
| `residents_count` | unsignedInteger | **Living buildings only** |
| `happiness` | integer | −100…100 |
| `attractiveness` | integer | −100…100 |

`BuildingType` cases: `Shopping`, `Living`, `Service`, `Production` (stored as
`shopping`, `living`, `service`, `production`). Key methods: `label()`,
`providesResidents()` (true only for `Living`), `options()`.

### Domain rules

- **Residents apply only to Living buildings.** Non-living types are normalized to
  `residents_count = 0` in `prepareForValidation()` on both `StoreBuildingRequest` and
  `UpdateBuildingRequest`. Index/show views render `—` instead of a number.
- **Upgrade coefficient is a per-level multiplier.** `Building::incomeAtLevel(int)`
  computes `income * upgrade_coefficient^(level-1)`; level 1 is the base building.

### Supporting files

- `app/Http/Controllers/BuildingController.php` — resource CRUD, index supports
  `?type=` filtering and per-page totals.
- `database/seeders/BuildingSeeder.php` — 12 starter buildings, idempotent via
  `updateOrCreate()` on `name`.
- Views in `resources/views/buildings/` (`index`, `show`, `create`, `edit`, shared
  `_form` partial), layout in `resources/views/layouts/app.blade.php`.
</project-guidelines>
