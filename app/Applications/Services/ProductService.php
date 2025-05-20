<?php

namespace App\Applications\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Exception;

class ProductService
{
    public function getAllProducts($categoryId = null)
    {
        $query = Product::with('category');
        
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        
        return $query->get();
    }

    public function getProductById($id)
    {
        return Product::with('category')->findOrFail($id);
    }

    public function createProduct(array $data, $userId)
    {
        try {
            Log::info('ProductService@createProduct: Starting to create product', ['user_id' => $userId]);
            
            $imagePath = null;
            
            if (isset($data['image'])) {
                Log::info('ProductService@createProduct: Processing image upload');
                try {
                    $imagePath = $data['image']->store('products', 'public');
                    Log::info('ProductService@createProduct: Image uploaded successfully', ['path' => $imagePath]);
                } catch (Exception $e) {
                    Log::error('ProductService@createProduct: Image upload failed', [
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }
            }

            Log::info('ProductService@createProduct: Creating product record', [
                'name' => $data['name'],
                'category_id' => $data['category_id'],
                'user_id' => $userId
            ]);

            $product = Product::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price'],
                'image_path' => $imagePath,
                'category_id' => $data['category_id'],
                'user_id' => $userId,
            ]);

            Log::info('ProductService@createProduct: Product created successfully', ['product_id' => $product->id]);
            
            return $product;
        } catch (Exception $e) {
            Log::error('ProductService@createProduct: Failed to create product', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function updateProduct($id, array $data)
    {
        $product = Product::findOrFail($id);
        
        $updateData = [
            'name' => $data['name'] ?? $product->name,
            'description' => $data['description'] ?? $product->description,
            'price' => $data['price'] ?? $product->price,
            'category_id' => $data['category_id'] ?? $product->category_id,
        ];

        if (isset($data['image'])) {
            $updateData['image_path'] = $data['image']->store('products', 'public');
        }

        $product->update($updateData);
        
        return $product;
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
    }
} 