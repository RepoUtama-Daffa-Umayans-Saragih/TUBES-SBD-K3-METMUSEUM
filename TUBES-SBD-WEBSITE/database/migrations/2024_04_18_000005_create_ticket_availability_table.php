<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_type_id')->constrained('ticket_types')->restrictOnDelete();
            $table->foreignId('visit_schedule_id')->constrained('visit_schedules')->restrictOnDelete();

            $table->unique(['ticket_type_id', 'visit_schedule_id'], 'uq_ticket_availability_type_schedule');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_availability');
    }
};
