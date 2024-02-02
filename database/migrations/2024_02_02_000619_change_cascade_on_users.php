<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table("users", function (Blueprint $table) {
            $table->dropForeign("users_manager_id_foreign");
            $table->foreign("manager_id")
                ->references('id')
                ->on('managers')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("users", function (Blueprint $table) {
            $table->dropForeign("users_manager_id_foreign");
            $table->foreign("manager_id")
                ->references('id')
                ->on('managers');
        });
    }
};
