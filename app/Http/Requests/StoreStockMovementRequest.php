<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStockMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_id' => ['required', 'exists:items,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'type' => ['required', Rule::in(['in', 'out', 'adjustment'])],
            'production_stage' => ['nullable', Rule::in(['cutting', 'sewing', 'dyeing', 'finishing', 'n/a'])],
            'quantity' => ['required', 'integer', 'min:1'],
            'reference_no' => ['nullable', 'string', 'max:50'],
            'note' => ['nullable', 'string', 'max:1000'],
            'production_order_id' => ['nullable', 'exists:production_orders,id'],
        ];
    }

    /**
     * Custom validation: prevent OUT movements that would push stock below zero.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->input('type') === 'out') {
                $item = \App\Models\Item::find($this->input('item_id'));
                $warehouseId = $this->input('warehouse_id');
                $requestedQty = (int) $this->input('quantity');

                if ($item && $item->currentStock($warehouseId) < $requestedQty) {
                    $validator->errors()->add(
                        'quantity',
                        'Insufficient stock at this warehouse for the requested OUT quantity.'
                    );
                }
            }
        });
    }
}
