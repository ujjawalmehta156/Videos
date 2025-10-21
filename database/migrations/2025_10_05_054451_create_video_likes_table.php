<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_likes', function (Blueprint $table) {
            $table->id('like_id');
            $table->unsignedBigInteger('video_id');
            $table->foreign('video_id')->references('id')->on('collections')->onDelete('cascade');

            $table->string('device_fingerprint')->nullable();
            $table->string('ip_address')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_likes');
    }
};
