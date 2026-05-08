<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_schedules', function (Blueprint $table) {
            $table->increments('visit_schedule_id');
            $table->unsignedInteger('location_id');
            $table->date('visit_date');
            $table->integer('capacity_limit');

            $table->foreign('location_id')->references('location_id')->on('locations');
            $table->unique(['location_id', 'visit_date'], 'uq_visit_schedules_location_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_schedules');
    }
};
