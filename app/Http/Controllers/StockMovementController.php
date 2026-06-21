<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockMovementRequest;
use App\Models\Item;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    public function index(): View
    {
        $movements = StockMovement::with(['item', 'warehouse'])
            ->latest()
            ->paginate(20);

        return view('stock_movements.index', compact('movements'));
    }

    public function create(): View
    {
        $items = Item::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();

        return view('stock_movements.create', compact('items', 'warehouses'));
    }

    public function store(StoreStockMovementRequest $request): RedirectResponse
    {
        StockMovement::create($request->validated());

        return redirect()
            ->route('stock-movements.index')
            ->with('success', 'Stock movement recorded.');
    }
}
