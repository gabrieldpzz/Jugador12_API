<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/products",
     *   tags={"Productos"},
     *   summary="Listar productos",
     *   description="Devuelve un listado limitado de productos con su galería de imágenes.",
     *   @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Máximo de productos a devolver (por defecto 6)",
     *     required=false,
     *     @OA\Schema(type="integer", default=6, minimum=1, maximum=100)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product"))
     *   )
     * )
     */
    public function index(Request $r)
    {
        $limit = (int) $r->query('limit', 6);

        // Eager loading de imágenes; selecciona solo columnas necesarias
        $products = \App\Models\Product::query()
            ->with(['images' => fn($q) => $q->orderBy('position')])
            ->orderBy('id')
            ->limit($limit)
            ->get(['id','name','price','team','category','description'])
            ->map(function ($p) {
                $imgs = $p->images->map(fn($img) => [
                    'id'  => $img->id,
                    'src' => $img->src,     // calculado en accessor del modelo ProductImage
                    'alt' => $img->alt,
                ])->values();

                return [
                    'id'          => $p->id,
                    'name'        => $p->name,
                    'price'       => (float) $p->price,
                    'team'        => $p->team,
                    'category'    => $p->category,
                    'description' => $p->description,
                    // SIN image_url: el cliente toma la primera imagen como thumbnail
                    'images'      => $imgs,
                ];
            })
            ->values();

        return response()->json($products);
    }

    /**
     * @OA\Get(
     *   path="/api/products/{id}/sizes",
     *   tags={"Productos"},
     *   summary="Tallas de un producto",
     *   description="Lista de tallas (adulto/niño) con stock.",
     *   @OA\Parameter(
     *     name="id", in="path", required=true,
     *     description="ID del producto",
     *     @OA\Schema(type="integer", example=1)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="product_id", type="integer", example=1),
     *       @OA\Property(
     *         property="sizes",
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/SizeOption")
     *       )
     *     )
     *   ),
     *   @OA\Response(response=404, description="Producto no encontrado")
     * )
     */
    public function sizes(int $id)
    {
        $product = \App\Models\Product::findOrFail($id);

        $sizes = $product->sizes()->orderBy('order')->get()->map(function ($s) {
            return [
                'id'    => $s->id,
                'type'  => $s->type,
                'label' => $s->label,
                'stock' => (int) ($s->pivot->stock ?? 0),
            ];
        })->values();

        return response()->json([
            'product_id' => $product->id,
            'sizes'      => $sizes,
        ]);
    }

    /**
     * @OA\Get(
     *   path="/api/products/{id}",
     *   tags={"Productos"},
     *   summary="Detalle de producto",
     *   description="Incluye tallas disponibles e imágenes en galería.",
     *   @OA\Parameter(
     *     name="id", in="path", required=true,
     *     description="ID del producto",
     *     @OA\Schema(type="integer", example=1)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(ref="#/components/schemas/ProductWithSizes")
     *   ),
     *   @OA\Response(response=404, description="Producto no encontrado")
     * )
     */
    public function show(int $id)
    {
        // Cache corto para aliviar la carga cuando se abre muchas veces el mismo detalle
        $payload = Cache::remember("product.show.$id", 30, function () use ($id) {
            $p = \App\Models\Product::with([
                    'sizes'  => fn($q) => $q->orderBy('order'),
                    'images' => fn($q) => $q->orderBy('position'),
                ])
                ->findOrFail($id);

            $sizes = $p->sizes->map(fn($s) => [
                'id'    => $s->id,
                'type'  => $s->type,
                'label' => $s->label,
                'stock' => (int) ($s->pivot->stock ?? 0),
            ])->values();

            $images = $p->images->map(fn($img) => [
                'id'  => $img->id,
                'src' => $img->src,
                'alt' => $img->alt,
            ])->values();

            return [
                'id'          => $p->id,
                'name'        => $p->name,
                'images'      => $images,             // SIN image_url
                'price'       => (float) $p->price,
                'team'        => $p->team,
                'category'    => $p->category,
                'description' => $p->description,
                'sizes'       => $sizes,
            ];
        });

        return response()->json($payload);
    }
}
