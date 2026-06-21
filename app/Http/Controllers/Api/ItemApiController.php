<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemApiController extends Controller
{
    /**
     * GET /api/items
     * Optional query params: ?category_id=&search=
     */
    public function index(Request $request): JsonResponse
    {
        $query = Item::with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('name')->paginate(20);

        return response()->json([
            'data' => $items->items(),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    /**
     * GET /api/items/{item}
     */
    public function show(Item $item): JsonResponse
    {
        $item->load('category');
        $item->current_stock = $item->currentStock();

        return response()->json(['data' => $item]);
    }
}
