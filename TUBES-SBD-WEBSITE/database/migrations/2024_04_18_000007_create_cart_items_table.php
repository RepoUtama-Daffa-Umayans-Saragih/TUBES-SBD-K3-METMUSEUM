<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->increments('cart_item_id');
            $table->unsignedInteger('cart_group_id');
            $table->unsignedInteger('ticket_availability_id');
            $table->unsignedInteger('quantity');

            $table->foreign('cart_group_id')->references('cart_group_id')->on('cart_groups')->onDelete('cascade');
            $table->foreign('ticket_availability_id')->references('ticket_availability_id')->on('ticket_availability');
            $table->unique(['cart_group_id', 'ticket_availability_id'], 'uq_cart_items_group_ticket_availability');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
