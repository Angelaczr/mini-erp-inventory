@extends('layouts.app')

@section('title', 'Record Stock Movement')

@section('content')
    <h1 class="h3 mb-4">Record Stock Movement</h1>

    <form action="{{ route('stock-movements.store') }}" method="POST" class="bg-white p-4 rounded shadow-sm">
        @csrf

        <div class="mb-3">
            <label class="form-label">Item</label>
            <select name="item_id" class="form-select" required>
                <option value="">-- Select Item --</option>
                @foreach ($items as $item)
                    <option value="{{ $item->id }}" @selected(old('item_id') == $item->id)>
                        {{ $item->name }} ({{ $item->sku }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Warehouse</label>
            <select name="warehouse_id" class="form-select" required>
                <option value="">-- Select Warehouse --</option>
                @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" @selected(old('warehouse_id') == $warehouse->id)>
                        {{ $warehouse->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Movement Type</label>
                <select name="type" class="form-select" required>
                    <option value="in" @selected(old('type') === 'in')>IN</option>
                    <option value="out" @selected(old('type') === 'out')>OUT</option>
                    <option value="adjustment" @selected(old('type') === 'adjustment')>ADJUSTMENT</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Production Stage</label>
                <select name="production_stage" class="form-select">
                    <option value="n/a">N/A</option>
                    <option value="cutting" @selected(old('production_stage') === 'cutting')>Cutting</option>
                    <option value="sewing" @selected(old('production_stage') === 'sewing')>Sewing</option>
                    <option value="dyeing" @selected(old('production_stage') === 'dyeing')>Dyeing</option>
                    <option value="finishing" @selected(old('production_stage') === 'finishing')>Finishing</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Quantity</label>
            <input type="number" name="quantity" class="form-control" min="1" value="{{ old('quantity') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Reference No.</label>
            <input type="text" name="reference_no" class="form-control" value="{{ old('reference_no') }}" placeholder="e.g. PO-2026-010">
        </div>

        <div class="mb-3">
            <label class="form-label">Note</label>
            <textarea name="note" class="form-control" rows="2">{{ old('note') }}</textarea>
        </div>

        <button class="btn btn-primary">Save</button>
        <a href="{{ route('stock-movements.index') }}" class="btn btn-link">Cancel</a>
    </form>
@endsection
