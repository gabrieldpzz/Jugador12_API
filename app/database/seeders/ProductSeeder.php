<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('products')->delete();

        DB::table('products')->insert([
            ['id'=>1,'name'=>'Camiseta Local Real Madrid 24/25','team'=>'Real Madrid','category'=>'Actual','price'=>89.99,'image_url'=>'https://sportpalace.es/wp-content/uploads/2024/05/2548814c-scaled.jpeg','description'=>'Camiseta oficial de local, Real Madrid 2024-2025','created_at'=>now(),'updated_at'=>now()],
            ['id'=>2,'name'=>'Camiseta Visitante FC Barcelona 24/25','team'=>'FC Barcelona','category'=>'Actual','price'=>89.99,'image_url'=>'https://kmisetashd.es/12272-large_default/barcelona-visitante-2425.jpg','description'=>'Camiseta oficial de visita, FC Barcelona 2024-2025','created_at'=>now(),'updated_at'=>now()],
            ['id'=>3,'name'=>'Camiseta Argentina 1986 (Maradona)','team'=>'Selección Argentina','category'=>'Retro','price'=>95.00,'image_url'=>'https://camisetardas.cl/wp-content/uploads/2024/03/402e69ed.jpg','description'=>'Réplica de la camiseta de Argentina, Mundial 1986','created_at'=>now(),'updated_at'=>now()],
            ['id'=>4,'name'=>'Camiseta AC Milan 1994 (Final Atenas)','team'=>'AC Milan','category'=>'Retro','price'=>92.00,'image_url'=>'https://acdn-us.mitiendanube.com/stores/002/872/034/products/1d919f371-3142a0f63adff94fad16810869003057-1024-1024.jpeg','description'=>'Camiseta retro del AC Milan, temporada 1993-1994','created_at'=>now(),'updated_at'=>now()],
            ['id'=>5,'name'=>'Sudadera Entrenamiento Liverpool FC 24/25','team'=>'Liverpool FC','category'=>'Entrenamiento','price'=>65.00,'image_url'=>'https://media.foot-store.es/catalog/product/cache/image/1800x/9df78eab33525d08d6e5fb8d27136e95/n/i/nike_fn9938-013-vpsrh001.jpg','description'=>'Sudadera de entrenamiento del Liverpool FC, 2024-2025','created_at'=>now(),'updated_at'=>now()],
        ]);
    }
}
