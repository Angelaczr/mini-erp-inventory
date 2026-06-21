@extends('layouts.app')

@section('title', 'Items')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Items</h1>
        <a href="{{ route('items.create') }}" class="btn btn-primary">+ Add Item</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered bg-white">
            <thead class="table-light">
                <tr>
                    <th>SKU</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Unit</th>
                    <th class="text-center">Current Stock</th>
                    <th class="text-center">Reorder Level</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr class="{{ $item->stock_now <= $item->reorder_level ? 'table-warning' : '' }}">
                        <td>{{ $item->sku }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->category->name }}</td>
                        <td>{{ $item->unit }}</td>
                        <td class="text-center">{{ $item->stock_now }}</td>
                        <td class="text-center">{{ $item->reorder_level }}</td>
                        {{-- <td class="text-end">
                            <a href="{{ route('items.edit', $item) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form action="{{ route('items.destroy', $item) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete this item?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td> --}}
                        <td class="text-end">
                            <a href="{{ route('items.edit', $item) }}" class="btn btn-sm btn-outline-secondary">Edit</a>

                            <form id="delete-form-{{ $item->id }}" action="{{ route('items.destroy', $item) }}"
                                method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>

                            <button type="button" class="btn btn-sm btn-outline-danger"
                                onclick="if(confirm('Yakin ingin menghapus item ini?')) { document.getElementById('delete-form-{{ $item->id }}').submit(); }">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No items yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $items->links() }}
@endsection
