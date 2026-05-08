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
        Schema::create('art_work_artists', function (Blueprint $table) {
            $table->engine    = 'InnoDB';
            $table->charset   = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->unsignedInteger('art_work_id');
            $table->unsignedInteger('artist_id');

            $table->foreign('art_work_id')->references('art_work_id')->on('art_works');
            $table->foreign('artist_id')->references('artist_id')->on('artists');

            $table->unique(['art_work_id', 'artist_id'], 'uq_art_work_artists_art_work_artist');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('art_work_artists');
    }
};
