<?php

namespace App\Http\Controllers;

use App\Applications\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->middleware('auth:api');
        $this->productService = $productService;
    }

    // Helper method to check if user is admin
    protected function checkAdminRole()
    {
        $user = Auth::user();
        if (!$user) {
            Log::error('checkAdminRole: No authenticated user found');
            abort(401, 'You must be logged in to perform this action');
        }
        
        if ($user->role !== 'admin') {
            Log::error('Unauthorized access attempt - not admin', ['user_id' => $user->id, 'user_role' => $user->role]);
            abort(403, 'You must be an admin to perform this action');
        }
        return true;
    }

    public function index(Request $request)
    {
        // Add debug code for auth check
        Log::info('ProductController@index: Request headers', [
            'authorization' => $request->header('Authorization'),
            'accept' => $request->header('Accept'),
            'content-type' => $request->header('Content-Type')
        ]);
        
        Log::info('ProductController@index: Auth check', [
            'is_authenticated' => Auth::check(),
            'user' => Auth::user() ? Auth::id() : 'No user'
        ]);
        
        $categoryId = $request->query('category_id');
        $cacheKey = 'products' . ($categoryId ? "_cat{$categoryId}" : '');
        
        if ($categoryId) {
            // Validate that the category exists
            $request->validate([
                'category_id' => 'exists:categories,id'
            ]);
        }
        
        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($categoryId) {
            return $this->productService->getAllProducts($categoryId);
        });
    }

    public function store(Request $request)
    {
        // Add debug for authentication headers
        Log::info('ProductController@store: Request headers', [
            'authorization' => $request->header('Authorization'),
            'accept' => $request->header('Accept'),
            'content-type' => $request->header('Content-Type')
        ]);
        
        Log::info('ProductController@store: Auth check before try block', [
            'is_authenticated' => Auth::check(),
            'user' => Auth::user() ? Auth::id() : 'No user'
        ]);
        
        Log::info('ProductController@store: Method started');
        
        try {
            // Check admin role directly
            $this->checkAdminRole();
            Log::info('ProductController@store: Admin role check passed');
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'category_id' => 'required|exists:categories,id',
            ]);
            
            Log::info('ProductController@store: Validation passed, creating product');
            
            $product = $this->productService->createProduct($validated, Auth::id());
            
            Log::info('ProductController@store: Product created successfully', ['product_id' => $product->id]);
            
            return response()->json($product, 201);
        } catch (Exception $e) {
            Log::error('ProductController@store: Exception caught', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to create product',
                'message' => $e->getMessage(),
                'trace' => app()->environment('production') ? null : $e->getTrace()
            ], 500);
        }
    }

    public function show($id)
    {
        $product = $this->productService->getProductById($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        try {
            // Check admin role directly
            $this->checkAdminRole();
            
            $validated = $request->validate([
                'name' => 'string|max:255',
                'description' => 'string',
                'price' => 'numeric|min:0',
                'image' => 'image|mimes:jpeg,png,jpg|max:2048',
                'category_id' => 'exists:categories,id',
            ]);

            $product = $this->productService->updateProduct($id, $validated);
            return response()->json($product);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to update product',
                'message' => $e->getMessage(),
                'trace' => app()->environment('production') ? null : $e->getTrace()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Check admin role directly
            $this->checkAdminRole();
            
            $this->productService->deleteProduct($id);
            return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to delete product',
                'message' => $e->getMessage(),
                'trace' => app()->environment('production') ? null : $e->getTrace()
            ], 500);
        }
    }
}