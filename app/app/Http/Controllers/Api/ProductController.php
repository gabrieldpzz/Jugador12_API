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

        $products = Product::query()
            ->orderBy('id')
            ->limit($limit)
            ->get(['id','name','price','image_url','team','category','description']);

        // Fuerza respuesta JSON y cabecera correcta
        return response()->json($products, 200, [
            'Content-Type' => 'application/json; charset=utf-8'
        ], JSON_UNESCAPED_UNICODE);
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
    $data = Cache::remember("product:{$id}:show", 30, function () use ($id) {
        $p = Product::with(['sizes' => fn($q) => $q->orderBy('order')])->findOrFail($id);

        $sizes = $p->sizes->map(function ($s) {
            return [
                'id'    => $s->id,
                'type'  => $s->type,
                'label' => $s->label,
                'stock' => (int)($s->pivot->stock ?? 0),
            ];
        })->values();

        return [
            'id'          => $p->id,
            'name'        => $p->name,
            'image_url'   => $p->image_url,
            'price'       => (float)$p->price,
            'team'        => $p->team,
            'category'    => $p->category,
            'description' => $p->description,
            'sizes'       => $sizes,
        ];
    });

    return response()->json($data);
}



}
