<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('product_name');
            $table->json('product_currency');
            $table->json('product_price');
            $table->json('product_social')->nullable();
            $table->longText('product_description')->nullable();
            $table->string('product_image')->nullable();
            $table->enum('success_redirect', [0, 1])->default(0)->comment('0 : No show success page, 1: Redirection to URL in product_website');
            $table->timestampsTz();
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
        Schema::dropIfExists('products');
    }
}
