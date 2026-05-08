<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('art_works', function (Blueprint $table) {
            $table->engine    = 'InnoDB';
            $table->charset   = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->increments('art_work_id');
            $table->string('object_number');
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('gallery_number', 100)->nullable();
            $table->integer('year_start')->nullable();
            $table->integer('year_end')->nullable();

            $table->unsignedInteger('department_id');
            $table->unsignedInteger('type_id');
            $table->unsignedInteger('geo_id');
            $table->unsignedInteger('location_id');

            $table->foreign('department_id')->references('department_id')->on('departments');
            $table->foreign('type_id')->references('object_type_id')->on('object_types');
            $table->foreign('geo_id')->references('geo_location_id')->on('geo_locations');
            $table->foreign('location_id')->references('location_id')->on('locations');

            $table->unique('object_number', 'uq_art_works_object_number');
            $table->unique('slug', 'uq_art_works_slug');
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE art_works ADD CONSTRAINT chk_art_works_year_range CHECK (year_start <= year_end)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('art_works');
    }
};
