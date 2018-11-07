<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 建立table
        Schema::create('condition_qty', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->integer('setting_id')->default(0)->comment('設定檔編號');
            $table->decimal('value', 8, 2)->default(0)->comment('條件值');
        });
        DB::statement("ALTER TABLE `condition_qty` comment '優惠券條件設定檔(滿X件/數量)'");

        Schema::create('condition_amount', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->integer('setting_id')->default(0)->comment('設定檔編號');
            $table->decimal('value', 8, 2)->default(0)->comment('條件值');
        });
        DB::statement("ALTER TABLE `condition_amount` comment '優惠券條件設定檔(滿X元)'");

        Schema::create('offer_amount', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->integer('setting_id')->default(0)->comment('設定檔編號');
            $table->decimal('value', 8, 2)->default(0)->comment('優惠值');
            $table->decimal('max_value', 8, 2)->default(0)->comment('獎勵值上限');
        });
        DB::statement("ALTER TABLE `offer_amount` comment '優惠券優惠設定檔(抵Y元)'");

        Schema::create('offer_discount', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->integer('setting_id')->default(0)->comment('設定檔編號');
            $table->decimal('value', 8, 2)->default(0)->comment('優惠值');
            $table->decimal('max_value', 8, 2)->default(0)->comment('折扣最大折抵金額');
        });
        DB::statement("ALTER TABLE `offer_discount` comment '優惠券優惠設定檔(打Y折)'");

        Schema::create('offer_qty', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->integer('setting_id')->default(0)->comment('設定檔編號');
            $table->decimal('value', 8, 2)->default(0)->comment('優惠值');
            $table->decimal('max_value', 8, 2)->default(0)->comment('獎勵值上限');
        });
        DB::statement("ALTER TABLE `offer_qty` comment '優惠券優惠設定檔(Y件免費)'");

        Schema::create('campaign_setting', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->integer('types')->default(1)->comment('設定類型 (1:促銷活動;2:優惠券)');
            $table->integer('event_id')->default(0)->comment('觸發事件編號');
            $table->integer('company_id')->default(0)->comment('品牌編號');
            $table->integer('store_id')->default(0)->comment('店家編號');
            $table->string('title', 50)->nullable()->comment('優惠活動名稱');
            $table->string('description', 60)->nullable()->comment('優惠說明');
            $table->integer('kind')->default(0)->comment('券別 (1:期間; 2: 領後 n日; 3:指定星期 n; ');
            $table->string('kind_value', 50)->nullable()->comment('領後有效天數 (例: kind=2, kind_value="14")');
            $table->string('week_days', 50)->nullable()->comment('指定星期(例: "1,3,5")');
            $table->integer('max_qty')->default(0)->comment('限量 (0: 不限)');
            $table->tinyInteger('sn_gen')->default(1)->comment('序號產生類別 (1: 先產; 2: 後產)');
            $table->integer('sn_length')->default(32)->comment('序號長度');
            $table->tinyInteger('sn_gen_status')->default(0)->comment('序號產生完成狀態 (0: 未產完; 1:已產完)');
            $table->integer('used_count')->default(0)->comment('已使用數量');
            $table->integer('user_use_count')->default(1)->comment('使用者兌換次數');
            $table->string('condition_table', 50)->nullable()->comment('條件設定資料表名稱');
            $table->string('offer_table', 50)->nullable()->comment('優惠設定資料表名稱');
            $table->integer('offer_max_value')->default(0)->comment('活動整體獎勵金額上限');
            $table->integer('product_delivery')->default(0)->comment('取餐方式 (0:不限;1:外送;2:外帶;3:內用');
            $table->tinyInteger('repeat')->default(0)->comment('獎勵累計0.不累計;1.累計;(PS.多獎勵不做累計)');
            $table->tinyInteger('plural')->default(0)->comment('活動累加0.不累加;1.累加;(預留:多個活動滿足是不是都給獎勵)');
            $table->tinyInteger('locks')->default(0)->comment('鎖定(無法修改條件或優惠內容)(0: 未鎖; 1: 鎖定');
            $table->tinyInteger('online')->default(1)->comment('0.離線活動;1.線上活動;(預留)');
            $table->text('remark')->nullable()->comment('備註');
            $table->tinyInteger('status')->default(1)->comment('狀態 (1: 啟用; 2:關閉)');
            $table->timestamp('start_at')->nullable()->comment('起始日期');
            $table->timestamp('end_at')->nullable()->comment('結束日期');
            $table->timestamp('created_at')->nullable()->comment('建立時間');
            $table->timestamp('updated_at')->nullable()->comment('更新時間');
            $table->timestamp('deleted_at')->nullable()->comment('刪除時間');
        });
        DB::statement("ALTER TABLE `campaign_setting` comment '優惠券設定檔'");

        Schema::create('coupon', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->integer('member_detail_id')->default(0)->comment('會員編號 (0: 非會員)');
            $table->integer('company_id')->default(0)->comment('品牌編號');
            $table->integer('store_id')->default(0)->comment('店家編號');
            $table->integer('setting_id')->default(0)->comment('優惠券設定編號');
            $table->string('sn', 50)->comment('序號');
            $table->integer('counts')->default(1)->comment('可使用數');
            $table->integer('used_count')->default(0)->comment('兌換次數');
            $table->tinyInteger('locks')->default(0)->comment('鎖定(0: 未鎖; 1: 鎖定');
            $table->tinyInteger('status')->default(1)->comment('狀態 (1: 啟用; 0: 關閉)');
//            $table->integer('kind')->default(0)->comment('券別 (1:期間; 2: 領後 n日; 3:指定星期 n; ');
//            $table->string('kind_value', 50)->nullable()->comment('券別值 (例: kind=2, kind_value="+14"; kind=3, kind_value="1,3,5"; )');
            $table->string('week_days', 50)->nullable()->comment('指定星期 (例: "1,3,5")');
            $table->timestamp('start_at')->nullable()->comment('起始日期');
            $table->timestamp('end_at')->nullable()->comment('結束日期');
            $table->timestamp('created_at')->nullable()->comment('建立時間');
            $table->timestamp('updated_at')->nullable()->comment('更新時間');

            $table->unique('sn');
        });
        DB::statement("ALTER TABLE `coupon` comment '優惠券資料檔'");

        Schema::create('coupon_log', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->integer('member_detail_id')->comment('正式會員編號');
            $table->string('sn', 50)->nullable()->comment('序號');
            $table->integer('setting_id')->default(0)->comment('優惠券設定編號');
            $table->integer('order_id')->default(0)->comment('訂單編號');
            $table->decimal('total_price', 8, 2)->nullable()->comment('總金額');
            $table->decimal('check_out_price', 8, 2)->nullable()->comment('結帳金額');
            $table->decimal('deduct_price', 8, 2)->nullable()->comment('減免金額');
            $table->timestamp('created_at')->nullable()->comment('建立時間');
            $table->timestamp('updated_at')->nullable()->comment('更新時間');
        });
        DB::statement("ALTER TABLE `coupon_log` comment '優惠券紀錄檔'");

        Schema::create('coupon_schedule_log', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->integer('member_detail_id')->default(0)->comment('正式會員編號');
            $table->integer('setting_id')->default(0)->comment('優惠券設定編號');
            $table->integer('coupon_id')->default(0)->comment('優惠券編號');
            $table->tinyInteger('exec_status')->default(0)->comment('執行狀態 (0: 未執行; 1: 已執行)');
            $table->tinyInteger('status')->default(0)->comment('執行結果 (0: 失敗; 1: 成功)');
            $table->string('message', 255)->nullable()->comment('訊息');
            $table->timestamp('created_at')->nullable()->comment('建立時間');
            $table->timestamp('updated_at')->nullable()->comment('更新時間');
        });
        DB::statement("ALTER TABLE `coupon_schedule_log` comment '優惠券發放排程紀錄檔'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 刪除table
        Schema::dropIfExists('condition_qty');
        Schema::dropIfExists('condition_amount');
        Schema::dropIfExists('offer_amount');
        Schema::dropIfExists('offer_discount');
        Schema::dropIfExists('offer_qty');
        Schema::dropIfExists('campaign_setting');
        Schema::dropIfExists('coupon');
        Schema::dropIfExists('coupon_log');
        Schema::dropIfExists('coupon_schedule_log');
    }
}
