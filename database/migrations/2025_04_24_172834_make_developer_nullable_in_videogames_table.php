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
            $table->string('developer')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videogames', function (Blueprint $table) {
            // Revert only if the column definitely existed and was not nullable before
            // $table->string('developer')->nullable(false)->change();
        });
    }
};
