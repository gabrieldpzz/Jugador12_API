<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(Request $r) {
    $limit = (int) $r->query('limit', 6);

    $products = \App\Models\Product::query()
        ->with(['images' => fn($q) => $q->orderBy('position')])
        ->orderBy('id')
        ->limit($limit)
        ->get(['id','name','price','image_url','team','category','description'])
        ->map(function ($p) {
            // construye arreglo de imágenes (src, alt)
            $imgs = $p->images->map(fn($img) => [
                'id'  => $img->id,
                'src' => $img->src,
                'alt' => $img->alt,
            ])->values();
            return [
                'id'          => $p->id,
                'name'        => $p->name,
                'price'       => (float)$p->price,
                'team'        => $p->team,
                'category'    => $p->category,
                'description' => $p->description,
                // thumb: usa el primero de images o el antiguo image_url
                'image_url'   => $imgs->first()['src'] ?? $p->image_url,
                'images'      => $imgs,
            ];
        })
        ->values();

    return response()->json($products);
}

    public function sizes(int $id)
{
    $product = \App\Models\Product::findOrFail($id);

    $sizes = $product->sizes()->get()->map(function ($s) {
        return [
            'id' => $s->id,
            'type' => $s->type,
            'label' => $s->label,
            'stock' => (int)($s->pivot->stock ?? 0),
        ];
    });

    return response()->json([
        'product_id' => $product->id,
        'sizes' => $sizes->values(),
    ]);
}

   public function show(int $id)
{
    $p = \App\Models\Product::with(['sizes' => fn($q) => $q->orderBy('order'),
                                    'images' => fn($q) => $q->orderBy('position')])
         ->findOrFail($id);

    $sizes = $p->sizes->map(fn($s) => [
        'id'    => $s->id,
        'type'  => $s->type,
        'label' => $s->label,
        'stock' => (int)($s->pivot->stock ?? 0),
    ])->values();

    $images = $p->images->map(fn($img) => [
        'id'  => $img->id,
        'src' => $img->src,
        'alt' => $img->alt,
    ])->values();

    return response()->json([
        'id'          => $p->id,
        'name'        => $p->name,
        'image_url'   => $images->first()['src'] ?? $p->image_url,
        'images'      => $images,
        'price'       => (float)$p->price,
        'team'        => $p->team,
        'category'    => $p->category,
        'description' => $p->description,
        'sizes'       => $sizes,
    ]);
}



}
