@extends('layouts.app')

@section('title', 'Warehouses')

@section('content')
    <h1 class="h3 mb-4">Warehouses</h1>

    <div class="row">
        <div class="col-md-7">
            <table class="table table-bordered bg-white">
                <thead class="table-light">
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Location</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($warehouses as $warehouse)
                        <tr>
                            <td>{{ $warehouse->code }}</td>
                            <td>{{ $warehouse->name }}</td>
                            <td>{{ $warehouse->location }}</td>
                            {{-- <td class="text-end">
                                <form action="{{ route('warehouses.destroy', $warehouse) }}" method="POST"
                                      onsubmit="return confirm('Delete this warehouse?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td> --}}
                            <td class="text-end">
                                <form id="delete-form-{{ $warehouse->id }}"
                                    action="{{ route('warehouses.destroy', $warehouse) }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>

                                <button type="button" class="btn btn-sm btn-outline-danger"
                                    onclick="if(confirm('Yakin ingin menghapus data ini?')) { document.getElementById('delete-form-{{ $warehouse->id }}').submit(); }">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No warehouses yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <h2 class="h6 mb-3">Add Warehouse</h2>
                    <form action="{{ route('warehouses.store') }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label">Code</label>
                            <input type="text" name="code" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control">
                        </div>
                        <button class="btn btn-primary btn-sm">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
