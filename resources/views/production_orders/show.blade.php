@extends('layouts.app')

@section('title', 'Manage PO: ' . $po->po_number)

@section('content')
    <div class="mb-3">
        <a href="{{ route('production-orders.index') }}" class="text-decoration-none">&larr; Back to PO List</a>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ $po->po_number }}</h1>
            <p class="text-muted mb-0">Target: <strong>{{ $po->target_quantity ?? 0 }} {{ $po->item->unit ?? "" }}</strong> of {{ $po->item->name ?? "" }}</p>
        </div>
        <div>
            <span class="badge bg-primary fs-6">Status: {{ strtoupper(str_replace('_', ' ', $po->status)) }}</span>
        </div>
    </div>

    <h2 class="h5 mb-3">Live Production Tracking</h2>
    <div class="row mb-4 g-3">
        @php
            // Asumsi variabel $stageStock dikirim dari controller berisi kalkulasi saat ini per stage
            $stages = [
                'potongan' => ['label' => '1. Potongan (Cutting)', 'color' => 'secondary'],
                'jahit' => ['label' => '2. Jahit (Sewing)', 'color' => 'info'],
                'pewarnaan' => ['label' => '3. Pewarnaan (Dyeing)', 'color' => 'warning'],
                'finishing' => ['label' => '4. Finishing', 'color' => 'success']
            ];
        @endphp

        @foreach($stages as $key => $stage)
            <div class="col-md-3">
                <div class="card h-100 border-{{ $stage['color'] }}">
                    <div class="card-header bg-{{ $stage['color'] }} text-white fw-bold">
                        {{ $stage['label'] }}
                    </div>
                    <div class="card-body text-center d-flex flex-column justify-content-center">
                        <h3 class="display-6 mb-0">{{ $stageStock[$key] ?? 0 }}</h3>
                        <small class="text-muted">In Progress</small>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if(($stageStock['bikin_bagus'] ?? 0) > 0)
        <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
            <div class="me-3">
                <h4 class="mb-0">⚠️ Repair Log Alert!</h4>
            </div>
            <div>
                Terdapat <strong>{{ $stageStock['bikin_bagus'] }} {{ $po->item->unit }}</strong> yang teridentifikasi cacat/reject dan saat ini sedang berada dalam antrean <strong>Bikin Bagus</strong> untuk diperbaiki sebelum dikembalikan ke alur produksi.
            </div>
        </div>
    @endif

    <hr class="my-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 mb-0">Record Stage Transition</h2>
        <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#transitionModal">
            Move Stock &rarr;
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
             <p class="text-muted mb-0">Tabel riwayat pergerakan stok khusus untuk PO ini (hanya menampilkan log IN/OUT yang terikat dengan po_id ini) akan ditampilkan di sini.</p>
        </div>
    </div>
@endsection