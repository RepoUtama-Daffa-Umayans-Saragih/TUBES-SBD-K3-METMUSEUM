<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('order_id');
            $table->string('order_code', 50)->unique();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('guest_id')->nullable();
            $table->dateTime('order_date');
            $table->dateTime('expired_at')->nullable();
            $table->decimal('total_amount', 15, 2);

            $table->foreign('user_id')->references('user_id')->on('users');
            $table->foreign('guest_id')->references('guest_id')->on('guests');
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE orders ADD CONSTRAINT chk_orders_xor_owner CHECK ((user_id IS NULL) <> (guest_id IS NULL))');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
