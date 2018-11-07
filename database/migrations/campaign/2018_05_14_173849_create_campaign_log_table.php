<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->comment('訂單編號');
            $table->string('sn', 50)->comment('序號');
            $table->string('trigger', 50)->comment('觸發點');
            $table->integer('campaign_setting_id')->comment('活動設定編號')->unsigned();
            $table->decimal('total_price', 8, 2)->default(0)->comment('總金額');
            $table->decimal('check_out_price', 8, 2)->default(0)->comment('結帳金額');
            $table->decimal('deduct_price', 8, 2)->default(0)->comment('減免金額');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `campaign_log` comment '活動紀錄檔'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaign_log');
    }
}
