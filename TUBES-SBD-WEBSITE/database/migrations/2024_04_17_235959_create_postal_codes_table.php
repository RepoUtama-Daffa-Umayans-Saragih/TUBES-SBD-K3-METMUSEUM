<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postal_codes', function (Blueprint $table) {
            $table->increments('postal_code_id');
            $table->string('postal_code', 20);
            $table->string('city', 100);
            $table->string('state', 100);
            $table->string('country', 100);

            $table->unique(['postal_code', 'city', 'state', 'country'], 'uq_postal_codes_full');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postal_codes');
    }
};
