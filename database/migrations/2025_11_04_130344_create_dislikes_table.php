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
        Schema::create('dislikes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('User who disliked');
            $table->foreignId('disliked_id')->constrained('users')->onDelete('cascade')->comment('User who was disliked');
            $table->timestamp('created_at')->nullable();

            // Unique constraint to prevent duplicate dislikes
            $table->unique(['user_id', 'disliked_id'], 'unique_dislike');

            // Indexes
            $table->index('user_id');
            $table->index('disliked_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dislikes');
    }
};
