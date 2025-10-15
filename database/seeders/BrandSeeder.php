<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Apple',
                'description' => 'Technology company specializing in consumer electronics.',
                'website' => 'https://www.apple.com',
                'logo' => null,
                'established_year' => 1976,
                'status' => 'active',
                'featured' => true,
            ],
            [
                'name' => 'Samsung',
                'description' => 'Global leader in electronics and appliances.',
                'website' => 'https://www.samsung.com',
                'logo' => null,
                'established_year' => 1938,
                'status' => 'active',
                'featured' => false,
            ],
            [
                'name' => 'Nike',
                'description' => 'Leading sportswear and athletic footwear brand.',
                'website' => 'https://www.nike.com',
                'logo' => null,
                'established_year' => 1964,
                'status' => 'active',
                'featured' => true,
            ],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
