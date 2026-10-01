<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Air & Sanitasi', 'Kelistrikan', 'AC & Pendingin', 'Furniture',
            'Bangunan', 'Kebersihan', 'Sound System', 'Lainnya',
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category]);
        }
    }
}
