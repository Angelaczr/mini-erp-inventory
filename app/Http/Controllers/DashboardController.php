<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Warehouse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $warehouses = Warehouse::orderBy('name')->get();
        $items = Item::with('category')->orderBy('name')->get();

        // Single bulk query instead of looping currentStock() per item per
        // warehouse — avoids N+1 queries (was ~50+ queries, now 1).
        $stockMatrix = Item::stockMatrix();

        $lowStockItems = Item::lowStock()->with('category')->get();

        $totals = [
            'items' => $items->count(),
            'warehouses' => $warehouses->count(),
            'low_stock' => $lowStockItems->count(),
        ];

        return view('dashboard.index', compact(
            'warehouses',
            'items',
            'stockMatrix',
            'lowStockItems',
            'totals'
        ));
    }
}
