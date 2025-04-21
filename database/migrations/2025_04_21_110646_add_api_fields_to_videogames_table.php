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
        Schema::table('videogames', function (Blueprint $table) {

            // Here we add the new fields to the videogames table
            $table->integer('api_id')->unique()->nullable()->after('id'); // We add the RAWG ID
            $table->string('slug')->unique()->nullable()->after('api_id'); // We add the slug

            // We rename the existing columns to match RAWG API names
            $table->renameColumn('title', 'name');
            $table->renameColumn('release_date', 'released');
            $table->renameColumn('cover_image_url', 'background_image');

            // We add indexes to the new columns for better performance
            $table->index('developer');
            $table->index('publisher');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videogames', function (Blueprint $table) {

            // Drop the columns if the migration is rolled back
            $table->dropIndex(['developer']); // Use array or string name given during creation
            $table->dropIndex(['publisher']);

            $table->dropUnique(['slug']); // Drop unique constraint first
            $table->dropColumn('slug');
            $table->dropUnique(['api_id']);
            $table->dropColumn('api_id');

            $table->renameColumn('name', 'title');
            $table->renameColumn('released', 'release_date');
            $table->renameColumn('background_image', 'cover_image_url');
        });
    }
};
