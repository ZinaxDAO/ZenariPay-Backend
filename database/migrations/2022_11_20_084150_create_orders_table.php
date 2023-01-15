<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('status', ['failed','pending','completed','processing','cancelled'])->default('pending');
            $table->string("reference");
            $table->string("coin");
            $table->string("cryptoAmount");
            $table->string("currency");
            $table->string("fiatAmount");
            $table->string("feeInCrypto");
            $table->string("customerName")->nullable();
            $table->string("customerEmail")->nullable();
            $table->string("address")->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}