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
        Schema::create('collections', function (Blueprint $table) {
            $table->id();

            // Main fields
            $table->string('title');
            $table->text('description')->nullable();

            // Who uploaded the file (reference to users table)
            $table->unsignedBigInteger('uploader_id');
            $table->foreign('uploader_id')->references('id')->on('users')->onDelete('cascade');

            // Category + Sub Category
            $table->unsignedBigInteger('cat_id');
            $table->foreign('cat_id')->references('id')->on('categories')->onDelete('cascade');

            $table->unsignedBigInteger('sub_cat_id')->nullable();
            $table->foreign('sub_cat_id')->references('id')->on('categories')->onDelete('cascade');

            // File related fields
            $table->string('file_format')->nullable();
            $table->string('hls_master_url')->nullable();
            $table->string('thumbnail_url')->nullable();

            // Status & Visibility
            $table->enum('status', ['processing', 'ready', 'failed', 'deleted'])->default('processing');
            $table->enum('visibility', ['public', 'private', 'unlisted'])->default('public');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
