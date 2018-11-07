<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCampaignLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaign_log', function (Blueprint $table)
        {
            $table->dropColumn('trigger');// 刪除欄位
            $table->dropColumn('campaign_setting_id');// 刪除欄位

            $table->integer('member_detail_id')->default(0)->comment('正式會員編號')->after('id');
            $table->integer('setting_id')->default(0)->comment('優惠券設定編號')->after('sn');
            $table->string('event', 50)->comment('觸發事件')->after('order_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaign_log', function (Blueprint $table)
        {
            $table->dropColumn('member_detail_id');// 刪除欄位
            $table->dropColumn('setting_id');// 刪除欄位
            $table->dropColumn('event');// 刪除欄位

            $table->integer('campaign_setting_id')->comment('活動設定編號')->unsigned()->after('sn');
            $table->string('trigger', 50)->comment('觸發點')->after('order_id');
        });
    }
}
