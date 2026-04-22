<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('carts')->cascadeOnDelete();
            $table->foreignId('ticket_availability_id')->constrained('ticket_availability')->restrictOnDelete();
            $table->unsignedInteger('quantity');

            $table->unique(['cart_id', 'ticket_availability_id'], 'uq_cart_items_cart_ticket_availability');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
