<?php

namespace App\Http\Controllers;

use App\Applications\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->middleware('auth:api');
        $this->categoryService = $categoryService;
    }
    
    protected function checkAdminRole()
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            Log::error('Unauthorized access attempt - not admin', ['user_id' => $user->id, 'user_role' => $user->role]);
            abort(403, 'You must be an admin to perform this action');
        }
        return true;
    }

    public function index()
    {
        $categories = $this->categoryService->getAllCategories();
        return response()->json($categories);
    }

    public function show($id)
    {
        $category = $this->categoryService->getCategoryById($id);
        return response()->json($category);
    }

    public function store(Request $request)
    {
        try {
            $this->checkAdminRole();
            
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories',
            ]);

            $category = $this->categoryService->createCategory($validated);
            return response()->json($category, 201);
        } catch (Exception $e) {
            Log::error('CategoryController@store: Exception caught', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to create category',
                'message' => $e->getMessage(),
                'trace' => app()->environment('production') ? null : $e->getTrace()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $this->checkAdminRole();
            
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $id,
            ]);

            $category = $this->categoryService->updateCategory($id, $validated);
            return response()->json($category);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to update category',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->checkAdminRole();
            
            $this->categoryService->deleteCategory($id);
            return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}