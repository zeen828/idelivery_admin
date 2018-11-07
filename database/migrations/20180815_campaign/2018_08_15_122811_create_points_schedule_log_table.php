<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePointsScheduleLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('points_schedule_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('service_id')->default(0)->comment('服務編號');
            $table->integer('company_id')->default(0)->comment('品牌編號');
            $table->integer('store_id')->default(0)->comment('店家編號');
            $table->integer('member_detail_id')->default(0)->comment('正式會員編號');
            $table->integer('member_id')->default(0)->comment('會員編號');
            $table->integer('point_type_id')->default(0)->comment('點數類型');
            $table->integer('points')->default(0)->comment('點數(1:集點;2:紅利;3:團體集點)');
            $table->timestamp('expired_at')->nullable()->comment('逾期時間');
            $table->string('description')->nullable()->comment('說明');
            $table->integer('status')->default(0)->comment('贈點狀態(0:未執行;1:已成功執行;2:執行失敗)');
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
        Schema::dropIfExists('points_schedule_log');
    }
}
