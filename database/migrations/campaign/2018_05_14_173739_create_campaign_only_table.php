<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignOnlyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_only', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('campaign_setting_id')->default(0)->comment('活動設定編號')->unsigned();
            $table->integer('type')->default(0)->comment('限定類型1:會員;2:品牌;3:店家');
            $table->integer('only_id')->default(0)->comment('對照類型的編號');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `campaign_only` comment '活動限定使用對象檔'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaign_only');
    }
}
