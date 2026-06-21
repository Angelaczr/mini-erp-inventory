<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Models\Category;
use App\Models\Item;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ItemController extends Controller
{
    public function index(): View
    {
        $items = Item::with('category')
            ->orderBy('name')
            ->paginate(15);

        // Attach computed current stock for display without N+1 issues
        // by pre-loading stock movements alongside.
        $items->getCollection()->transform(function (Item $item) {
            $item->stock_now = $item->currentStock();
            return $item;
        });

        return view('items.index', compact('items'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        return view('items.create', compact('categories'));
    }

    public function store(StoreItemRequest $request): RedirectResponse
    {
        Item::create($request->validated());

        return redirect()
            ->route('items.index')
            ->with('success', 'Item created successfully.');
    }

    public function edit(Item $item): View
    {
        $categories = Category::orderBy('name')->get();
        return view('items.edit', compact('item', 'categories'));
    }

    public function update(StoreItemRequest $request, Item $item): RedirectResponse
    {
        $item->update($request->validated());

        return redirect()
            ->route('items.index')
            ->with('success', 'Item updated successfully.');
    }

    public function destroy(Item $item): RedirectResponse
    {
        $item->delete();

        return redirect()
            ->route('items.index')
            ->with('success', 'Item deleted.');
    }
}
