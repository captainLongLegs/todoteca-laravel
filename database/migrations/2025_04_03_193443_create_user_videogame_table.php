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
        Schema::create('user_videogame', function (Blueprint $table) {;
            $table->foreignId('user_id')->constrained();
            $table->foreignId('videogame_id')->constrained();
            $table->string('status')->default('to-play'); // playing/completed/abandoned
            $table->integer('playtime_hours')->nullable();
            $table->integer('rating')->nullable(); // 1-5
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->primary(['user_id', 'videogame_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_videogame');
    }
};
