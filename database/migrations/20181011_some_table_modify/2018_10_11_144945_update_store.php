<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateStore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store', function (Blueprint $table)
        {
            $table->tinyInteger('sw_moneypos')->default(0)->comment('使用money_pos開關(0:關,1:開)')->after('sw_pos');
            $table->tinyInteger('sw_r_point_member')->default(0)->comment('獎勵點數[暫定會員]可否使用開關(0:關,1:開)')->after('sw_r_campaign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store', function (Blueprint $table)
        {
            $table->dropColumn('sw_moneypos');// 刪除欄位
            $table->dropColumn('sw_r_point_member');// 刪除欄位
        });
    }
}
