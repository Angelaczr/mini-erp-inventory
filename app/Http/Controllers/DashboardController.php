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

        // Build a stock matrix: item x warehouse => current quantity
        $stockMatrix = [];
        foreach ($items as $item) {
            foreach ($warehouses as $warehouse) {
                $stockMatrix[$item->id][$warehouse->id] = $item->currentStock($warehouse->id);
            }
        }

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
