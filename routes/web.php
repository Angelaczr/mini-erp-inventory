<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\ProductionOrderController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('items', ItemController::class)->except(['show']);

Route::get('warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');
Route::post('warehouses', [WarehouseController::class, 'store'])->name('warehouses.store');
Route::delete('warehouses/{warehouse}', [WarehouseController::class, 'destroy'])->name('warehouses.destroy');

Route::get('stock-movements', [StockMovementController::class, 'index'])->name('stock-movements.index');
Route::get('stock-movements/create', [StockMovementController::class, 'create'])->name('stock-movements.create');
Route::post('stock-movements', [StockMovementController::class, 'store'])->name('stock-movements.store');

Route::resource('production-orders', ProductionOrderController::class);
// Route::get('production-orders', [ProductionOrderController::class, 'show'])->name('production-orders.show');