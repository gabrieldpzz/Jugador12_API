<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use OpenApi\Annotations as OA;

class BannerController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/banners",
     *   tags={"Banners"},
     *   summary="Listar banners activos",
     *   description="Devuelve los banners activos ordenados por `order`. Por defecto retorna 2.",
     *   @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Máximo de banners a devolver (por defecto 2)",
     *     required=false,
     *     @OA\Schema(type="integer", default=2, minimum=1, maximum=10)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Banner"))
     *   )
     * )
     */
    public function index(Request $r)
    {
        $limit = (int) $r->get('limit', 2);

        return Banner::query()
            ->where('active', true)
            ->orderBy('order')
            ->limit($limit)
            ->get(['id','title','subtitle','image_url','order']);
    }
}
