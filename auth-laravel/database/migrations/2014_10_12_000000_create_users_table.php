<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Str::uuid()
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string("cpf", "20")->nullable();
            $table->string("rg", "20")->nullable();
            $table->string("address", "200")->nullable();
            $table->string("addressNumber", "10")->nullable();
            $table->string("telephone", "20")->nullable();
            $table->string("postalCode", "20")->nullable();
            $table->double("salary")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
