<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('art_work_images', function (Blueprint $table) {
            $table->increments('image_id');
            $table->unsignedInteger('art_work_id');
            $table->text('image_url');
            $table->boolean('is_primary')->default(false);
            $table->integer('display_order')->default(1);
            $table->softDeletes();

            $table->foreign('art_work_id')->references('art_work_id')->on('art_works');
        });

        DB::statement('CREATE UNIQUE INDEX art_work_images_primary_unique ON art_work_images ((CASE WHEN is_primary = 1 THEN art_work_id ELSE NULL END))');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX art_work_images_primary_unique ON art_work_images');
        Schema::dropIfExists('art_work_images');
    }
};
