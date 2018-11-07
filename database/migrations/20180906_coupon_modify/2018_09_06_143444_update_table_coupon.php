<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableCoupon extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 更新table
        Schema::table('coupon', function (Blueprint $table)
        {
            $table->integer('product_delivery')->default(0)->comment('取餐方式 (0:不限;1:外送;2:外帶;3:內用')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 更新table
        Schema::table('coupon', function (Blueprint $table) {
            $table->dropColumn('product_delivery');// 刪除欄位
        });
    }
}
