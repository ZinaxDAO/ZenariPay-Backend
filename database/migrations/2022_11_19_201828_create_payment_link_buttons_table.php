<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentLinkButtonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_link_buttons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id');
            $table->string('link_title');
            $table->string('slug');
            $table->string('link_description', 5000);
            $table->enum('link_type', ['donation', 'standard'])->default('standard');
            $table->enum('phone_number', [0,1])->comment('0: not required, 1: required')->default(0);
            $table->enum('shipping_address', [0,1])->comment('0: not required, 1: required')->default(0);
            $table->string('redirect_website')->nullable();
            $table->longText('payment_success_msg')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_link_buttons');
    }
}
