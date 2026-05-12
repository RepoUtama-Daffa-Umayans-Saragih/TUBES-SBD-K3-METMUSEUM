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
            $table->timestamps(); // FINAL SCHEMA: created_at & updated_at

            $table->foreign('art_work_id')->references('art_work_id')->on('art_works');
        });

        // Perbaikan untuk MariaDB 11:
        // 1. Buat Virtual Column yang hanya berisi ID jika is_primary = 1
        // 2. Berikan Unique Index pada kolom virtual tersebut
        DB::statement("ALTER TABLE art_work_images ADD COLUMN primary_check INT AS (CASE WHEN is_primary = 1 THEN art_work_id ELSE NULL END) VIRTUAL");
        DB::statement("CREATE UNIQUE INDEX art_work_images_primary_unique ON art_work_images (primary_check)");
    }

    public function down(): void
    {
        // Drop index dulu, baru drop tabel
        Schema::table('art_work_images', function (Blueprint $table) {
            $table->dropIndex('art_work_images_primary_unique');
        });
        Schema::dropIfExists('art_work_images');
    }
};
