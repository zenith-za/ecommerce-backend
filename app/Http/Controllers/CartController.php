<?php

namespace App\Http\Controllers;

use App\Applications\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use App\Models\Cart;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->middleware('auth:api');
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cartItems = $this->cartService->getCartItems(Auth::id());
        return response()->json($cartItems);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = $this->cartService->addToCart(Auth::id(), $validated);
        return response()->json($cartItem, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = $this->cartService->updateCartItem($id, $validated['quantity'], Auth::id());
        return response()->json($cartItem);
    }

    public function destroy($id)
    {
        $this->cartService->removeFromCart($id, Auth::id());
        return response()->json(null, 204);
    }

    public function getCount(Request $request)
    {
        $userId = auth()->id();
        
        // Return 0 if not authenticated instead of error
        if (!$userId) {
            return response()->json(['count' => 0]);
        }
        
        $count = Cart::where('user_id', $userId)->sum('quantity');
        return response()->json(['count' => $count]);
    }
}