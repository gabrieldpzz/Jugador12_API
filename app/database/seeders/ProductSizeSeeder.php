<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSizeSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $sizeIds = DB::table('sizes')->whereIn('label', ['S','M','L','XL'])->pluck('id')->all();

        foreach ([1,2,3,4,5] as $productId) {
            foreach ($sizeIds as $sid) {
                DB::table('product_sizes')->updateOrInsert(
                    ['product_id' => $productId, 'size_id' => $sid],
                    ['stock' => 10, 'updated_at' => $now, 'created_at' => $now]
                );
            }
        }
    }
}
