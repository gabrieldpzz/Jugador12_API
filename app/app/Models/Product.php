<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name','team','category','price','image_url','description'];

    protected $casts = [
        'price' => 'float', // o 'double'
    ];

    public function sizes()
{
    return $this->belongsToMany(Size::class, 'product_sizes')
        ->withPivot(['stock'])
        ->withTimestamps()
        ->orderBy('order');
}

}