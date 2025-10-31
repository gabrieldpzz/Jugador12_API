<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    protected $fillable = ['product_id','url','path','alt','position'];

    protected $appends = ['src'];

    // Devuelve la URL final (si path => storage/public, si url => remota)
    public function getSrcAttribute(): ?string
    {
        if (!empty($this->attributes['url'])) {
            return $this->attributes['url'];
        }
        if (!empty($this->attributes['path'])) {
            return Storage::disk('public')->url($this->attributes['path']);
        }
        return null;
    }
}
