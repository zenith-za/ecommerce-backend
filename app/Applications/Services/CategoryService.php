<?php

namespace App\Applications\Services;

use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    public function getAllCategories()
    {
        return Category::all();
    }

    public function getCategoryById($id)
    {
        return Category::findOrFail($id);
    }

    public function createCategory(array $data)
    {
        return Category::create([
            'name' => $data['name'],
        ]);
    }

    public function updateCategory($id, array $data)
    {
        $category = Category::findOrFail($id);
        $category->update([
            'name' => $data['name'],
        ]);

        return $category;
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        if ($category->products()->count() > 0) {
            throw new \Exception('Cannot delete category with associated products.');
        }
        $category->delete();
    }
}