<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order', function (Blueprint $table)
        {
            $table->integer('marketing_type')->default(0)->comment('促銷類型(1:活動,2:優惠券)')->after('call_no');
            $table->string('marketing_sn', 50)->nullable()->comment('促銷編號')->after('marketing_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order', function (Blueprint $table)
        {
            $table->dropColumn('marketing_type');// 刪除欄位
            $table->dropColumn('marketing_sn');// 刪除欄位
        });
    }
}
