<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sizes', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->enum('type', ['adult', 'kid']);
            $t->string('label', 20);
            $t->unsignedSmallInteger('order')->default(0);
            $t->unsignedSmallInteger('chest_min_cm')->nullable();
            $t->unsignedSmallInteger('chest_max_cm')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sizes');
    }
};
