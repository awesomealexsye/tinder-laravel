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
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('liker_id')->constrained('users')->onDelete('cascade')->comment('User who liked');
            $table->foreignId('liked_id')->constrained('users')->onDelete('cascade')->comment('User who was liked');
            $table->timestamp('created_at')->nullable();

            // Unique constraint to prevent duplicate likes
            $table->unique(['liker_id', 'liked_id'], 'unique_like');

            // Indexes
            $table->index('liker_id');
            $table->index('liked_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
