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
        Schema::create('managers', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Str::uuid()
            $table->string('name');
            $table->string('email')->unique();
            $table->integer("typeIdentificationNumber");
            $table->string("identificationNumber", "40");
            $table->string("address", "200");
            $table->string("addressNumber", "10");
            $table->string("telephone", "20");
            $table->string("postalCode", "20");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('managers');
    }
};
