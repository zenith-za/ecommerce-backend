<?php

namespace App\Applications\Services;

use App\Models\Transaction;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class TransactionService
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function createTransaction($userId)
    {
        $cartItems = $this->cartService->getCartItems($userId);

        if (empty($cartItems)) {
            throw new \Exception('Cart is empty.');
        }

        $totalAmount = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        $transaction = Transaction::create([
            'user_id' => $userId,
            'total_amount' => $totalAmount,
            'status' => 'completed',
            'cart_items' => $cartItems->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ];
            })->toArray(),
        ]);

        // Clear the cart after successful transaction
        Cart::where('user_id', $userId)->delete();

        return $transaction;
    }

    public function getUserTransactions($userId)
    {
        return Transaction::where('user_id', $userId)->get();
    }
}