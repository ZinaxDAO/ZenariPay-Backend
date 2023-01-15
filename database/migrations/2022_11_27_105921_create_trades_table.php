<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('min_amount');
            $table->string('max_amount');
            $table->string('trade_currency');
            $table->enum('priceType', ['fixed', 'float'])->default('float');
            $table->string('totalAmount');
            $table->string('paymentMethod');
            $table->string('tradeType');
            $table->string('assetName');
            $table->string('fiatName');
            $table->decimal('marginPrice');
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
        Schema::dropIfExists('trades');
    }
}
