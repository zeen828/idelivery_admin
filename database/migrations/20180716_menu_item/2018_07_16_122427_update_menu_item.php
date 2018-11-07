<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMenuItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu_item', function (Blueprint $table)
        {
            $table->tinyInteger('hidden')->default(0)->comment('顯示狀態(0:顯示,1:隱藏)')->after('spec_relation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu_item', function (Blueprint $table)
        {
            $table->dropColumn('hidden');// 刪除欄位
        });
    }
}
