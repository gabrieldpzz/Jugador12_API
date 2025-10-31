<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SizeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('sizes')->truncate();

        $now = now();

        $sizes = [
            // Adulto
            ['type'=>'adult','label'=>'XS','order'=>10],
            ['type'=>'adult','label'=>'S', 'order'=>20],
            ['type'=>'adult','label'=>'M', 'order'=>30],
            ['type'=>'adult','label'=>'L', 'order'=>40],
            ['type'=>'adult','label'=>'XL','order'=>50],
            ['type'=>'adult','label'=>'XXL','order'=>60],
            // Niño
            ['type'=>'kid','label'=>'6',  'order'=>110],
            ['type'=>'kid','label'=>'8',  'order'=>120],
            ['type'=>'kid','label'=>'10', 'order'=>130],
            ['type'=>'kid','label'=>'12', 'order'=>140],
            ['type'=>'kid','label'=>'14', 'order'=>150],
        ];

        foreach ($sizes as $s) {
            DB::table('sizes')->insert([
                'type' => $s['type'],
                'label' => $s['label'],
                'order' => $s['order'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
