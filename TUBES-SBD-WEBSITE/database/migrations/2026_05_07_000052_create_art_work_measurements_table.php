<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('art_work_measurements', function (Blueprint $table) {
            $table->increments('art_work_measurement_id');
            $table->unsignedInteger('art_work_id');
            $table->unsignedInteger('measurement_element_id')->nullable();
            $table->unsignedInteger('measurement_type_id')->nullable();
            $table->unsignedInteger('measurement_unit_id')->nullable();
            $table->text('element_description')->nullable();
            $table->decimal('value', 12, 4);
            $table->softDeletes();
            $table->timestamps(); // FINAL SCHEMA: created_at & updated_at

            $table->foreign('art_work_id')->references('art_work_id')->on('art_works');
            $table->foreign('measurement_element_id')->references('measurement_element_id')->on('measurement_elements');
            $table->foreign('measurement_type_id')->references('measurement_type_id')->on('measurement_types');
            $table->foreign('measurement_unit_id')->references('measurement_unit_id')->on('measurement_units');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('art_work_measurements');
    }
};
