<?php

namespace App\Http\Controllers;

use App\Enums\BuildingType;
use App\Http\Requests\StoreBuildingRequest;
use App\Http\Requests\UpdateBuildingRequest;
use App\Models\Building;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BuildingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $type = $request->enum('type', BuildingType::class);

        $buildings = Building::query()
            ->when($type, fn ($query) => $query->ofType($type))
            ->orderBy('type')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $totals = [
            'price' => (int) $buildings->getCollection()->sum('price'),
            'income' => (int) $buildings->getCollection()->sum('income'),
            'employees_count' => (int) $buildings->getCollection()->sum('employees_count'),
            'residents_count' => (int) $buildings->getCollection()->sum('residents_count'),
            'happiness' => (int) $buildings->getCollection()->sum('happiness'),
            'attractiveness' => (int) $buildings->getCollection()->sum('attractiveness'),
        ];

        return view('buildings.index', [
            'buildings' => $buildings,
            'types' => BuildingType::options(),
            'type' => $type,
            'totals' => $totals,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('buildings.create', [
            'building' => new Building,
            'types' => BuildingType::options(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBuildingRequest $request): RedirectResponse
    {
        Building::create($request->validated());

        return redirect()
            ->route('buildings.index')
            ->with('status', __('Building created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Building $building): View
    {
        return view('buildings.show', [
            'building' => $building,
            'incomeByLevel' => collect(range(1, 5))
                ->mapWithKeys(fn (int $level) => [
                    $level => $building->incomeAtLevel($level),
                ]),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Building $building): View
    {
        return view('buildings.edit', [
            'building' => $building,
            'types' => BuildingType::options(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBuildingRequest $request, Building $building): RedirectResponse
    {
        $building->update($request->validated());

        return redirect()
            ->route('buildings.index')
            ->with('status', __('Building updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Building $building): RedirectResponse
    {
        $building->delete();

        return redirect()
            ->route('buildings.index')
            ->with('status', __('Building deleted successfully.'));
    }
}
