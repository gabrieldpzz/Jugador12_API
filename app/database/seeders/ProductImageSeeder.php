<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductImageSeeder extends Seeder
{
    public function run(): void
    {
        // Limpio rápido (usa con cuidado en prod)
        DB::table('product_images')->truncate();

        // Un par por producto, mezclando URL remota y “local” (simulada)
        $now = now();
        $rows = [
            // p1
            ['product_id'=>1,'url'=>'https://www.camisetafutboles.com/images/camisetafutbolbaratas/Camiseta-Real-Madrid-2%C2%AA-Equipaci%C3%B3n-24-25-Nbarao_01.jpg','position'=>0,'created_at'=>$now,'updated_at'=>$now],
            ['product_id'=>1,'url'=>'https://www.camisetafutboles.com/images/camisetafutbolbaratas/Camiseta-Real-Madrid-2%C2%AA-Equipaci%C3%B3n-24-25-Nbarao_03.jpg','position'=>1,'created_at'=>$now,'updated_at'=>$now],
            // p2
            ['product_id'=>2,'url'=>'https://www.camisetafutboles.com/images/camisetafutbolbaratas/Camiseta-B-arcelona-Fc-Segunda-Equipaci%C3%B3n-24-25-futbol-camisetas-baratas_1.jpg','position'=>0,'created_at'=>$now,'updated_at'=>$now],
            // p3
            ['product_id'=>3,'url'=>'https://picsum.photos/seed/arg1/1200/1200','position'=>0,'created_at'=>$now,'updated_at'=>$now],
            // p4
            ['product_id'=>4,'url'=>'https://picsum.photos/seed/milan1/1200/1200','position'=>0,'created_at'=>$now,'updated_at'=>$now],
            // p5
            ['product_id'=>5,'url'=>'https://picsum.photos/seed/liv1/1200/1200','position'=>0,'created_at'=>$now,'updated_at'=>$now],
        ];
        DB::table('product_images')->insert($rows);
    }
}
