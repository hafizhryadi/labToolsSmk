<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class TransactionDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $details = TransactionDetail::query()
            ->with(['transaction', 'item'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return $request->expectsJson()
            ? response()->json($details)
            : inertia('transaction-details/index', ['details' => $details]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $transactions = Transaction::query()->latest('transaction_date')->get();
        $items = Item::query()->orderBy('item_name')->get();

        return $request->expectsJson()
            ? response()->json(['transactions' => $transactions, 'items' => $items])
            : inertia('transaction-details/create', compact('transactions', 'items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): Response
    {
        $detail = TransactionDetail::create($request->validate([
            'transaction_id' => ['required', 'integer', Rule::exists('transactions', 'id')],
            'item_id' => ['required', 'integer', Rule::exists('items', 'id')->whereNull('deleted_at')],
            'quantity' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string'],
        ]));

        return $request->expectsJson()
            ? response()->json($detail->load(['transaction', 'item']), 201)
            : to_route('transaction-details.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, TransactionDetail $transactionDetail)
    {
        $transactionDetail->load(['transaction', 'item']);

        return $request->expectsJson()
            ? response()->json($transactionDetail)
            : inertia('transaction-details/show', ['detail' => $transactionDetail]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, TransactionDetail $transactionDetail)
    {
        $transactions = Transaction::query()->latest('transaction_date')->get();
        $items = Item::query()->orderBy('item_name')->get();

        return $request->expectsJson()
            ? response()->json(['detail' => $transactionDetail, 'transactions' => $transactions, 'items' => $items])
            : inertia('transaction-details/edit', compact('transactionDetail', 'transactions', 'items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransactionDetail $transactionDetail): Response
    {
        $transactionDetail->update($request->validate([
            'transaction_id' => ['required', 'integer', Rule::exists('transactions', 'id')],
            'item_id' => ['required', 'integer', Rule::exists('items', 'id')->whereNull('deleted_at')],
            'quantity' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string'],
        ]));

        return $request->expectsJson()
            ? response()->json($transactionDetail->fresh()->load(['transaction', 'item']))
            : to_route('transaction-details.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, TransactionDetail $transactionDetail): Response
    {
        $transactionDetail->delete();

        return $request->expectsJson()
            ? response()->json(status: 204)
            : to_route('transaction-details.index');
    }
}
