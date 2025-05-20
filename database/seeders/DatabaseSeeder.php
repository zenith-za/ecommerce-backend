<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user if it doesn't exist
        if (!User::where('email', 'admin@ecommerce.com')->exists()) {
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@ecommerce.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'billing_address' => '123 Admin St, City',
            ]);
        }

        // Call the category seeder
        $this->call([
            CategorySeeder::class,
        ]);
    }
}
