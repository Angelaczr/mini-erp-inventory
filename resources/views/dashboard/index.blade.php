@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1 class="h3 mb-4">Inventory Dashboard</h1>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Total Items</h6>
                    <p class="fs-3 fw-bold mb-0">{{ $totals['items'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Warehouses</h6>
                    <p class="fs-3 fw-bold mb-0">{{ $totals['warehouses'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center border-danger">
                <div class="card-body">
                    <h6 class="text-muted">Low Stock Alerts</h6>
                    <p class="fs-3 fw-bold mb-0 text-danger">{{ $totals['low_stock'] }}</p>
                </div>
            </div>
        </div>
    </div>

    @if ($lowStockItems->isNotEmpty())
        <div class="alert alert-warning">
            <strong>⚠️ Low stock:</strong>
            {{ $lowStockItems->pluck('name')->join(', ') }}
        </div>
    @endif

    <h2 class="h5 mb-3">Stock by Warehouse</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-sm bg-white">
            <thead class="table-light">
                <tr>
                    <th>Item</th>
                    <th>Category</th>
                    @foreach ($warehouses as $warehouse)
                        <th class="text-center">{{ $warehouse->name }}</th>
                    @endforeach
                    <th class="text-center">Total</th>
                    <th class="text-center">Reorder Level</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    @php $total = 0; @endphp
                    <tr>
                        <td>{{ $item->name }} <span class="text-muted">({{ $item->sku }})</span></td>
                        <td>{{ $item->category->name }}</td>
                        @foreach ($warehouses as $warehouse)
                            @php
                                $qty = $stockMatrix[$item->id][$warehouse->id] ?? 0;
                                $total += $qty;
                            @endphp
                            <td class="text-center">{{ $qty }} {{ $item->unit }}</td>
                        @endforeach
                        <td class="text-center fw-bold {{ $total <= $item->reorder_level ? 'text-danger' : '' }}">
                            {{ $total }} {{ $item->unit }}
                        </td>
                        <td class="text-center">{{ $item->reorder_level }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
