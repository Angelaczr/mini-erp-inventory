<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\ProductionOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;

class ProductionOrderController extends Controller
{
    public function index(): View
    {
        $productionOrders = ProductionOrder::with('item')->latest()->paginate(15);
        return view('production_orders.index', compact('productionOrders'));
    }

    public function create(): View
    {
        // Hanya tampilkan item yang masuk kategori "Finished Goods" (Barang Jadi)
        // Kita asumsikan Finished Goods punya nama kategori tersebut, 
        // atau kamu bisa load semua item sementara jika belum difilter ketat.
        $items = Item::whereHas('category', function ($q) {
            $q->where('name', 'Finished Goods');
        })->orderBy('name')->get();

        return view('production_orders.create', compact('items'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'po_number' => 'required|string|max:50|unique:production_orders',
            'item_id' => 'required|exists:items,id',
            'target_quantity' => 'required|integer|min:1',
        ]);

        // Status default akan otomatis 'planned' sesuai migration
        ProductionOrder::create($validated);

        return redirect()
            ->route('production-orders.index')
            ->with('success', 'Production Order berhasil dibuat.');
    }

    public function show(ProductionOrder $productionOrder): View
    {
        // 1. Eager load relasi item agar efisien saat dipanggil di view
        $productionOrder->load('item');

        // 2. The Aggregate Query: Menghitung net stock (IN - OUT) per stage
        $stockPerStage = DB::table('stock_movements')
            ->select('production_stage')
            ->selectRaw("
                COALESCE(SUM(CASE WHEN type IN ('in', 'adjustment') THEN quantity ELSE 0 END), 0) -
                COALESCE(SUM(CASE WHEN type = 'out' THEN quantity ELSE 0 END), 0)
                AS net_qty
            ")
            ->where('production_order_id', $productionOrder->id)
            ->groupBy('production_stage')
            ->pluck('net_qty', 'production_stage')
            ->toArray();

        // 3. Mapping hasil query ke format array yang dikenali oleh view
        // Jika sebuah stage belum ada transaksinya, hasilnya otomatis default ke 0
        $stageStock = [
            'potongan' => $stockPerStage['cutting'] ?? 0,
            'jahit' => $stockPerStage['sewing'] ?? 0,
            'pewarnaan' => $stockPerStage['dyeing'] ?? 0,
            'finishing' => $stockPerStage['finishing'] ?? 0,

            // Repair log "Bikin Bagus" diisolasi dari alur normal
            'bikin_bagus' => $stockPerStage['bikin_bagus'] ?? 0,
        ];

        return view('production_orders.show', [
            'po' => $productionOrder,
            'stageStock' => $stageStock
        ]);
    }
}
