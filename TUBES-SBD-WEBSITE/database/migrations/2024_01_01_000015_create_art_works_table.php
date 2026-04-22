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
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->string('object_number');
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('gallery_number')->nullable();
            $table->integer('year_start')->nullable();
            $table->integer('year_end')->nullable();

            $table->foreignId('department_id')->constrained('departments')->restrictOnDelete();
            $table->foreignId('type_id')->constrained('object_types')->restrictOnDelete();
            $table->foreignId('geo_id')->constrained('geo_locations')->restrictOnDelete();
            $table->foreignId('location_id')->constrained('locations')->restrictOnDelete();

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
