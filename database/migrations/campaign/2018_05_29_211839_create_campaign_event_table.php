<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_event', function (Blueprint $table) {
            $table->increments('id');
            $table->string('keyword', 20)->comment('事件鍵值');
            $table->tinyInteger('status')->default(1)->comment('啟用狀態;1=啟用;2=關閉;');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE `campaign_event` comment '活動事件資料表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaign_event');
    }
}
