<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collection_meta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id') // foreign key
                  ->constrained('collections') // reference collections table
                  ->onDelete('cascade'); // agar collection delete ho jaye toh meta bhi delete
            $table->string('meta_title')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_meta');
    }
};
