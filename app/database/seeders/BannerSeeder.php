<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Banner;

class BannerSeeder extends Seeder {
    public function run(): void {
        Banner::query()->delete();
        Banner::create([
            'title' => 'Cashback 20%',
            'subtitle' => 'A Summer Surprise',
            'image_url' => 'https://i.pinimg.com/736x/6d/ff/7d/6dff7dae2e81c5ab34a636ce9c6624a1.jpg',
            'order' => 1,
            'active' => true,
        ]);
        Banner::create([
            'title' => 'Nueva colección',
            'subtitle' => 'Temporada 24/25',
            'image_url' => 'https://universidadeuropea.com/resources/media/images/real-madrid-home-banner-hero-1440x.2e16d0ba.fill-767x384.jpg',
            'order' => 2,
            'active' => true,
        ]);
    }
}
