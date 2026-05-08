<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('payment_id');
            $table->unsignedInteger('order_id');
            $table->string('payment_method', 100);

            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->enum('payment_status', ['Pending', 'Paid', 'Failed', 'Refunded']);
            $table->dateTime('paid_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
