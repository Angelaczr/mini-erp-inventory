<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Guard: skip seeding if data already exists, so this stays safe
        // to re-run on every container boot without creating duplicates.
        if (Warehouse::count() > 0) {
            return;
        }

        // 3 warehouses, mirroring a multi-warehouse garment manufacturing setup
        $warehouses = [
            Warehouse::create(['code' => 'WH-01', 'name' => 'Main Warehouse', 'location' => 'Jakarta Pusat']),
            Warehouse::create(['code' => 'WH-02', 'name' => 'Production Warehouse', 'location' => 'Jakarta Timur']),
            Warehouse::create(['code' => 'WH-03', 'name' => 'Finished Goods Warehouse', 'location' => 'Jakarta Utara']),
        ];

        $categories = [
            Category::create(['name' => 'Raw Material']),
            Category::create(['name' => 'Work in Progress']),
            Category::create(['name' => 'Finished Goods']),
            Category::create(['name' => 'Packaging']),
        ];

        $items = [
            Item::create(['sku' => 'RM-DNM-001', 'name' => 'Denim Fabric - Indigo 14oz', 'category_id' => $categories[0]->id, 'unit' => 'meter', 'reorder_level' => 500]),
            Item::create(['sku' => 'RM-DNM-002', 'name' => 'Denim Fabric - Black 12oz', 'category_id' => $categories[0]->id, 'unit' => 'meter', 'reorder_level' => 300]),
            Item::create(['sku' => 'RM-THR-001', 'name' => 'Sewing Thread - Navy', 'category_id' => $categories[0]->id, 'unit' => 'spool', 'reorder_level' => 100]),
            Item::create(['sku' => 'RM-BTN-001', 'name' => 'Jeans Button - 17mm', 'category_id' => $categories[0]->id, 'unit' => 'pcs', 'reorder_level' => 1000]),
            Item::create(['sku' => 'WIP-CUT-001', 'name' => 'Cut Panels - Slim Fit 32', 'category_id' => $categories[1]->id, 'unit' => 'set', 'reorder_level' => 50]),
            Item::create(['sku' => 'WIP-SEW-001', 'name' => 'Sewn Pieces - Slim Fit 32', 'category_id' => $categories[1]->id, 'unit' => 'pcs', 'reorder_level' => 50]),
            Item::create(['sku' => 'FG-JNS-001', 'name' => 'Jeans Slim Fit - Size 32', 'category_id' => $categories[2]->id, 'unit' => 'pcs', 'reorder_level' => 100]),
            Item::create(['sku' => 'FG-JNS-002', 'name' => 'Jeans Regular Fit - Size 34', 'category_id' => $categories[2]->id, 'unit' => 'pcs', 'reorder_level' => 100]),
            Item::create(['sku' => 'PKG-BOX-001', 'name' => 'Shipping Box - Medium', 'category_id' => $categories[3]->id, 'unit' => 'pcs', 'reorder_level' => 200]),
        ];

        // Dummy stock movements across warehouses & production stages
        $movements = [
            ['item' => 0, 'wh' => 0, 'type' => 'in', 'stage' => 'n/a', 'qty' => 2000, 'ref' => 'PO-2026-001'],
            ['item' => 1, 'wh' => 0, 'type' => 'in', 'stage' => 'n/a', 'qty' => 1200, 'ref' => 'PO-2026-002'],
            ['item' => 2, 'wh' => 0, 'type' => 'in', 'stage' => 'n/a', 'qty' => 500, 'ref' => 'PO-2026-003'],
            ['item' => 3, 'wh' => 0, 'type' => 'in', 'stage' => 'n/a', 'qty' => 5000, 'ref' => 'PO-2026-004'],

            ['item' => 0, 'wh' => 0, 'type' => 'out', 'stage' => 'cutting', 'qty' => 800, 'ref' => 'WO-2026-101'],
            ['item' => 4, 'wh' => 1, 'type' => 'in', 'stage' => 'cutting', 'qty' => 300, 'ref' => 'WO-2026-101'],

            ['item' => 4, 'wh' => 1, 'type' => 'out', 'stage' => 'sewing', 'qty' => 250, 'ref' => 'WO-2026-102'],
            ['item' => 5, 'wh' => 1, 'type' => 'in', 'stage' => 'sewing', 'qty' => 240, 'ref' => 'WO-2026-102'],

            ['item' => 5, 'wh' => 1, 'type' => 'out', 'stage' => 'finishing', 'qty' => 240, 'ref' => 'WO-2026-103'],
            ['item' => 6, 'wh' => 2, 'type' => 'in', 'stage' => 'finishing', 'qty' => 230, 'ref' => 'WO-2026-103'],

            ['item' => 6, 'wh' => 2, 'type' => 'out', 'stage' => 'n/a', 'qty' => 150, 'ref' => 'SO-2026-201'],
            ['item' => 7, 'wh' => 2, 'type' => 'in', 'stage' => 'n/a', 'qty' => 90, 'ref' => 'WO-2026-104'],
            ['item' => 8, 'wh' => 2, 'type' => 'in', 'stage' => 'n/a', 'qty' => 180, 'ref' => 'PO-2026-005'],
            ['item' => 8, 'wh' => 2, 'type' => 'out', 'stage' => 'n/a', 'qty' => 60, 'ref' => 'SO-2026-202'],
        ];

        foreach ($movements as $m) {
            StockMovement::create([
                'item_id' => $items[$m['item']]->id,
                'warehouse_id' => $warehouses[$m['wh']]->id,
                'type' => $m['type'],
                'production_stage' => $m['stage'],
                'quantity' => $m['qty'],
                'reference_no' => $m['ref'],
            ]);
        }
    }
}
