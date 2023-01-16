<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompliancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compliances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->string("nationality")->nullable();
            $table->string("idType")->nullable();
            $table->string("id_front")->nullable();
            $table->string("id_back")->nullable();
            $table->string("selfie_image")->nullable();
            $table->string("zipcode")->nullable();
            $table->string("utilityType")->nullable();
            $table->string("utility_image")->nullable();
            $table->string("director_1")->nullable();
            $table->string("director_2")->nullable();
            $table->string("director_3")->nullable();
            $table->string("d_image_1")->nullable();
            $table->string("d_image_2")->nullable();
            $table->string("d_image_3")->nullable();
            $table->string("share_holder_1")->nullable();
            $table->string("share_holder_2")->nullable();
            $table->string("share_holder_3")->nullable();
            $table->string("d_share_holder_1")->nullable();
            $table->string("d_share_holder_2")->nullable();
            $table->string("d_share_holder_3")->nullable();
            $table->string("incoporation")->nullable();
            $table->string("address_proof")->nullable();
            $table->string("business_license")->nullable();
            $table->string("tax_id")->nullable();
            $table->string("aml_policy")->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compliances');
    }
}
