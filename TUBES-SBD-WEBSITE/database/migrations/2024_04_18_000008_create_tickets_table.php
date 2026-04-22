<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('ticket_availability_id')->constrained('ticket_availability')->restrictOnDelete();
            $table->string('qr_code')->unique();
            $table->enum('status', ['valid', 'used', 'cancelled'])->default('valid');
            $table->dateTime('used_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
