@extends('layouts.app')

@section('title', 'Stock Movements')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Stock Movements</h1>
        <a href="{{ route('stock-movements.create') }}" class="btn btn-primary">+ Record Movement</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered bg-white">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Item</th>
                    <th>Warehouse</th>
                    <th>Type</th>
                    <th>Stage</th>
                    <th class="text-center">Qty</th>
                    <th>Reference</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($movements as $movement)
                    <tr>
                        <td>{{ $movement->created_at->format('d M Y H:i') }}</td>
                        <td>{{ $movement->item->name }}</td>
                        <td>{{ $movement->warehouse->name }}</td>
                        <td>
                            <span class="badge {{ $movement->type === 'in' ? 'bg-success' : ($movement->type === 'out' ? 'bg-danger' : 'bg-secondary') }}">
                                {{ strtoupper($movement->type) }}
                            </span>
                        </td>
                        <td>{{ $movement->production_stage !== 'n/a' ? ucfirst($movement->production_stage) : '—' }}</td>
                        <td class="text-center">{{ $movement->quantity }}</td>
                        <td>{{ $movement->reference_no ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">No movements recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $movements->links() }}
@endsection
