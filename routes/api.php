<?php

use App\Http\Controllers\Api\ItemApiController;
use App\Http\Controllers\Api\StockApiController;
use Illuminate\Support\Facades\Route;

Route::get('items', [ItemApiController::class, 'index']);
Route::get('items/{item}', [ItemApiController::class, 'show']);

Route::get('stock-summary', [StockApiController::class, 'summary']);
Route::get('stock-summary/low-stock', [StockApiController::class, 'lowStock']);

Route::get('warehouses', [StockApiController::class, 'warehouses']);
