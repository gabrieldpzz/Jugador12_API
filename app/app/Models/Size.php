<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $fillable = ['type','label','order','chest_min_cm','chest_max_cm'];
}
