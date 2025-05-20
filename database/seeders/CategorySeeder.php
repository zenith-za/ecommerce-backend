<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Laptop',
            'Smartwatches',
            'Earphones',
            'Smartphone',
            'Console'
        ];
        
        foreach ($categories as $categoryName) {
            // Check if the category already exists to avoid duplicates
            if (!Category::where('name', $categoryName)->exists()) {
                Category::create(['name' => $categoryName]);
            }
        }
    }
}
