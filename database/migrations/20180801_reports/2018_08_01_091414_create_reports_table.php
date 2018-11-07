<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     * php artisan migrate --path="database/migrations/20180801_reports"
     * @return void
     */
    public function up()
    {
        Schema::create('report_order', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->default(0)->comment('品牌編號');
            $table->integer('store_id')->default(0)->comment('店家編號');
            $table->integer('years')->comment('年');
            $table->integer('months')->comment('月');
            $table->integer('days')->comment('日');
            $table->integer('hours')->comment('小時');
            $table->timestamp('start_hour')->nullable()->comment('起始時間(24時:分:01)');
            $table->timestamp('end_hour')->nullable()->comment('結束時間(24時:分:00)');
            $table->integer('product_delivery')->default(0)->comment('訂單類型(1:外送;2:外帶;3:內用)');
            $table->integer('payment')->default(1)->comment('付款方式(1:現金;2:信用卡)');
            $table->integer('member_class')->default(0)->comment('會員分級');//x
            $table->integer('weather')->default(0)->comment('天氣狀況');//x
            $table->integer('order_count')->default(0)->comment('訂單數');
            $table->integer('src_amount')->default(0)->comment('原價');
            $table->integer('total_qty')->default(0)->comment('總數量');
            $table->integer('discount_amount')->default(0)->comment('系統折扣金額');
            $table->integer('coupon_discount')->default(0)->comment('活動/優惠券折扣金額');
            $table->integer('custom_discount')->default(0)->comment('手動折扣金額');
            $table->integer('amount')->default(0)->comment('應付金額');
            $table->timestamps();

            $table->index(['company_id', 'years', 'months', 'days', 'hours', 'product_delivery'], 'company_product_delivery_idx');
            $table->index(['company_id', 'years', 'months', 'days', 'hours', 'payment'], 'company_payment_idx');
            $table->index(['company_id', 'years', 'months', 'days', 'hours', 'member_class'], 'company_member_class_idx');
            $table->index(['company_id', 'years', 'months', 'days', 'hours', 'weather'], 'company_weather_idx');

            $table->index(['store_id', 'years', 'months', 'days', 'hours', 'product_delivery'], 'store_product_delivery_idx');
            $table->index(['store_id', 'years', 'months', 'days', 'hours', 'payment'], 'store_payment_idx');
            $table->index(['store_id', 'years', 'months', 'days', 'hours', 'member_class'], 'store_member_class_idx');
            $table->index(['store_id', 'years', 'months', 'days', 'hours', 'weather'], 'store_weather_idx');
        });
        DB::statement("ALTER TABLE `report_order` comment '店家訂單報表統計資料表'");

        Schema::create('report_order_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->default(0)->comment('品牌編號');
            $table->integer('store_id')->default(0)->comment('店家編號');
            $table->integer('years')->comment('年');
            $table->integer('months')->comment('月');
            $table->integer('days')->comment('日');
            $table->integer('hours')->comment('小時');
            $table->timestamp('start_hour')->nullable()->comment('起始時間(24時:分:01)');
            $table->timestamp('end_hour')->nullable()->comment('結束時間(24時:分:00)');
            $table->integer('group_id')->default(0)->comment('餐點分類編號');
            $table->string('group_name', 50)->nullable()->comment('餐點分類名稱');
            $table->integer('item_id')->default(0)->comment('品項編號');
            $table->string('item_name', 50)->nullable()->comment('品項名稱');
            $table->integer('item_price')->default(0)->comment('原價');
            $table->integer('qty')->default(0)->comment('數量');
            $table->integer('discount_amount')->default(0)->comment('扣抵金額');
            $table->integer('sub_price')->default(0)->comment('金額(小計)');
            $table->timestamps();

            $table->index(['company_id', 'years', 'months', 'days', 'hours', 'group_id'], 'company_group_id_idx');
            $table->index(['company_id', 'years', 'months', 'days', 'hours', 'item_id'], 'company_item_id_idx');

            $table->index(['store_id', 'years', 'months', 'days', 'hours', 'group_id'], 'store_group_id_idx');
            $table->index(['store_id', 'years', 'months', 'days', 'hours', 'item_id'], 'store_item_id_idx');

        });
        DB::statement("ALTER TABLE `report_order_detail` comment '店家訂單明細報表統計資料表'");

        Schema::create('report_campaign', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->default(0)->comment('品牌編號');
            $table->integer('store_id')->default(0)->comment('店家編號');
            $table->integer('years')->comment('年');
            $table->integer('months')->comment('月');
            $table->integer('days')->comment('日');
            $table->integer('hours')->comment('小時');
            $table->timestamp('start_hour')->nullable()->comment('起始時間(24時:分:01)');
            $table->timestamp('end_hour')->nullable()->comment('結束時間(24時:分:00)');
            $table->integer('types')->default(0)->comment('設定類型(1:活動;2:優惠券)');
            $table->integer('setting_id')->default(0)->comment('設定編號');
            $table->string('setting_title', 50)->nullable()->comment('活動/優惠名稱');
            $table->integer('used_count')->default(0)->comment('已使用次數');
            $table->integer('total_price')->default(0)->comment('原價');
            $table->integer('check_out_price')->default(0)->comment('結帳金額');
            $table->integer('deduct_price')->default(0)->comment('減免金額');
            $table->timestamps();

            $table->index(['company_id', 'years', 'months', 'days', 'hours', 'types'], 'company_types_idx');
            $table->index(['company_id', 'years', 'months', 'days', 'hours', 'setting_id'], 'company_setting_id_idx');

            $table->index(['store_id', 'years', 'months', 'days', 'hours', 'types'], 'store_types_idx');
            $table->index(['store_id', 'years', 'months', 'days', 'hours', 'setting_id'], 'store_setting_id_idx');

        });
        DB::statement("ALTER TABLE `report_campaign` comment '店家活動/優惠統計資料表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_order');
        Schema::dropIfExists('report_order_detail');
        Schema::dropIfExists('report_campaign');
    }
}
