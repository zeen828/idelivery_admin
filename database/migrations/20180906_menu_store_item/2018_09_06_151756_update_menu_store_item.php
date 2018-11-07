<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMenuStoreItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu_store_item', function (Blueprint $table)
        {
            $table->unsignedInteger('points')->default(0)->comment('獲得點數')->after('item_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu_store_item', function (Blueprint $table)
        {
            $table->dropColumn('points');// 刪除欄位
        });
    }
}
