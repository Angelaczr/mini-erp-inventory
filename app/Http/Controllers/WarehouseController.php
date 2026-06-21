<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

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

    // public function destroy(Warehouse $warehouse): RedirectResponse
    // {
    //     $warehouse->delete();

    //     return redirect()
    //         ->route('warehouses.index')
    //         ->with('success', 'Warehouse deleted.');
    // }
    public function destroy(Warehouse $warehouse): RedirectResponse
{
    // Cek apakah gudang sudah pernah digunakan di stock_movements
    if ($warehouse->stockMovements()->exists()) {
        return redirect()
            ->route('warehouses.index')
            ->withErrors(['Gagal menghapus: Gudang ini sudah memiliki riwayat pergerakan stok.']);
    }

    $warehouse->delete();

    return redirect()
        ->route('warehouses.index')
        ->with('success', 'Warehouse deleted.');
}
}
