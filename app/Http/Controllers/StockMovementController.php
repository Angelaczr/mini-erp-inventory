<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Warehouse;
use Illuminate\View\View;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreStockMovementRequest;

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
        // $items = Item::orderBy('name')->get();
        // $warehouses = Warehouse::orderBy('name')->get();

        // return view('stock_movements.create', compact('items', 'warehouses'));

        $items = Item::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();

        // Ambil PO yang belum selesai untuk ditampilkan di dropdown
        $activePos = \App\Models\ProductionOrder::whereIn('status', ['planned', 'in_progress'])
            ->orderBy('po_number')
            ->get();

        return view('stock_movements.create', compact('items', 'warehouses', 'activePos'));
    }

    public function store(StoreStockMovementRequest $request): RedirectResponse
    {
        StockMovement::create($request->validated());

        return redirect()
            ->route('stock-movements.index')
            ->with('success', 'Stock movement recorded.');
    }
}
