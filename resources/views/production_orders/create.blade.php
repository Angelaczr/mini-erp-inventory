@extends('layouts.app')

@section('title', 'Create Production Order')

@section('content')
    <h1 class="h3 mb-4">Create Production Order (PO)</h1>

    <div class="card shadow-sm col-md-8">
        <div class="card-body">
            <form action="{{ route('production-orders.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">PO Number</label>
                    <input type="text" name="po_number" class="form-control" value="{{ old('po_number') }}" placeholder="e.g. PO-JNS-2607-001" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Target Item (Finished Good)</label>
                    <select name="item_id" class="form-select" required>
                        <option value="">-- Select Target Item --</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}" @selected(old('item_id') == $item->id)>
                                {{ $item->name }} ({{ $item->sku }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label">Target Quantity</label>
                    <input type="number" name="target_quantity" class="form-control" min="1" value="{{ old('target_quantity') }}" required>
                </div>

                <button class="btn btn-primary">Create PO</button>
                <a href="{{ route('production-orders.index') }}" class="btn btn-link">Cancel</a>
            </form>
        </div>
    </div>
@endsection