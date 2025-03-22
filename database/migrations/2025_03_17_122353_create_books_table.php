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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author');
            $table->string('isbn');
            $table->string('language')->nullable();
            $table->string('pages')->nullable();
            $table->string('genre')->nullable();
            $table->string('cover_image')->nullable();
            $table->text('description')->nullable();
            $table->string('publisher')->nullable();
            $table->integer('publication_year_this_publisher')->nullable();
            $table->integer('publication_year_original')->nullable();
            $table->integer('valoration')->nullable();
            $table->string('comments')->nullable();
            $table->string('format')->nullable();
            $table->string('tags')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
