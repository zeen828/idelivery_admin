<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCouponLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coupon_log', function (Blueprint $table)
        {
            $table->integer('member_id')->default(0)->comment('暫定會員編號')->after('id');
            $table->integer('company_id')->default(0)->comment('品牌編號')->after('member_detail_id');
            $table->integer('store_id')->default(0)->comment('店家編號')->after('company_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coupon_log', function (Blueprint $table)
        {
            $table->dropColumn('member_id');// 刪除欄位
            $table->dropColumn('company_id');// 刪除欄位
            $table->dropColumn('store_id');// 刪除欄位
        });
    }
}
