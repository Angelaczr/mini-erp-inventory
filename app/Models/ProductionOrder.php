<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductionOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number',
        'item_id',
        'target_quantity',
        'status',
    ];

    /**
     * Target barang jadi yang akan diproduksi oleh PO ini.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Riwayat pergerakan stok (cutting, sewing, dyeing, finishing) untuk PO ini.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}
