<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add user_id to categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('uuid');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        // Add user_id to collections table
        Schema::table('collections', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('uuid');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['created_by']);
        });

        Schema::table('collections', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['created_by']);
        });
    }
};
