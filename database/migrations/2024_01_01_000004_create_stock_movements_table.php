<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['in', 'out', 'adjustment']);
            $table->enum('production_stage', ['cutting', 'sewing', 'dyeing', 'finishing', 'n/a'])
                ->default('n/a');
            $table->unsignedInteger('quantity');
            $table->string('reference_no', 50)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['item_id', 'warehouse_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
