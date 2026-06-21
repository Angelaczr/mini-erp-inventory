@php $item = $item ?? null; @endphp

<div class="mb-3">
    <label class="form-label">SKU</label>
    <input type="text" name="sku" class="form-control" value="{{ old('sku', $item->sku ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $item->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Category</label>
    <select name="category_id" class="form-select" required>
        <option value="">-- Select Category --</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}"
                @selected(old('category_id', $item->category_id ?? '') == $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Unit</label>
        <input type="text" name="unit" class="form-control" value="{{ old('unit', $item->unit ?? 'pcs') }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Reorder Level</label>
        <input type="number" name="reorder_level" class="form-control" min="0"
               value="{{ old('reorder_level', $item->reorder_level ?? 0) }}" required>
    </div>
</div>
