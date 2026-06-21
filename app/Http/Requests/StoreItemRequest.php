<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $itemId = $this->route('item')?->id;

        return [
            'sku' => ['required', 'string', 'max:30', Rule::unique('items', 'sku')->ignore($itemId)],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'unit' => ['required', 'string', 'max:20'],
            'reorder_level' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'sku.unique' => 'SKU is already in use by another item.',
            'category_id.exists' => 'Selected category does not exist.',
        ];
    }
}
