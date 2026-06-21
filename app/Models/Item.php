<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['sku', 'name', 'category_id', 'unit', 'reorder_level'];

    /**
     * Bulk-calculate current stock per item, grouped by warehouse, in a
     * single query. Avoids N+1 queries when building a stock matrix for
     * many items across many warehouses (e.g. on the dashboard).
     *
     * @return array<int, array<int, int>> [item_id => [warehouse_id => qty]]
     */
    public static function stockMatrix(): array
    {
        $rows = StockMovement::selectRaw("
                item_id, warehouse_id,
                COALESCE(SUM(CASE WHEN type IN ('in','adjustment') THEN quantity ELSE 0 END), 0)
                - COALESCE(SUM(CASE WHEN type = 'out' THEN quantity ELSE 0 END), 0) as net_qty
            ")
            ->groupBy('item_id', 'warehouse_id')
            ->get();

        $matrix = [];
        foreach ($rows as $row) {
            $matrix[$row->item_id][$row->warehouse_id] = (int) $row->net_qty;
        }

        return $matrix;
    }

    /**
     * Bulk-calculate total current stock per item (all warehouses combined)
     * in a single query. Used for list views to avoid N+1 queries.
     *
     * @return array<int, int> [item_id => total_qty]
     */
    public static function totalStockMap(): array
    {
        return StockMovement::selectRaw("
                item_id,
                COALESCE(SUM(CASE WHEN type IN ('in','adjustment') THEN quantity ELSE 0 END), 0)
                - COALESCE(SUM(CASE WHEN type = 'out' THEN quantity ELSE 0 END), 0) as net_qty
            ")
            ->groupBy('item_id')
            ->pluck('net_qty', 'item_id')
            ->map(fn ($v) => (int) $v)
            ->toArray();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Calculate current stock for this item, optionally scoped to one warehouse.
     * IN movements add stock, OUT subtracts, ADJUSTMENT can be +/- via quantity sign
     * is not used here — adjustment quantity is always treated as a correction add.
     */
    public function currentStock(?int $warehouseId = null): int
    {
        $query = $this->stockMovements();

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        $in = (clone $query)->whereIn('type', ['in', 'adjustment'])->sum('quantity');
        $out = (clone $query)->where('type', 'out')->sum('quantity');

        return $in - $out;
    }

    /**
     * Scope: items whose current total stock has fallen at/below their reorder level.
     * Demonstrates a SQL aggregation + HAVING clause via Eloquent.
     */
    // public function scopeLowStock(Builder $query): Builder
    // {
    //     return $query->select('items.*')
    //         ->selectRaw('
    //             COALESCE(SUM(CASE WHEN stock_movements.type IN (\'in\',\'adjustment\') THEN stock_movements.quantity ELSE 0 END), 0)
    //             - COALESCE(SUM(CASE WHEN stock_movements.type = \'out\' THEN stock_movements.quantity ELSE 0 END), 0)
    //             AS current_stock
    //         ')
    //         ->leftJoin('stock_movements', 'stock_movements.item_id', '=', 'items.id')
    //         ->groupBy('items.id', 'items.sku', 'items.name', 'items.category_id', 'items.unit', 'items.reorder_level', 'items.created_at', 'items.updated_at')
    //         ->havingRaw('current_stock <= items.reorder_level');
    // }

    public function scopeLowStock(Builder $query): Builder
    {
        return $query->select('items.*')
            ->selectRaw('
                COALESCE(SUM(CASE WHEN stock_movements.type IN (\'in\',\'adjustment\') THEN stock_movements.quantity ELSE 0 END), 0)
                - COALESCE(SUM(CASE WHEN stock_movements.type = \'out\' THEN stock_movements.quantity ELSE 0 END), 0)
                AS current_stock
            ')
            ->leftJoin('stock_movements', 'stock_movements.item_id', '=', 'items.id')
            ->groupBy('items.id', 'items.sku', 'items.name', 'items.category_id', 'items.unit', 'items.reorder_level', 'items.created_at', 'items.updated_at')
            ->havingRaw('
                (
                    COALESCE(SUM(CASE WHEN stock_movements.type IN (\'in\',\'adjustment\') THEN stock_movements.quantity ELSE 0 END), 0)
                    - COALESCE(SUM(CASE WHEN stock_movements.type = \'out\' THEN stock_movements.quantity ELSE 0 END), 0)
                ) <= items.reorder_level
            ');
    }
}
