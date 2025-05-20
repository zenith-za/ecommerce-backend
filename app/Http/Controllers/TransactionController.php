<?php

namespace App\Http\Controllers;

use App\Applications\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->middleware('auth:api');
        $this->transactionService = $transactionService;
    }

    public function store(Request $request)
    {
        try {
            $transaction = $this->transactionService->createTransaction(Auth::id());
            return response()->json($transaction, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function index()
    {
        $transactions = $this->transactionService->getUserTransactions(Auth::id());
        return response()->json($transactions);
    }
}