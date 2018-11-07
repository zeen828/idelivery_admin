<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateStoreSetting extends Migration
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
            $table->tinyInteger('sw_app')->default(1)->comment('APP顯示開關(0:關,1:開)')->after('promotion_discount');
            $table->tinyInteger('sw_pos')->default(1)->comment('POS機顯示開關(0:關,1:開)')->after('sw_app');
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
            $table->dropColumn('sw_app');// 刪除欄位
            $table->dropColumn('sw_pos');// 刪除欄位
        });
    }
}
