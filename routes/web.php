<?php

use App\Http\Controllers\AprioriRuleController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionDetailController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
    Route::resource('items', ItemController::class);
    Route::resource('transactions', TransactionController::class);
    Route::resource('transaction-details', TransactionDetailController::class)
        ->parameters(['transaction-details' => 'transactionDetail']);
    Route::resource('apriori-rules', AprioriRuleController::class)
        ->parameters(['apriori-rules' => 'aprioriRule']);
});

require __DIR__.'/settings.php';
