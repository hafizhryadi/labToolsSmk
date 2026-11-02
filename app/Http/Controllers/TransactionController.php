<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $transactions = Transaction::query()
            ->with(['user', 'details.item'])
            ->latest('transaction_date')
            ->paginate(15)
            ->withQueryString();

        return $request->expectsJson()
            ? response()->json($transactions)
            : inertia('transactions/index', ['transactions' => $transactions]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $users = User::query()->orderBy('name')->get(['id', 'name']);

        return $request->expectsJson()
            ? response()->json(['users' => $users])
            : inertia('transactions/create', ['users' => $users]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): Response
    {
        $transaction = Transaction::create($request->validate([
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')->whereNull('deleted_at')],
            'transaction_date' => ['required', 'date'],
            'return_date' => ['nullable', 'date', 'after_or_equal:transaction_date'],
            'actual_return_date' => ['nullable', 'date', 'after_or_equal:transaction_date'],
            'status' => ['required', Rule::in(['dipinjam', 'dikembalikan', 'terlambat'])],
        ]));

        return $request->expectsJson()
            ? response()->json($transaction->load('user'), 201)
            : to_route('transactions.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Transaction $transaction)
    {
        $transaction->load(['user', 'details.item']);

        return $request->expectsJson()
            ? response()->json($transaction)
            : inertia('transactions/show', ['transaction' => $transaction]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Transaction $transaction)
    {
        $users = User::query()->orderBy('name')->get(['id', 'name']);

        return $request->expectsJson()
            ? response()->json(['transaction' => $transaction, 'users' => $users])
            : inertia('transactions/edit', ['transaction' => $transaction, 'users' => $users]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction): Response
    {
        $transaction->update($request->validate([
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')->whereNull('deleted_at')],
            'transaction_date' => ['required', 'date'],
            'return_date' => ['nullable', 'date', 'after_or_equal:transaction_date'],
            'actual_return_date' => ['nullable', 'date', 'after_or_equal:transaction_date'],
            'status' => ['required', Rule::in(['dipinjam', 'dikembalikan', 'terlambat'])],
        ]));

        return $request->expectsJson()
            ? response()->json($transaction->fresh()->load('user'))
            : to_route('transactions.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Transaction $transaction): Response
    {
        $transaction->delete();

        return $request->expectsJson()
            ? response()->json(status: 204)
            : to_route('transactions.index');
    }
}
