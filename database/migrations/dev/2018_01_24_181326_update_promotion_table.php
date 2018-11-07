<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePromotionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 更新table
        Schema::table('promotion', function (Blueprint $table) {
            $table->string('expired', 50)->nullable()->comment('點數有效期限(加1年=P1Y)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu_item', function (Blueprint $table) {
            $table->dropColumn('expired');// 刪除欄位
        });
    }
}
