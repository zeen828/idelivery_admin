<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateStoreAddSwitch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 更新table
        Schema::table('store', function (Blueprint $table)
        {
            //獎勵-獲得
            $table->tinyInteger('sw_reward')->default(0)->comment('獎勵開關(0:關,1:開)')->after('promotion_discount');
            $table->tinyInteger('sw_r_point')->default(0)->comment('獎勵集點開關(0:關,1:開)')->after('sw_reward');
            $table->tinyInteger('sw_r_campaign')->default(0)->comment('獎勵活動開關(0:關,1:開)')->after('sw_r_point');
            //使用-消耗
            $table->tinyInteger('sw_use')->default(0)->comment('使用開關(0:關,1:開)')->after('sw_r_campaign');
            $table->tinyInteger('sw_u_exchange')->default(0)->comment('使用兌換開關(0:關,1:開)')->after('sw_use');
            $table->tinyInteger('sw_u_campaign')->default(0)->comment('使用活動開關(0:關,1:開)')->after('sw_u_exchange');
            $table->tinyInteger('sw_u_coupon')->default(0)->comment('使用優惠卷開關(0:關,1:開)')->after('sw_u_campaign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('store', function (Blueprint $table)
        {
            $table->dropColumn('sw_reward');// 刪除欄位
            $table->dropColumn('sw_r_point');// 刪除欄位
            $table->dropColumn('sw_r_campaign');// 刪除欄位
            $table->dropColumn('sw_use');// 刪除欄位
            $table->dropColumn('sw_u_exchange');// 刪除欄位
            $table->dropColumn('sw_u_campaign');// 刪除欄位
            $table->dropColumn('sw_u_coupon');// 刪除欄位
        });
    }
}
