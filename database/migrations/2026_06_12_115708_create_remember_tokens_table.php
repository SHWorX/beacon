<?php

use App\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        $this->schema()->create('remember_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('token_hash');
            $table->timestamp('expires_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        $this->schema()->dropIfExists('remember_tokens');
    }
};
