<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoStreamsTable extends Migration
{
    public function up(): void
    {
        Schema::create('video_streams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained('collections')->onDelete('cascade');
            $table->string('resolution');
            $table->integer('bitrate_kbps')->nullable();
            $table->string('codec')->nullable();
            $table->string('hls_url')->nullable();
            $table->decimal('file_size_mb', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_streams');
    }
}
