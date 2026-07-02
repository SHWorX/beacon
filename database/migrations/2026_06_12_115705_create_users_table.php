<?php

use App\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        $this->schema()->create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('email_verification_expires_at')->nullable();
            $table->string('email_verification_token', 64)->nullable();
            $table->char('totp_secret', 32)->nullable();
            $table->timestamp('totp_enabled_at')->nullable();
            $table->json('totp_recovery_codes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        $this->schema()->dropIfExists('users');
    }
};
