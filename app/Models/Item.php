<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['sku', 'name', 'category_id', 'unit', 'reorder_level'];

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
            ->havingRaw('current_stock <= items.reorder_level');
    }
}
