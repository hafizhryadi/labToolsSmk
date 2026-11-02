<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $items = Item::query()
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return $request->expectsJson()
            ? response()->json($items)
            : inertia('items/index', ['items' => $items]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return $request->expectsJson()
            ? response()->json(['item' => null])
            : inertia('items/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): Response
    {
        $item = Item::create($request->validate([
            'item_code' => ['required', 'string', 'max:255', 'unique:items,item_code'],
            'item_name' => ['required', 'string', 'max:255'],
            'item_type' => ['required', Rule::in(['alat', 'bahan'])],
            'item_stock' => ['required', 'integer', 'min:0'],
            'item_unit' => ['required', 'string', 'max:255'],
        ]));

        return $request->expectsJson()
            ? response()->json($item, 201)
            : to_route('items.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Item $item)
    {
        return $request->expectsJson()
            ? response()->json($item)
            : inertia('items/show', ['item' => $item]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Item $item)
    {
        return $request->expectsJson()
            ? response()->json($item)
            : inertia('items/edit', ['item' => $item]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item): Response
    {
        $item->update($request->validate([
            'item_code' => ['required', 'string', 'max:255', Rule::unique('items', 'item_code')->ignore($item)],
            'item_name' => ['required', 'string', 'max:255'],
            'item_type' => ['required', Rule::in(['alat', 'bahan'])],
            'item_stock' => ['required', 'integer', 'min:0'],
            'item_unit' => ['required', 'string', 'max:255'],
        ]));

        return $request->expectsJson()
            ? response()->json($item->fresh())
            : to_route('items.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Item $item): Response
    {
        $item->delete();

        return $request->expectsJson()
            ? response()->json(status: 204)
            : to_route('items.index');
    }
}
