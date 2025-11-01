<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('email_otp_codes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('code_hash');      // bcrypt del código
            $t->timestamp('expires_at');
            $t->unsignedSmallInteger('attempts')->default(0);
            $t->timestamp('consumed_at')->nullable();
            $t->timestamps();

            $t->index(['user_id', 'expires_at']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('email_otp_codes');
    }
};
