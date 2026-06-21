<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class StockApiController extends Controller
{
    /**
     * GET /api/stock-summary
     * Optional query param: ?warehouse_id=
     * Returns current stock per item, optionally scoped to one warehouse.
     */
    // public function summary(Request $request): JsonResponse
    // {
    //     $warehouseId = $request->input('warehouse_id');
    //     $items = Item::with('category')->orderBy('name')->get();

    //     $data = $items->map(function (Item $item) use ($warehouseId) {
    //         return [
    //             'id' => $item->id,
    //             'sku' => $item->sku,
    //             'name' => $item->name,
    //             'category' => $item->category->name,
    //             'unit' => $item->unit,
    //             'reorder_level' => $item->reorder_level,
    //             'current_stock' => $item->currentStock($warehouseId ? (int) $warehouseId : null),
    //         ];
    //     });

    //     return response()->json([
    //         'warehouse_id' => $warehouseId,
    //         'data' => $data,
    //     ]);
    // }

    public function summary(Request $request): JsonResponse
    {
        $warehouseId = $request->input('warehouse_id');
        $items = Item::with('category')->orderBy('name')->get();

        // 1. Ambil data stok secara bulk (cukup 1 query ke DB)
        if ($warehouseId) {
            $matrix = Item::stockMatrix();
        } else {
            $totalMap = Item::totalStockMap();
        }

        // 2. Mapping data di memori PHP, bukan bolak-balik ke DB
        $data = $items->map(function (Item $item) use ($warehouseId, $matrix, $totalMap) {
            
            // Ambil qty dari array memori
            if ($warehouseId) {
                $stockQty = $matrix[$item->id][(int) $warehouseId] ?? 0;
            } else {
                $stockQty = $totalMap[$item->id] ?? 0;
            }

            return [
                'id' => $item->id,
                'sku' => $item->sku,
                'name' => $item->name,
                'category' => $item->category->name,
                'unit' => $item->unit,
                'reorder_level' => $item->reorder_level,
                'current_stock' => $stockQty, // <-- Instan dari memori
            ];
        });

        return response()->json([
            'warehouse_id' => $warehouseId,
            'data' => $data,
        ]);
    }

    /**
     * GET /api/stock-summary/low-stock
     */
    public function lowStock(): JsonResponse
    {
        $items = Item::lowStock()->with('category')->get();

        return response()->json(['data' => $items]);
    }

    /**
     * GET /api/warehouses
     */
    public function warehouses(): JsonResponse
    {
        return response()->json(['data' => Warehouse::orderBy('name')->get()]);
    }
}
