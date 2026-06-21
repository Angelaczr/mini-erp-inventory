<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {

        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique(); // Contoh: PO-JNS-2606-001
            $table->foreignId('item_id')->constrained('items'); // Target Barang Jadi (Finished Good)
            $table->integer('target_quantity');
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('planned');
            $table->timestamps();
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['in', 'out', 'adjustment']);
            // $table->enum('production_stage', ['cutting', 'sewing', 'dyeing', 'finishing', 'n/a'])
            //     ->default('n/a');
            $table->enum('production_stage', ['cutting', 'sewing', 'dyeing', 'finishing', 'bikin_bagus', 'n/a'])
                ->default('n/a');
            $table->unsignedInteger('quantity');
            $table->string('reference_no', 50)->nullable();
            $table->foreignId('production_order_id')->nullable()->constrained()->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['item_id', 'warehouse_id']);
        });


    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('production_orders');
    }
};
