<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfferPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_points', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('setting_id')->default(0)->comment('活動/優惠設定編號');
            $table->integer('point_type_id')->default(1)->comment('點數類型編號');
            $table->integer('value')->default(0)->comment('點數');
            $table->timestamp('expired_at')->nullable()->comment('逾期時間');
            $table->integer('max_value')->default(0)->comment('最高點數');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offer_points');
    }
}
