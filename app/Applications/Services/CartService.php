<?php

namespace App\Applications\Services;

use App\Models\Cart;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function getCartItems($userId)
    {
        return Cart::where('user_id', $userId)
            ->with('product')
            ->get();
    }

    public function addToCart($userId, array $data)
    {
        $existing = Cart::where('user_id', $userId)
            ->where('product_id', $data['product_id'])
            ->first();

        if ($existing) {
            $existing->quantity += $data['quantity'];
            $existing->save();
            return $existing;
        }

        return Cart::create([
            'user_id' => $userId,
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
        ]);
    }

    public function updateCartItem($cartId, $quantity, $userId)
    {
        $cartItem = Cart::where('id', $cartId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $cartItem->quantity = $quantity;
        $cartItem->save();

        return $cartItem;
    }

    public function removeFromCart($cartId, $userId)
    {
        $cartItem = Cart::where('id', $cartId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $cartItem->delete();
    }

    public function getCartCount($userId)
    {
        return Cart::where('user_id', $userId)->sum('quantity');
    }
}