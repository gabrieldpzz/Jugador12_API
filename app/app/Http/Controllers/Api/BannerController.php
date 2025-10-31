<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;

class BannerController extends Controller
{
    public function index(Request $r) {
        $limit = (int)($r->get('limit', 2));
        return Banner::query()
            ->where('active', true)
            ->orderBy('order')
            ->limit($limit)
            ->get(['id','title','subtitle','image_url','order']);
    }
}
