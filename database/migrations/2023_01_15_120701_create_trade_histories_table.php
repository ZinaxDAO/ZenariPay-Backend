<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trade_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('trade_id');
            $table->string('trade_status');
            $table->string('trade_amount');
            $table->string('transaction_id');
            $table->string('trade_currency');
            $table->unsignedBigInteger('payment_id')->comment("Payment method ID");
            $table->string('trade_type');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('trade_id')->references('id')->on('trades');
            $table->foreign('payment_id')->references('id')->on('paymentmethods');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trade_histories');
    }
}
