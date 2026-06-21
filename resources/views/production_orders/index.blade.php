@extends('layouts.app')

@section('title', 'Production Orders')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Production Orders</h1>
        <a href="{{ route('production-orders.create') }}" class="btn btn-primary">+ Create New PO</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>PO Number</th>
                        <th>Target Item</th>
                        <th class="text-center">Target Qty</th>
                        <th class="text-center">Status</th>
                        <th>Created At</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($productionOrders as $po)
                        <tr>
                            <td class="fw-bold">{{ $po->po_number }}</td>
                            <td>{{ $po->item->name }}</td>
                            <td class="text-center">{{ $po->target_quantity }} {{ $po->item->unit }}</td>
                            <td class="text-center">
                                @php
                                    $badge = match($po->status) {
                                        'planned' => 'bg-secondary',
                                        'in_progress' => 'bg-primary',
                                        'completed' => 'bg-success',
                                        'cancelled' => 'bg-danger',
                                        default => 'bg-dark'
                                    };
                                @endphp
                                <span class="badge {{ $badge }}">{{ strtoupper(str_replace('_', ' ', $po->status)) }}</span>
                            </td>
                            <td>{{ $po->created_at->format('d M Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('production-orders.show', $po) }}" class="btn btn-sm btn-outline-primary">Manage Production</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No production orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection