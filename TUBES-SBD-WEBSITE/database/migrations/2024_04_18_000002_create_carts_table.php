<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->increments('cart_id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('guest_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->dateTime('expires_at');

            $table->foreign('user_id')->references('user_id')->on('users');
            $table->foreign('guest_id')->references('guest_id')->on('guests');
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE carts ADD CONSTRAINT chk_carts_xor_owner CHECK ((user_id IS NULL) <> (guest_id IS NULL))');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
