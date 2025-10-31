<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_sizes', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->unsignedBigInteger('product_id');
            $t->unsignedBigInteger('size_id');
            $t->unsignedInteger('stock')->default(0);
            $t->timestamps();

            $t->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $t->foreign('size_id')->references('id')->on('sizes')->onDelete('restrict');
            $t->unique(['product_id', 'size_id']);
            $t->index(['size_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_sizes');
    }
};
