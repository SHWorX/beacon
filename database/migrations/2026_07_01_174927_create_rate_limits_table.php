<?php

use App\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        $this->schema()->create('rate_limits', function (Blueprint $table) {
            $table->string('key', 191)->primary()->unique();
            $table->integer('hits')->default(0);
            $table->timestamp('window_start');
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        $this->schema()->dropIfExists('rate_limits');
    }
};
