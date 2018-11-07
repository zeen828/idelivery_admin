<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignOfferCoupon extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_coupon', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('coupon_setting_id')->default(0)->comment('優惠券設定編號');
            $table->integer('setting_id')->default(0)->comment('活動設定編號');
            $table->timestamps();
            $table->index('setting_id')->comment('活動設定的編號');
        });

        DB::statement("ALTER TABLE `offer_coupon` comment '活動給予設定檔-優惠券'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offer_coupon');
    }
}
