<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->unsignedBigInteger('product_id');
            // Dos modos: URL remota o archivo local almacenado
            $t->string('url')->nullable();   // ej: https://...
            $t->string('path')->nullable();  // ej: products/abc.jpg  (en disk 'public')
            $t->string('alt')->nullable();
            $t->unsignedSmallInteger('position')->default(0); // orden en la galería
            $t->timestamps();

            $t->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $t->index(['product_id','position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
