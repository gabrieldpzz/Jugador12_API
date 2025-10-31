<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS vector');

        Schema::create('products', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->string('name');
            $t->string('team')->nullable();
            $t->string('category')->nullable();
            $t->decimal('price', 10, 2);
            $t->text('image_url')->nullable();
            $t->text('description')->nullable();
            $t->timestamps();
        });

        DB::statement('ALTER TABLE products ADD COLUMN embedding vector(768)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_products_embedding_cos ON products USING ivfflat (embedding vector_cosine_ops) WITH (lists = 100)');
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
