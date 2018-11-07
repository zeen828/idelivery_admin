<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCouponLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 更新table
        Schema::table('coupon_log', function (Blueprint $table)
        {
            $table->tinyInteger('status')->default(1)->comment('狀態 (1:正常; 0: 還原/取消)')->after('deduct_price');
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
        Schema::table('coupon_log', function (Blueprint $table) {
            $table->dropColumn('status');// 刪除欄位
        });
    }
}
