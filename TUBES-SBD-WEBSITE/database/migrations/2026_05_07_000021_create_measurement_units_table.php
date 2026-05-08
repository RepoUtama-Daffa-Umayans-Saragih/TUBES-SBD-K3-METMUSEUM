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
        Schema::create('measurement_units', function (Blueprint $table) {
            $table->increments('measurement_unit_id');
            $table->string('measurement_unit_name', 100)->unique();
            $table->string('measurement_unit_symbol', 20)->unique();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('measurement_units');
    }
};
