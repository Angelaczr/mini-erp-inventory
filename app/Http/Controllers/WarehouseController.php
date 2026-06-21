<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WarehouseController extends Controller
{
    public function index(): View
    {
        $warehouses = Warehouse::orderBy('name')->get();
        return view('warehouses.index', compact('warehouses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:warehouses,code',
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        Warehouse::create($validated);

        return redirect()
            ->route('warehouses.index')
            ->with('success', 'Warehouse created.');
    }

    public function destroy(Warehouse $warehouse): RedirectResponse
    {
        $warehouse->delete();

        return redirect()
            ->route('warehouses.index')
            ->with('success', 'Warehouse deleted.');
    }
}
