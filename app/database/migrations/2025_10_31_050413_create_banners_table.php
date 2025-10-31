<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('banners', function (Blueprint $t) {
            $t->id();
            $t->string('title')->nullable();        // texto grande (p. ej. "Cashback 20%")
            $t->string('subtitle')->nullable();     // texto chico (p. ej. "A Summer Surprise")
            $t->string('image_url');                // URL de la imagen
            $t->unsignedTinyInteger('order')->default(1);  // 1 primero, 2 segundo...
            $t->boolean('active')->default(true);
            $t->timestamps();
            $t->index(['active', 'order']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('banners');
    }
};

